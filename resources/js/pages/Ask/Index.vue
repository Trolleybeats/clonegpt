<script setup>
import { ask } from '@/actions/App/Http/Controllers/AskController';
import AppLayout from '@/layouts/AppLayout.vue';
import { useForm } from '@inertiajs/vue3';
import hljs from 'highlight.js';
import 'highlight.js/styles/github-dark.css'; // ou un autre thème
import MarkdownIt from 'markdown-it';

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
    models: Array,
    selectedModel: String,
    message: String,
    response: String,
    error: String,
});

const form = useForm({
    message: props.message ?? '',
    model: props.selectedModel,
});

const submit = () => {
    form.post(ask());
};
</script>
<template>
    <AppLayout>
        <div
            class="mx-auto flex min-h-screen max-w-3xl items-center justify-center px-4"
        >
            <div class="w-full">
                <div
                    v-if="props.response"
                    class="prose mb-6 max-w-none prose-slate dark:prose-invert"
                    v-html="md.render(props.response)"
                ></div>
                <div v-if="props.error" class="mb-4 text-red-500">
                    {{ props.error }}
                </div>
                <form @submit.prevent="submit">
                    <textarea
                        v-model="form.message"
                        placeholder="Quelle est votre question ?"
                        class="mb-4 w-full rounded border p-2"
                        rows="4"
                    ></textarea>
                    <select
                        v-model="form.model"
                        class="mb-4 rounded border p-2"
                    >
                        <option
                            v-for="model in props.models"
                            :key="model.id"
                            :value="model.id"
                        >
                            {{ model.name }}
                        </option>
                    </select>
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="ml-4 rounded bg-blue-500 px-4 py-2 text-white disabled:opacity-50"
                    >
                        {{ form.processing ? 'Envoi...' : 'Poser la question' }}
                    </button>
                </form>
            </div>
        </div>
    </AppLayout>
</template>
