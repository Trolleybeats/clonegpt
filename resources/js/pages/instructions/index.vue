<script setup>
import { store } from '@/actions/App/Http/Controllers/InstructionController';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import { useForm } from '@inertiajs/vue3';
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
    <AppLayout>
        <div class="p-4 text-white">
            <h1 class="mb-4 text-2xl font-bold">Instructions</h1>
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
                class="mb-4 rounded bg-green-500 p-3 text-white"
            >
                Instructions mises à jour avec succès !
            </div>

            <label
                for="comportement"
                class="mb-2 block font-semibold text-white"
                >Comportement de l'IA :</label
            >
            <div class="mb-2 text-white">
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
            <div class="relative">
                <textarea
                    v-model="form.instructions"
                    id="comportement"
                    class="w-full rounded border bg-[#1B1B1E] p-2 text-white focus:ring-2 focus:ring-blue-500 focus:outline-none"
                    rows="6"
                    placeholder="Décrivez ici le comportement souhaité de l'IA..."
                    :disabled="form.processing"
                ></textarea>
                <Button
                    :disabled="form.processing || !form.instructions.trim()"
                    class="absolute right-2 bottom-6 bg-[#FF3B30] hover:bg-[#C8FF2E] hover:text-black disabled:cursor-not-allowed disabled:opacity-50"
                >
                    <Loader v-if="form.processing" class="animate-spin" />
                    <span v-else>Envoyer</span>
                </Button>
            </div>
        </form>
    </AppLayout>
</template>
