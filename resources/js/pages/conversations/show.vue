<script setup>
import { store } from '@/actions/App/Http/Controllers/MessageController';
import Message from '@/components/Message.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { useForm } from '@inertiajs/vue3';

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

const submit = () => {
    form.post(store());
};
</script>

<template>
    <AppLayout>
        <div class="mx-auto max-w-3xl px-4 py-6">
            <h1 class="mb-6 text-2xl font-bold">
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
                        class="mb-2 text-xs font-semibold text-gray-600 uppercase"
                    >
                        {{ message.role === 'user' ? 'Vous' : 'Assistant' }}
                    </div>
                    <Message
                        v-if="message.role === 'assistant'"
                        :response="message.content"
                    />
                    <p v-else class="whitespace-pre-wrap">
                        {{ message.content }}
                    </p>
                    <p class="mt-2 text-sm text-gray-500">
                        {{ new Date(message.created_at).toLocaleString() }}
                    </p>
                </div>
                <div v-if="conversation.messages.length < 1">
                    Aucun message dans cette conversation.
                </div>
            </div>
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
