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
            <h1 class="mb-6 text-2xl text-white">
                <AppLogoIcon class="mr-2 inline-block h-8 w-8" />
                Nouvelle Conversation
            </h1>
            <form @submit.prevent="submit">
                <div class="relative mb-4 text-white">
                    <textarea
                        v-model="form.content"
                        placeholder="Écrire un message..."
                        class="w-full rounded-lg border border-[#2A2A2F] bg-[#1B1B1E] p-3 pr-28 text-white placeholder:text-[#8A8A8F] focus:ring-2 focus:ring-[#C8FF2E]/60 focus:outline-none"
                        rows="5"
                    ></textarea>
                    <button
                        type="submit"
                        :disabled="form.processing || !form.content.trim()"
                        class="absolute right-2 bottom-2 inline-flex items-center gap-2 rounded-lg bg-[#FF3B30] px-4 py-2 text-white transition-colors hover:bg-[#C8FF2E] hover:text-black disabled:cursor-not-allowed disabled:opacity-50"
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
                    class="w-full rounded-lg border border-[#2A2A2F] bg-[#1B1B1E] p-2 text-white focus:ring-2 focus:ring-[#C8FF2E]/60 focus:outline-none"
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
    </AppLayout>
</template>
