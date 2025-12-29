<script setup>
import { store } from '@/actions/App/Http/Controllers/InstructionController';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { Loader } from 'lucide-vue-next';

const props = defineProps({
    instructions: {
        type: String,
        default: '',
    },
});

const form = useForm({
    instructions: props.instructions || '',
});

const submit = () => {
    form.post(store(), {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head>
        <title>Instructions - CloneGPT</title>
        <meta
            name="description"
            content="Gérez vos instructions personnalisées pour guider le comportement de l'IA dans CloneGPT."
        />
    </Head>
    <AppLayout>
        <div class="p-4 text-foreground">
            <h1 class="mb-4 text-2xl text-primary">Instructions</h1>
            <p>
                Bienvenue dans la section des instructions. Ici, vous pouvez
                ajouter des instructions personnalisées pour guider le
                comportement de l'IA lors de vos conversations.
            </p>
            <p class="mt-2">
                Pour ajouter une instruction, cliquez sur le bouton "Ajouter une
                instruction" et saisissez votre texte. Vous pouvez gérer vos
                instructions à tout moment depuis cette page.
            </p>
        </div>

        <form @submit.prevent="submit" class="px-4">
            <div
                v-if="form.recentlySuccessful"
                class="mb-4 rounded bg-accent p-3 text-accent-foreground"
            >
                Instructions mises à jour avec succès !
            </div>

            <label
                for="comportement"
                class="mb-2 block font-semibold text-foreground"
                >Comportement de l'IA :</label
            >
            <div class="mb-2 text-foreground">
                <h2>Suggestions d'instructions :</h2>
                <ul class="list-disc pl-5">
                    <li>Sois concis et précis dans tes réponses.</li>
                    <li>
                        Adopte un ton amical et encourageant lors de tes
                        interactions.
                    </li>
                    <li>
                        Fournis des explications détaillées lorsque tu réponds à
                        des questions techniques.
                    </li>
                    <li>
                        Pose des questions de clarification si une demande est
                        ambiguë.
                    </li>
                </ul>
            </div>
            <div class="relative mb-4 text-foreground">
                <textarea
                    v-model="form.instructions"
                    id="comportement"
                    class="w-full rounded-lg border border-border bg-background p-3 pr-28 text-foreground placeholder:text-muted-foreground focus:ring-2 focus:ring-ring/60 focus:outline-none"
                    rows="6"
                    placeholder="Décrivez ici le comportement souhaité de l'IA..."
                    :disabled="form.processing"
                ></textarea>
                <button
                    type="submit"
                    :disabled="form.processing || !form.instructions.trim()"
                    class="absolute right-2 bottom-2 inline-flex items-center gap-2 rounded-lg bg-accent px-4 py-2 text-accent-foreground transition-colors hover:bg-accent-soft disabled:cursor-not-allowed disabled:opacity-50 dark:bg-destructive dark:text-destructive-foreground dark:hover:bg-primary dark:hover:text-black"
                >
                    <Loader
                        v-if="form.processing"
                        class="h-4 w-4 animate-spin"
                    />
                    <span v-else>Envoyer</span>
                </button>
            </div>
        </form>
    </AppLayout>
</template>
