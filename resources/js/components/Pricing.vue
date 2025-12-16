<script setup>
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardTitle,
} from '@/components/ui/card';
</script>

<template>
    <Card class="mx-auto my-8 max-w-full">
        <CardContent>
            <CardTitle class="mb-4 text-2xl font-bold">Tarification</CardTitle>
            <CardDescription class="mb-6">
                Choisissez le plan qui vous convient le mieux.
            </CardDescription>
            <div class="flex flex-col gap-4 lg:flex-row">
                <Card class="m-4 p-8">
                    <CardTitle>Gratuit</CardTitle>
                    <Button
                        disabled
                        class="cursor-default bg-black text-white hover:bg-black/90 dark:bg-white dark:text-black dark:hover:bg-white/90"
                    >
                        Plan actuel
                    </Button>
                    <CardDescription class="text-black">
                        Accès limité aux fonctionnalités de base.
                    </CardDescription>
                    <CardDescription class="mt-4 font-bold text-black">
                        0€ / mois
                    </CardDescription>
                </Card>

                <Card class="m-4 bg-[#d6d6e1] p-8">
                    <CardTitle>Pro</CardTitle>
                    <Button> Me choisir </Button>
                    <CardDescription class="text-black">
                        Accès complet à toutes les fonctionnalités et
                        prioritaire support.
                    </CardDescription>
                    <CardDescription class="mt-4 font-bold text-black">
                        19,99€ / mois
                    </CardDescription>
                </Card>

                <Card class="m-4 p-8">
                    <CardTitle>Entreprise</CardTitle>
                    <Button> Me choisir </Button>
                    <CardDescription class="text-black">
                        Solutions personnalisées pour les grandes équipes et
                        entreprises.
                    </CardDescription>
                    <CardDescription class="mt-4 font-bold text-black">
                        Contactez-nous
                    </CardDescription>
                </Card>
            </div>
        </CardContent>
    </Card>
</template>
