<script setup>
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import AppLayout from '@/layouts/AppLayout.vue';
import { router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    conversations: Array,
});

const deleteDialog = ref(false);
const conversationToDelete = ref(null);

const openDeleteDialog = (exp) => {
    conversationToDelete.value = exp;
    deleteDialog.value = true;
};

const deleteConversation = () => {
    if (conversationToDelete.value) {
        router.delete(`/conversations/${conversationToDelete.value.id}`, {
            onSuccess: () => {
                deleteDialog.value = false;
                conversationToDelete.value = null;
            },
        });
    }
};
</script>

<template>
    <AppLayout>
        <div class="mx-auto max-w-3xl px-4 py-6">
            <div class="w-full">
                <h1 class="mb-6 text-2xl font-bold">Conversations</h1>
                <ul>
                    <li
                        v-for="conversation in conversations"
                        :key="conversation.id"
                        class="mb-4 rounded border p-4 pr-60 pl-60 hover:bg-gray-300"
                    >
                        <a
                            :href="`/conversations/${conversation.id}`"
                            class="text-lg font-semibold hover:underline"
                        >
                            {{ conversation.title || 'Untitled Conversation' }}
                        </a>
                        <p class="mt-2 text-sm text-gray-500">
                            Créé le
                            {{
                                new Date(
                                    conversation.created_at,
                                ).toLocaleString()
                            }}
                        </p>
                        <Button
                            variant="destructive"
                            @click="openDeleteDialog(conversation)"
                            >Supprimer</Button
                        >
                    </li>
                    <li v-if="conversations.length < 1">Aucune conversation</li>
                </ul>
            </div>
        </div>
        <!-- Dialog de confirmation de suppression -->
        <Dialog v-model:open="deleteDialog">
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>Confirmer la suppression</DialogTitle>
                    <DialogDescription>
                        Êtes-vous sûr de vouloir supprimer "{{
                            conversationToDelete?.title
                        }}" ? Cette action est irréversible.
                    </DialogDescription>
                </DialogHeader>
                <DialogFooter>
                    <Button variant="outline" @click="deleteDialog = false">
                        Annuler
                    </Button>
                    <Button variant="destructive" @click="deleteConversation">
                        Supprimer
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </AppLayout>
</template>
