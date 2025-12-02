<script setup>
import hljs from 'highlight.js';
import 'highlight.js/styles/github-dark.css'; // ou un autre thème
import MarkdownIt from 'markdown-it';

const props = defineProps({
    response: String,
});

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
</script>

<template>
    <div
        v-if="props.response"
        class="prose max-w-none prose-slate dark:prose-invert"
        v-html="md.render(props.response)"
    ></div>
</template>
