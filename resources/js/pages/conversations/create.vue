<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
    models: Array,
    selectedModel: String,
});

const form = useForm({
    content: '',
    model: props.selectedModel,
});

const submit = () => {
    form.post('/conversations');
};
</script>

<template>
    <AppLayout>
        <div class="mx-auto max-w-3xl px-4 py-6">
            <h1 class="mb-6 text-2xl font-bold">Nouvelle Conversation</h1>
            <form @submit.prevent="submit">
                <textarea
                    v-model="form.content"
                    placeholder="Écrire un message..."
                    class="mb-4 w-full rounded border p-2"
                    rows="4"
                ></textarea>
                <select v-model="form.model" class="mb-4 rounded border p-2">
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
                    class="rounded bg-blue-600 px-4 py-2 text-white disabled:opacity-50"
                >
                    Envoyer
                </button>
            </form>
        </div>
    </AppLayout>
</template>
