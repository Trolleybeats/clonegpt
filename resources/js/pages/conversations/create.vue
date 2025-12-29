<script setup>
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { Loader } from 'lucide-vue-next';

const props = defineProps({
    models: Array,
    selectedModel: String,
});

const form = useForm({
    content: '',
    model: props.selectedModel,
});

const submit = () => {
    form.post('/conversations', {
        onSuccess: () => {
            form.reset('content');
        },
    });
};
</script>

<template>
    <Head>
        <title>Nouvelle Conversation - CloneGPT</title>
        <meta
            name="description"
            content="Démarrez une nouvelle conversation avec CloneGPT en sélectionnant un modèle et en envoyant votre message."
        />
    </Head>
    <AppLayout>
        <div class="mx-auto mt-24 max-w-3xl px-4 py-6">
            <h1 class="mb-6 text-2xl text-foreground">
                <AppLogoIcon class="mr-2 inline-block h-8 w-8 text-primary" />
                Nouvelle Conversation
            </h1>
            <form @submit.prevent="submit">
                <div class="relative mb-4 text-foreground">
                    <textarea
                        dusk="content"
                        v-model="form.content"
                        placeholder="Écrire un message..."
                        class="w-full rounded-lg border border-border bg-background p-3 pr-28 text-foreground placeholder:text-muted-foreground focus:ring-2 focus:ring-ring/60 focus:outline-none"
                        rows="5"
                    ></textarea>
                    <button
                        type="submit"
                        :disabled="form.processing || !form.content.trim()"
                        dusk="send"
                        class="absolute right-2 bottom-2 inline-flex items-center gap-2 rounded-lg bg-accent px-4 py-2 text-accent-foreground transition-colors hover:bg-accent-soft disabled:cursor-not-allowed disabled:opacity-50 dark:bg-destructive dark:text-destructive-foreground dark:hover:bg-primary dark:hover:text-black"
                    >
                        <Loader
                            v-if="form.processing"
                            class="h-4 w-4 animate-spin"
                        />
                        <span v-else>Envoyer</span>
                    </button>
                </div>
                <select
                    v-model="form.model"
                    class="w-full rounded-lg border border-border bg-background p-2 text-foreground focus:ring-2 focus:ring-ring/60 focus:outline-none"
                >
                    <option
                        v-for="model in props.models"
                        :key="model.id"
                        :value="model.id"
                        class="bg-background text-foreground"
                    >
                        {{ model.name }}
                    </option>
                </select>
            </form>
        </div>
    </AppLayout>
</template>
