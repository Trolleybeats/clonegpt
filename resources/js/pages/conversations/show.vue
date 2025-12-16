<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import { useStream } from '@laravel/stream-vue';
import hljs from 'highlight.js';
import 'highlight.js/styles/github-dark.css';
import { Loader } from 'lucide-vue-next';
import MarkdownIt from 'markdown-it';
import { computed, nextTick, onMounted, ref } from 'vue';

const md = new MarkdownIt({
    highlight: function (str, lang) {
        if (lang && hljs.getLanguage(lang)) {
            try {
                return hljs.highlight(str, { language: lang }).value;
            } catch (__) {}
        }
        return ''; // use external default escaping
    },
});
// Utilisation : md.render(text)

// Stream state
const message = ref('');
const temperature = ref(1.0);
const reasoningEffort = ref<'low' | 'medium' | 'high' | null>(null);

/**
 * Extrait le contenu principal (sans le reasoning)
 */
const streamedContent = computed(() => {
    if (!data.value) return '';
    // Enlever les blocs [REASONING]...[/REASONING]
    return data.value
        .replace(/\[REASONING\][\s\S]*?\[\/REASONING\]/g, '')
        .trim();
});

/**
 * Extrait le reasoning des marqueurs
 */
const streamedReasoning = computed(() => {
    if (!data.value) return '';
    const matches = data.value.match(/\[REASONING\]([\s\S]*?)\[\/REASONING\]/g);
    if (!matches) return '';
    return matches
        .map((m) =>
            m.replace(/\[REASONING\]/g, '').replace(/\[\/REASONING\]/g, ''),
        )
        .join('');
});

const props = defineProps({
    conversation: Object,
    models: Array,
    selectedModel: String,
    response: String,
    error: String,
});

const model = ref(props.selectedModel || 'openai/gpt-4o-mini');
const thinking = ref(false);

// useStream hook - concatène automatiquement dans `data`
const lastSent = ref('');
const { data, isFetching, isStreaming, send, cancel } = useStream(
    '/ask-stream',
    {
        onFinish: async () => {
            try {
                message.value = '';
                window.location.reload();
            } catch (e) {
                console.error('Post-stream error:', e);
            }
        },
        onError: (err: Error) => {
            console.error('Erreur streaming:', err);
        },
    },
);

// Submit handler - lance le streaming
const submit = () => {
    if (!message.value.trim() || isStreaming.value) return;

    lastSent.value = message.value;
    thinking.value = true;
    send({
        message: message.value,
        model: model.value,
        temperature: temperature.value,
        reasoning_effort: reasoningEffort.value,
        conversation_id: (props.conversation as any).id,
    });
};

const scrollToBottom = () => {
    nextTick(() => {
        window.scrollTo({
            top: document.documentElement.scrollHeight,
            behavior: 'smooth',
        });
    });
};

onMounted(() => {
    scrollToBottom();

    // Démarrer automatiquement le stream si un message initial est passé en query
    try {
        const search = window.location.search;
        if (search) {
            const params = new URLSearchParams(search);
            const initial = params.get('initial');
            const initialModel = params.get('model');
            const thinkingFlag = params.get('thinking');
            if (thinkingFlag === '1') {
                thinking.value = true;
            }
            if (initial) {
                message.value = initial;
                if (initialModel) model.value = initialModel;
                submit();
                // Nettoyer l'URL pour éviter de relancer au refresh
                window.history.replaceState({}, '', window.location.pathname);
            }
        }
    } catch {}

    // Charger le choix de réflexion depuis localStorage
    try {
        const saved = localStorage.getItem('reasoningEffort');
        if (saved === 'low' || saved === 'medium' || saved === 'high') {
            reasoningEffort.value = saved as 'low' | 'medium' | 'high';
        } else if (saved === 'null') {
            reasoningEffort.value = null;
        }
    } catch {}
});
</script>

