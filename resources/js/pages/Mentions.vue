<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
</script>

<template>
    <Head>
        <title>Mentions légales - CloneGPT</title>
        <meta
            name="description"
            content="Consultez les mentions légales de CloneGPT, incluant l'identification de l'éditeur, l'hébergeur, et les informations sur l'utilisation des modèles d'IA."
        />
    </Head>
    <div class="bg-[#1B1B1E]">
        <section class="mx-auto prose max-w-3xl py-10">
            <h1>Mentions légales</h1>

            <h2>Identification de l'éditeur</h2>
            <p>
                Éditeur: CloneGPT (fictif)<br />
                Société: CloneGPT SAS au capital de 10 000 €<br />
                RCS: Paris 123 456 789<br />
                Siège social: 10 rue de l'IA, 75000 Paris, France<br />
                TVA intracommunautaire: FR12 123456789<br />
                Contact: privacy@clonegpt.example (fictif)
            </p>

            <h2>Hébergeur</h2>
            <p>
                Hébergeur: Laragon Cloud (fictif)<br />
                Adresse: 1, Avenue des Serveurs, 69000 Lyon, France<br />
                Téléphone: +33 1 23 45 67 89
            </p>

            <h2>Directeur de publication</h2>
            <p>Directeur de publication: Jane Doe, CEO</p>

            <h2>Transparence et interactions avec une IA</h2>
            <p>
                Vous interagissez avec un système d'intelligence artificielle.
                Les réponses peuvent contenir des erreurs ou des approximations
                (hallucinations). Ce service ne remplace pas l'avis d'un
                professionnel.
            </p>

            <h2>Modèles et services utilisés</h2>
            <p>
                Le service peut s'appuyer sur des modèles de langage (LLM)
                fournis par des API tierces. Les noms et versions des modèles
                utilisés peuvent évoluer. Si applicable, cette information est
                affichée dans l'interface au moment de l'interaction.
            </p>

            <h2>Utilisation des conversations</h2>
            <p>
                Les conversations peuvent être utilisées afin d'améliorer le
                service (si l'option est activée). Lorsque cette option est
                active, nous mettons en place des mesures de minimisation et
                d'anonymisation lorsque c'est possible.
            </p>

            <h2>Propriété intellectuelle</h2>
            <p>
                Sauf mention contraire, les contenus du site sont la propriété
                de l'éditeur. Toute reproduction, représentation ou adaptation
                non autorisée est interdite.
            </p>
            <div class="mt-8">
                <Link href="/" class="text-white underline hover:text-gray-300"
                    >Retour à l’accueil</Link
                >
            </div>
        </section>
    </div>
</template>

<style scoped>
h1,
h2,
p {
    color: white;
}
.prose {
    line-height: 1.7;
}
.prose h1 {
    font-size: 2rem;
    margin-bottom: 1rem;
}
.prose h2 {
    font-size: 1.25rem;
    margin-top: 1.5rem;
}
</style>
