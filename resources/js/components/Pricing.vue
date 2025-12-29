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
    <Card class="mx-auto my-8 max-w-full bg-card text-card-foreground">
        <CardContent>
            <CardTitle class="mb-4 text-2xl font-bold">Tarification</CardTitle>
            <CardDescription class="mb-6 text-foreground">
                Choisissez le plan qui vous convient le mieux.
            </CardDescription>
            <div class="flex flex-col gap-4 lg:flex-row">
                <Card
                    class="m-4 border border-border bg-card p-8 text-card-foreground"
                >
                    <CardTitle>Gratuit</CardTitle>
                    <Button
                        disabled
                        class="cursor-default bg-muted text-foreground hover:bg-muted"
                    >
                        Plan actuel
                    </Button>
                    <CardDescription class="text-foreground">
                        Accès limité aux fonctionnalités de base.
                    </CardDescription>
                    <CardDescription class="mt-4 font-bold text-foreground">
                        0€ / mois
                    </CardDescription>
                </Card>
                <Card
                    class="m-4 border border-border bg-primary-soft p-8 text-foreground"
                >
                    <CardTitle>Pro</CardTitle>
                    <Button
                        class="bg-accent text-accent-foreground hover:bg-accent-soft dark:bg-primary dark:text-primary-foreground dark:hover:bg-primary-soft"
                    >
                        Me choisir
                    </Button>
                    <CardDescription class="text-foreground">
                        Accès complet à toutes les fonctionnalités et
                        prioritaire support.
                    </CardDescription>
                    <CardDescription class="mt-4 font-bold text-foreground">
                        19,99€ / mois
                    </CardDescription>
                </Card>

                <Card
                    class="m-4 border border-border bg-card p-8 text-card-foreground"
                >
                    <CardTitle>Entreprise</CardTitle>
                    <Button
                        class="bg-accent text-accent-foreground hover:bg-accent-soft dark:bg-primary dark:text-primary-foreground dark:hover:bg-primary-soft"
                    >
                        Me choisir
                    </Button>
                    <CardDescription class="text-foreground">
                        Solutions personnalisées pour les grandes équipes et
                        entreprises.
                    </CardDescription>
                    <CardDescription class="mt-4 font-bold text-foreground">
                        Contactez-nous
                    </CardDescription>
                </Card>
            </div>
        </CardContent>
    </Card>
</template>
