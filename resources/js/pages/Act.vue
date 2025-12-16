<script setup lang="ts">
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import { Head, Link } from '@inertiajs/vue3';
</script>

<template>
    <Head title="AI Act"
        ><meta
            name="description"
            content="Exemple fictif de conformité et transparence selon l'AI Act pour CloneGPT."
    /></Head>
    <div
        class="min-h-screen bg-[#1B1B1E] px-6 py-10 text-white dark:bg-[#0a0a0a]"
    >
        <main class="mx-auto w-full max-w-2xl">
            <header class="mb-8 flex items-center gap-3">
                <AppLogoIcon class="h-8 w-8 text-white" />
                <h1 class="text-3xl font-semibold">
                    AI Act – Conformité et Transparence
                </h1>
            </header>

            <section class="space-y-6">
                <p>
                    Cette page présente, de manière fictive, la façon dont
                    l’application s’aligne sur les exigences de transparence et
                    de sécurité de l’AI Act. Elle n’a pas de valeur légale et
                    sert uniquement d’exemple.
                </p>

                <div class="space-y-3">
                    <h2 class="text-xl font-medium">Classification et usage</h2>
                    <ul class="list-disc space-y-2 pl-5">
                        <li>
                            Catégorie d’usage: assistant conversationnel à visée
                            informative.
                        </li>
                        <li>
                            Non destiné à des décisions ayant un impact
                            juridique, financier ou médical sans supervision
                            humaine.
                        </li>
                    </ul>
                </div>

                <div class="space-y-3">
                    <h2 class="text-xl font-medium">Transparence</h2>
                    <ul class="list-disc space-y-2 pl-5">
                        <li>
                            Indication claire lorsque le contenu est généré par
                            une IA.
                        </li>
                        <li>
                            Description des sources et limites des modèles
                            utilisés.
                        </li>
                        <li>
                            Mentions des données d’entrée fournies par
                            l’utilisateur et traitement côté serveur.
                        </li>
                    </ul>
                </div>

                <div class="space-y-3">
                    <h2 class="text-xl font-medium">Supervision humaine</h2>
                    <ul class="list-disc space-y-2 pl-5">
                        <li>
                            L’utilisateur conserve le contrôle: édition,
                            suppression et validation des réponses.
                        </li>
                        <li>
                            Escalade possible vers un humain pour les cas
                            sensibles.
                        </li>
                    </ul>
                </div>

                <div class="space-y-3">
                    <h2 class="text-xl font-medium">
                        Sécurité et gestion des risques
                    </h2>
                    <ul class="list-disc space-y-2 pl-5">
                        <li>
                            Journalisation des interactions et surveillance des
                            anomalies.
                        </li>
                        <li>
                            Mises à jour régulières des modèles et dépendances.
                        </li>
                        <li>
                            Tests et garde‑fous pour limiter les contenus
                            inappropriés.
                        </li>
                    </ul>
                </div>

                <div class="space-y-3">
                    <h2 class="text-xl font-medium">
                        Données et confidentialité
                    </h2>
                    <ul class="list-disc space-y-2 pl-5">
                        <li>
                            Respect des paramètres de confidentialité décrits
                            dans la Politique.
                        </li>
                        <li>
                            Conservation limitée, suppression à la demande et
                            chiffrement en transit.
                        </li>
                    </ul>
                </div>

                <div
                    class="rounded-md border border-white/10 bg-white/5 p-4 text-sm"
                >
                    <p>
                        Avertissement: ce document est un exemple fictif destiné
                        à illustrer une démarche de conformité. Pour une
                        conformité réelle, consultez un expert juridique et les
                        textes officiels de l’AI Act.
                    </p>
                </div>

                <div class="mt-8">
                    <Link href="/" class="underline hover:text-gray-300"
                        >Retour à l’accueil</Link
                    >
                </div>
            </section>
        </main>
    </div>
</template>
