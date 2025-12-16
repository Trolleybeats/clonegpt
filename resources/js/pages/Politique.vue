<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
</script>

<template>
    <Head>
        <title>Politique de confidentialité - CloneGPT</title>
        <meta
            name="description"
            content="Découvrez la politique de confidentialité de CloneGPT, détaillant la collecte, l'utilisation et la protection de vos données personnelles."
        />
    </Head>
    <div class="bg-[#1B1B1E]">
        <section class="mx-auto prose max-w-3xl py-10">
            <h1>Politique de confidentialité</h1>

            <h2>Types de données collectées</h2>
            <p>
                - Données de compte (nom, email, identifiant).<br />
                - Données de journalisation (IP, logs techniques,
                métadonnées).<br />
                - Contenus des conversations et prompts envoyés au service (si
                applicable).<br />
                - Cookies ou technologies similaires pour la session et les
                préférences.
            </p>

            <h2>Finalités du traitement</h2>
            <p>
                - Fournir le service et répondre aux requêtes.<br />
                - Améliorer la qualité des réponses et l'expérience
                utilisateur.<br />
                - Assurer la sécurité, la prévention des abus et la conformité
                légale.<br />
                - Statistiques et analyses agrégées (sans identification
                directe).
            </p>

            <h2>Base légale</h2>
            <p>
                - Consentement: lorsque vous acceptez certaines fonctionnalités
                (ex: amélioration du service via vos données).<br />
                - Exécution du contrat: fourniture du service principal.<br />
                - Intérêt légitime: sécurité, prévention des abus, amélioration
                technique.<br />
                - Obligation légale: conservation ou communication requise par
                la loi.
            </p>

            <h2>Durée de conservation</h2>
            <p>
                Les données sont conservées pendant la durée nécessaire aux
                finalités ci-dessus, puis supprimées ou anonymisées. Les durées
                peuvent varier selon le type de données et les obligations
                légales.
            </p>

            <h2>Droits des utilisateurs</h2>
            <p>
                Vous disposez des droits d'accès, rectification, suppression,
                opposition, limitation et portabilité. Pour exercer vos droits,
                contactez-nous à l'adresse email suivante:
                privacy@clonegpt.example (fictif).
            </p>

            <h2>Transferts hors UE</h2>
            <p>
                Si des API de LLM ou services tiers traitent les données hors de
                l'UE, nous cherchons à encadrer ces transferts via des garanties
                adéquates (ex: clauses contractuelles types) et à minimiser les
                données envoyées.
            </p>

            <h2>Conformité AI Act (information, transparence)</h2>
            <p>
                Nous nous engageons à respecter les exigences de transparence:
                vous êtes informé que vous interagissez avec une IA. Nous
                affichons des avertissements sur les limites (hallucinations,
                erreurs possibles). Ce service ne remplace pas l'avis d'un
                professionnel.
            </p>

            <h2>Informations sur les modèles utilisés</h2>
            <p>
                Les modèles (LLM) et leurs versions peuvent changer dans le
                temps. Lorsque possible, les informations pertinentes sont
                communiquées dans l'interface (nom du modèle, éditeur).
            </p>

            <h2>Utilisation des conversations pour améliorer le service</h2>
            <p>
                Si vous y consentez, certaines conversations peuvent être
                utilisées pour améliorer le service. Nous appliquons des
                pratiques de minimisation et d'anonymisation lorsque possible,
                et vous pouvez retirer votre consentement.
            </p>

            <h2>Contact</h2>
            <p>
                Pour toute question, écrivez-nous à privacy@clonegpt.example
                (fictif).
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
