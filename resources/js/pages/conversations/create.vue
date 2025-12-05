<script setup>
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import { useForm } from '@inertiajs/vue3';
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
    <AppLayout>
        <div class="mx-auto mt-24 max-w-3xl px-4 py-6">
            <h1 class="mb-6 text-2xl font-bold text-white">
                <AppLogoIcon class="mr-2 inline-block h-8 w-8" />
                Nouvelle Conversation
            </h1>
            <form @submit.prevent="submit">
                <div class="relative mb-4">
                    <textarea
                        v-model="form.content"
                        placeholder="Écrire un message..."
                        class="w-full rounded border p-2 pr-24 text-white"
                        rows="4"
                    ></textarea>
                    <Button
                        :disabled="form.processing || !form.content.trim()"
                        class="absolute right-2 bottom-6 cursor-pointer bg-[#FF3B30] hover:bg-[#C8FF2E] hover:text-black disabled:cursor-not-allowed disabled:opacity-50"
                    >
                        <Loader v-if="form.processing" class="animate-spin" />
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
    </AppLayout>
</template>
