<script setup>
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import hljs from 'highlight.js';
import 'highlight.js/styles/github-dark.css'; // ou un autre thème
import MarkdownIt from 'markdown-it';
import { ref } from 'vue';

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
    conversations: Array,
});

const deleteDialog = ref(false);
const conversationToDelete = ref(null);

const openDeleteDialog = (conversation) => {
    conversationToDelete.value = conversation;
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

const renameDialog = ref(false);
const conversationToRename = ref(null);
const newTitle = ref('');

const openRenameDialog = (conversation) => {
    conversationToRename.value = conversation;
    newTitle.value = conversation.title || '';
    renameDialog.value = true;
};

const renameConversation = () => {
    if (conversationToRename.value && newTitle.value.trim()) {
        router.put(
            `/conversations/${conversationToRename.value.id}`,
            {
                title: newTitle.value.trim(),
            },
            {
                preserveScroll: true,
                onSuccess: () => {
                    renameDialog.value = false;
                    conversationToRename.value = null;
                    newTitle.value = '';
                },
            },
        );
    }
};
</script>

<template>
    <Head>
        <title>Conversations - CloneGPT</title>
        <meta
            name="description"
            content="Gérez vos conversations avec CloneGPT. Créez, renommez ou supprimez vos discussions facilement."
        />
    </Head>
    <AppLayout>
        <div class="mx-auto max-w-3xl px-4 py-6">
            <div class="w-full">
                <div class="flex">
                    <h1 class="mb-6 flex-1 text-2xl text-white">
                        Conversations
                    </h1>
                    <Button
                        as="a"
                        class="mb-4 self-end bg-[#FF3B30] hover:bg-[#C8FF2E] hover:text-black"
                        href="/conversations/create"
                        >Nouvelle Conversation
                    </Button>
                </div>
                <ul>
                    <li
                        v-for="conversation in conversations"
                        :key="conversation.id"
                        class="mb-4"
                    >
                        <a
                            :href="`/conversations/${conversation.id}`"
                            class="flex flex-col gap-3 rounded border px-4 py-4 text-white hover:bg-gray-700 sm:flex-row sm:items-center sm:px-6 md:px-10 lg:px-16 xl:px-24"
                        >
                            <AppLogoIcon
                                class="h-8 w-8 fill-white stroke-white text-white sm:mr-4"
                            />
                            <div class="flex-1">
                                <h2 class="text-lg">
                                    {{
                                        conversation.title ||
                                        'Untitled Conversation'
                                    }}
                                </h2>
                                <p class="mt-2 text-sm text-white">
                                    Dernier message le
                                    {{
                                        new Date(
                                            conversation.updated_at,
                                        ).toLocaleString()
                                    }}
                                </p>
                            </div>
                            <DropdownMenu class="self-end">
                                <DropdownMenuTrigger @click.prevent>
                                    <Button
                                        variant="ghost"
                                        dusk="actions-trigger"
                                        >•••</Button
                                    >
                                </DropdownMenuTrigger>
                                <DropdownMenuContent align="end">
                                    <DropdownMenuLabel
                                        >Actions</DropdownMenuLabel
                                    >
                                    <DropdownMenuSeparator />
                                    <DropdownMenuItem
                                        @click="openRenameDialog(conversation)"
                                        dusk="rename-conversation"
                                        >Renommer</DropdownMenuItem
                                    >
                                    <DropdownMenuSeparator />
                                    <DropdownMenuItem
                                        @click="openDeleteDialog(conversation)"
                                        dusk="delete-conversation"
                                        >Supprimer</DropdownMenuItem
                                    >
                                </DropdownMenuContent>
                            </DropdownMenu>
                        </a>
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
                    <Button
                        variant="destructive"
                        @click="deleteConversation"
                        dusk="confirm-delete"
                    >
                        Supprimer
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>

        <!-- Dialog de renommage -->
        <Dialog v-model:open="renameDialog">
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>Renommer la conversation</DialogTitle>
                    <DialogDescription>
                        Entrez un nouveau nom pour la conversation "{{
                            conversationToRename?.title
                        }}".
                    </DialogDescription>
                </DialogHeader>
                <div class="mt-4">
                    <input
                        v-model="newTitle"
                        type="text"
                        class="w-full rounded border p-2"
                        placeholder="Nouveau nom de la conversation"
                        @keyup.enter="renameConversation"
                        dusk="rename-input"
                    />
                </div>
                <DialogFooter>
                    <Button variant="outline" @click="renameDialog = false">
                        Annuler
                    </Button>
                    <Button
                        variant="primary"
                        :disabled="!newTitle.trim()"
                        @click="renameConversation"
                        dusk="confirm-rename"
                    >
                        Renommer
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </AppLayout>
</template>
