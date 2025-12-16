<script setup>
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
</script>

<template>
    <Card class="mx-auto my-8 max-w-full">
        <CardHeader>
            <CardTitle class="text-2xl font-bold">FAQ</CardTitle>
        </CardHeader>
        <CardContent>
            <ul class="list-inside list-disc space-y-2">
                <li>
                    <strong>Q :</strong> Comment puis-je créer une nouvelle
                    conversation ?
                    <br />
                    <strong>R :</strong> Cliquez sur le bouton "Nouvelle
                    Conversation" sur la page des conversations pour commencer.
                </li>
                <li>
                    <strong>Q :</strong> Quels modèles d'IA sont disponibles ?
                    <br />
                    <strong>R :</strong> Nous proposons plusieurs modèles, y
                    compris GPT-3.5 et GPT-4. Vous pouvez choisir le modèle lors
                    de la création d'une conversation.
                </li>
                <li>
                    <strong>Q :</strong> Puis-je renommer ou supprimer une
                    conversation existante ?
                    <br />
                    <strong>R :</strong> Oui, vous pouvez renommer ou supprimer
                    des conversations en utilisant les options disponibles à
                    côté de chaque conversation dans la liste.
                </li>
                <li>
                    <strong>Q :</strong> Comment puis-je gérer mes paramètres de
                    compte ?
                    <br />
                    <strong>R :</strong> Accédez à la page des paramètres de
                    votre compte en cliquant sur votre profil dans le coin
                    supérieur droit.
                </li>
                <li>
                    <strong>Q :</strong> Puis-je personnaliser des instructions
                    pour l'IA ?
                    <br />
                    <strong>R :</strong> Oui, vous pouvez définir des
                    instructions personnalisées pour guider le comportement de
                    l'IA dans chaque conversation.
                </li>
            </ul>
        </CardContent>
    </Card>
</template>
