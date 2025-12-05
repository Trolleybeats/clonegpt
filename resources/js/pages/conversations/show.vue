<script setup>
import { store } from '@/actions/App/Http/Controllers/MessageController';
import Message from '@/components/Message.vue';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import { useForm } from '@inertiajs/vue3';
import hljs from 'highlight.js';
import 'highlight.js/styles/github-dark.css'; // ou un autre thème
import { Loader } from 'lucide-vue-next';
import MarkdownIt from 'markdown-it';
import { nextTick, onMounted } from 'vue';

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
// Utilisation : md.render(props.response)

const props = defineProps({
    conversation: Object,
    models: Array,
    selectedModel: String,
    response: String,
    error: String,
});

const form = useForm({
    content: props.content ?? '',
    conversation_id: props.conversation.id,
    model: props.selectedModel,
});

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
});

const submit = () => {
    form.post(store(), {
        preserveScroll: false,
        onSuccess: () => {
            form.reset('content');
            scrollToBottom();
        },
    });
};
</script>

<template>
    <AppLayout>
        <div class="mx-auto max-w-3xl px-4 py-6 pb-48">
            <h1 class="mb-6 text-2xl font-bold text-white">
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
                    <Message
                        v-if="message.role === 'assistant'"
                        :response="message.content"
                    />
                    <div
                        v-else
                        class="prose max-w-none dark:prose-invert"
                        v-html="md.render(message.content)"
                    ></div>
                    <p class="mt-2 text-sm text-[#4A4A50]">
                        {{ new Date(message.created_at).toLocaleString() }}
                    </p>
                </div>
                <div v-if="conversation.messages.length < 1" class="text-white">
                    Aucun message dans cette conversation.
                </div>
            </div>
        </div>
        <div
            class="fixed right-0 bottom-0 left-0 border-t bg-[#1B1B1E] shadow-lg"
        >
            <div class="mx-auto max-w-3xl px-4 py-4">
                <form @submit.prevent="submit">
                    <div class="relative mb-4 text-white">
                        <textarea
                            v-model="form.content"
                            placeholder="Écrire un message..."
                            class="w-full rounded border p-2 pr-24"
                            rows="4"
                        ></textarea>
                        <Button
                            :disabled="form.processing || !form.content.trim()"
                            class="absolute right-2 bottom-6 bg-[#FF3B30] hover:bg-[#C8FF2E] hover:text-black disabled:cursor-not-allowed disabled:opacity-50"
                        >
                            <Loader
                                v-if="form.processing"
                                class="animate-spin"
                            />
                            <span v-else>Envoyer</span>
                        </Button>
                    </div>
                    <select
                        v-model="form.model"
                        class="w-full rounded border p-2 text-white"
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
