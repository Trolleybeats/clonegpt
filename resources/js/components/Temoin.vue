<script setup>
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
</script>

<template>
    <Card class="mx-auto my-8 max-w-full">
        <CardHeader>
            <CardTitle class="text-2xl font-bold">Témoignages</CardTitle>
        </CardHeader>
        <CardContent>
            <ul class="list-inside list-disc space-y-2">
                <li>
                    « Cette application a transformé ma façon de travailler ! »
                    - Alice D.
                </li>
                <li>
                    « Un outil indispensable pour mes projets quotidiens. » -
                    Marc L.
                </li>
                <li>
                    « Support client exceptionnel et fonctionnalités puissantes.
                    » - Sophie R.
                </li>
            </ul>
        </CardContent>
    </Card>
</template>