<template>
    <Head>
        <title>Conversation - CloneGPT</title>
        <meta
            name="description"
            content="Affichez et poursuivez votre conversation avec CloneGPT. Interagissez avec l'assistant IA en temps réel."
        />
    </Head>
    <AppLayout>
        <div class="mx-auto max-w-3xl px-4 py-6 pb-56 sm:pb-48">
            <h1 class="mb-6 text-2xl text-white">
                {{ conversation.title || 'Untitled Conversation' }}
            </h1>
            <div class="mb-6 space-y-4">
                <div
                    v-for="message in conversation.messages"
                    :key="message.id"
                    class="rounded border p-4"
                    :class="{
                        'bg-blue-50': message.role === 'user',
                        'bg-gray-50': message.role === 'assistant',
                    }"
                >
                    <div
                        class="mb-2 text-xs font-semibold text-[#4A4A50] uppercase"
                    >
                        {{ message.role === 'user' ? 'Vous' : 'Assistant' }}
                    </div>
                    <div class="prose max-w-none text-[#4A4A50] prose-invert">
                        <div v-html="md.render(message.content || '')"></div>
                    </div>
                    <p class="mt-2 text-sm text-[#4A4A50]">
                        {{ new Date(message.created_at).toLocaleString() }}
                    </p>
                </div>
                <div v-if="conversation.messages.length < 1" class="text-white">
                    Aucun message dans cette conversation.
                </div>
            </div>
            <div
                v-if="thinking || isStreaming"
                class="mb-4 flex items-center gap-2 rounded border border-[#C8FF2E] bg-yellow-50/10 p-3 text-white"
            >
                <Loader class="h-4 w-4 animate-spin" />
                <span>L’IA réfléchit…</span>
            </div>
            <!-- Zone d'affichage du streaming en cours -->
            <div v-if="isStreaming || data" class="mb-6 space-y-3">
                <div class="rounded border bg-gray-50 p-4">
                    <div
                        class="mb-2 text-xs font-semibold text-[#4A4A50] uppercase"
                    >
                        Assistant (en cours)
                    </div>
                    <div
                        v-if="streamedReasoning"
                        class="mb-3 text-xs text-[#4A4A50]"
                    >
                        <div class="font-semibold">Trace de raisonnement</div>
                        <pre class="whitespace-pre-wrap">{{
                            streamedReasoning
                        }}</pre>
                    </div>
                </div>
                <div
                    class="prose max-w-none rounded border bg-gray-50 p-4 text-[#4A4A50]"
                >
                    <div v-html="md.render(streamedContent)"></div>
                </div>
            </div>
            <!-- Espace de scroll pour permettre de dépasser la fin du contenu -->
            <div class="h-40 sm:h-24"></div>
        </div>
        <div
            class="fixed right-0 bottom-0 left-0 border-t border-[#2A2A2F] bg-[#121216]/95 shadow-2xl backdrop-blur md:pl-64"
        >
            <div class="mx-auto max-w-3xl px-4 py-4">
                <form @submit.prevent="submit">
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-start">
                        <!-- Colonne gauche: textarea + select + submit -->
                        <div class="flex-1">
                            <div class="relative mb-4 text-white">
                                <textarea
                                    v-model="message"
                                    placeholder="Écrire un message..."
                                    class="w-full rounded-lg border border-[#2A2A2F] bg-[#1B1B1E] p-3 text-white placeholder:text-[#8A8A8F] focus:ring-2 focus:ring-[#C8FF2E]/60 focus:outline-none sm:p-2 sm:pr-28"
                                    rows="5"
                                ></textarea>
                                <button
                                    type="submit"
                                    :disabled="
                                        isFetching ||
                                        isStreaming ||
                                        !message.trim()
                                    "
                                    class="absolute right-2 bottom-2 hidden items-center gap-2 rounded-lg bg-[#FF3B30] px-4 py-2 text-white transition-colors hover:bg-[#C8FF2E] hover:text-black disabled:cursor-not-allowed disabled:opacity-50 sm:inline-flex"
                                >
                                    <Loader
                                        v-if="isStreaming"
                                        class="h-4 w-4 animate-spin"
                                    />
                                    <span v-else>Envoyer</span>
                                </button>
                            </div>
                            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                                <div class="flex flex-col gap-1">
                                    <label
                                        class="text-xs font-medium text-[#8A8A8F]"
                                        >Modèle</label
                                    >
                                    <select
                                        v-model="model"
                                        class="w-full rounded-lg border border-[#2A2A2F] bg-[#1B1B1E] p-2 text-white focus:ring-2 focus:ring-[#C8FF2E]/60 focus:outline-none"
                                    >
                                        <option
                                            v-for="m in props.models"
                                            :key="m.id"
                                            :value="m.id"
                                            class="bg-[#1B1B1E] text-white"
                                        >
                                            {{ m.name }}
                                        </option>
                                    </select>
                                </div>
                                <div class="hidden sm:block"></div>
                            </div>
                            <!-- Bouton mobile en dessous -->
                            <div class="mt-3 sm:hidden">
                                <button
                                    type="submit"
                                    :disabled="
                                        isFetching ||
                                        isStreaming ||
                                        !message.trim()
                                    "
                                    class="w-full rounded-lg bg-[#FF3B30] px-4 py-2 text-white transition-colors hover:bg-[#C8FF2E] hover:text-black disabled:cursor-not-allowed disabled:opacity-50"
                                >
                                    <Loader
                                        v-if="isStreaming"
                                        class="mx-auto h-4 w-4 animate-spin"
                                    />
                                    <span v-else>Envoyer</span>
                                </button>
                            </div>
                        </div>

                        <!-- Colonne droite: contrôle de réflexion -->
                        <div class="flex w-full flex-col gap-3 sm:w-64">
                            <span class="text-xs font-medium text-[#8A8A8F]"
                                >Réflexion</span
                            >
                            <div class="grid grid-cols-2 gap-2">
                                <button
                                    type="button"
                                    @click="
                                        ((reasoningEffort = 'low'),
                                        localStorage.setItem(
                                            'reasoningEffort',
                                            'low',
                                        ))
                                    "
                                    :class="[
                                        'rounded-lg border border-[#2A2A2F] px-3 py-2 text-sm transition-colors',
                                        reasoningEffort === 'low'
                                            ? 'bg-[#C8FF2E] text-black'
                                            : 'bg-[#1B1B1E] text-white hover:bg-[#2A2A2F] hover:text-white',
                                    ]"
                                >
                                    Faible
                                </button>
                                <button
                                    type="button"
                                    @click="
                                        ((reasoningEffort = 'medium'),
                                        localStorage.setItem(
                                            'reasoningEffort',
                                            'medium',
                                        ))
                                    "
                                    :class="[
                                        'rounded-lg border border-[#2A2A2F] px-3 py-2 text-sm transition-colors',
                                        reasoningEffort === 'medium'
                                            ? 'bg-[#C8FF2E] text-black'
                                            : 'bg-[#1B1B1E] text-white hover:bg-[#2A2A2F] hover:text-white',
                                    ]"
                                >
                                    Moyenne
                                </button>
                                <button
                                    type="button"
                                    @click="
                                        ((reasoningEffort = 'high'),
                                        localStorage.setItem(
                                            'reasoningEffort',
                                            'high',
                                        ))
                                    "
                                    :class="[
                                        'rounded-lg border border-[#2A2A2F] px-3 py-2 text-sm transition-colors',
                                        reasoningEffort === 'high'
                                            ? 'bg-[#C8FF2E] text-black'
                                            : 'bg-[#1B1B1E] text-white hover:bg-[#2A2A2F] hover:text-white',
                                    ]"
                                >
                                    Élevée
                                </button>
                                <button
                                    type="button"
                                    @click="
                                        ((reasoningEffort = null),
                                        localStorage.setItem(
                                            'reasoningEffort',
                                            'null',
                                        ))
                                    "
                                    :class="[
                                        'rounded-lg border border-[#2A2A2F] px-3 py-2 text-sm transition-colors',
                                        reasoningEffort === null
                                            ? 'bg-[#C8FF2E] text-black'
                                            : 'bg-[#1B1B1E] text-white hover:bg-[#2A2A2F] hover:text-white',
                                    ]"
                                >
                                    Aucune
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>
