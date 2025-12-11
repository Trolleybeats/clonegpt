<script setup>
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import { useStream } from '@laravel/stream-vue';
import hljs from 'highlight.js';
import 'highlight.js/styles/github-dark.css';
import { Loader } from 'lucide-vue-next';
import MarkdownIt from 'markdown-it';
import { nextTick, onMounted, ref } from 'vue';

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

const props = defineProps({
    conversation: Object,
    models: Array,
    selectedModel: String,
    prefill: Object,
});

// Refs pour la réactivité de l'UI
// Normalise le contenu des messages en chaîne pour MarkdownIt
const normalizeContent = (content) => {
    if (content == null) return '';
    if (typeof content === 'string') return content;
    if (Array.isArray(content)) {
        // Cherche le premier bloc texte { type: 'text', data: string }
        const textItem = content.find(
            (c) =>
                c &&
                (c.type === 'text' || c.type === 'TEXT') &&
                typeof c.data === 'string',
        );
        return textItem ? textItem.data : JSON.stringify(content);
    }
    if (typeof content === 'object') {
        // Certaines anciennes sauvegardes peuvent stocker { data: '...' }
        if (typeof content.data === 'string') return content.data;
        return JSON.stringify(content);
    }
    return String(content);
};

const localMessages = ref(
    (props.conversation.messages || []).map((m) => ({
        ...m,
        content: normalizeContent(m.content),
    })),
);
const newMessage = ref('');
const selectedModelRef = ref(props.selectedModel);
const messagesContainer = ref(null);

// Helper de streaming
const { isStreaming, send: sendStream } = useStream(
    `/chat/${props.conversation.id}/stream`,
    {
        onData: (data) => {
            // Concaténer chaque chunk au dernier message (celui de l'assistant)
            const lastMessage =
                localMessages.value[localMessages.value.length - 1];
            if (lastMessage && lastMessage.role === 'assistant') {
                lastMessage.content += data;
                scrollToBottom();
            }
        },
        onFinish: () => {
            newMessage.value = ''; // Réinitialiser le champ de saisie
        },
        onError: (error) => {
            console.error('Erreur de streaming:', error);
            // Optionnel: afficher une erreur à l'utilisateur
            const lastMessage =
                localMessages.value[localMessages.value.length - 1];
            if (lastMessage && lastMessage.role === 'assistant') {
                lastMessage.content = 'Désolé, une erreur est survenue.';
            }
        },
    },
);

const scrollToBottom = () => {
    nextTick(() => {
        if (messagesContainer.value) {
            messagesContainer.value.scrollTop =
                messagesContainer.value.scrollHeight;
        } else {
            // Fallback si le conteneur n'est pas prêt
            window.scrollTo({
                top: document.documentElement.scrollHeight,
                behavior: 'smooth',
            });
        }
    });
};

onMounted(() => {
    // Affiche les données de la conversation dans la console du navigateur
    console.log(
        'Conversation data from backend:',
        JSON.parse(JSON.stringify(props.conversation)),
    );
    scrollToBottom();

    // Si on arrive depuis la création avec un prefill, auto-démarrer le stream
    if (
        props.prefill &&
        typeof props.prefill.text === 'string' &&
        props.prefill.text.trim()
    ) {
        newMessage.value = props.prefill.text;
        sendMessage();
    }
});

const sendMessage = () => {
    if (isStreaming.value || !newMessage.value.trim()) return;

    // 1. Ajouter le message de l'utilisateur à l'UI
    const userMessage = {
        id: Date.now(),
        role: 'user',
        content: newMessage.value,
        created_at: new Date().toISOString(),
    };
    localMessages.value.push(userMessage);

    // 2. Ajouter un message vide pour l'assistant (placeholder)
    const assistantMessage = {
        id: Date.now() + 1,
        role: 'assistant',
        content: '', // Sera rempli par le stream
        created_at: new Date().toISOString(),
    };
    localMessages.value.push(assistantMessage);
    scrollToBottom();

    // 3. Envoyer le message au backend via le stream
    sendStream({
        text: newMessage.value,
    });
};
</script>

<template>
    <AppLayout>
        <div ref="messagesContainer" class="flex-1 overflow-y-auto">
            <div class="mx-auto max-w-3xl px-4 py-6 pb-48">
                <h1 class="mb-6 text-2xl font-bold text-white">
                    {{ conversation.title || 'Untitled Conversation' }}
                </h1>
                <div class="mb-6 space-y-4">
                    <div
                        v-for="message in localMessages"
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
                        <div
                            class="prose max-w-none dark:prose-invert"
                            v-html="md.render(message.content || '')"
                        ></div>
                        <p class="mt-2 text-sm text-[#4A4A50]">
                            {{ new Date(message.created_at).toLocaleString() }}
                        </p>
                    </div>
                    <div v-if="localMessages.length < 1" class="text-white">
                        Aucun message dans cette conversation.
                    </div>
                </div>
            </div>
        </div>
        <div
            class="fixed right-0 bottom-0 left-0 border-t bg-[#1B1B1E] shadow-lg"
        >
            <div class="mx-auto max-w-3xl px-4 py-4">
                <form @submit.prevent="sendMessage">
                    <div class="relative mb-4 text-white">
                        <textarea
                            v-model="newMessage"
                            placeholder="Écrire un message..."
                            class="w-full rounded border p-2 pr-24"
                            rows="4"
                            :disabled="isStreaming"
                            @keydown.enter.prevent="sendMessage"
                        ></textarea>
                        <Button
                            :disabled="isStreaming || !newMessage.trim()"
                            class="absolute right-2 bottom-6 bg-[#FF3B30] hover:bg-[#C8FF2E] hover:text-black disabled:cursor-not-allowed disabled:opacity-50"
                        >
                            <Loader v-if="isStreaming" class="animate-spin" />
                            <span v-else>Envoyer</span>
                        </Button>
                    </div>
                    <select
                        v-model="selectedModelRef"
                        class="w-full rounded border p-2 text-white"
                        :disabled="isStreaming"
                    >
                        <option
                            v-for="model in props.models"
                            :key="model.id"
                            :value="model.id"
                            class="bg-[#1B1B1E] text-white"
                        >
                            {{ model.name }}
                        </option>
                    </select>
                </form>
            </div>
        </div>
    </AppLayout>
</template>
