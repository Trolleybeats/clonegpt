<script setup lang="ts">
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import Cookie from '@/components/Cookie.vue';
import Faq from '@/components/Faq.vue';
import Pricing from '@/components/Pricing.vue';
import Temoin from '@/components/Temoin.vue';
import { Button } from '@/components/ui/button';
import { dashboard, login, register } from '@/routes';
import { Head, Link } from '@inertiajs/vue3';

withDefaults(
    defineProps<{
        canRegister: boolean;
    }>(),
    {
        canRegister: true,
    },
);
</script>

<template>
    <Head title="Welcome">
        <link rel="preconnect" href="https://rsms.me/" />
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
        <link
            rel="apple-touch-icon"
            sizes="180x180"
            href="/apple-touch-icon.png"
        />
        <link
            rel="icon"
            type="image/png"
            sizes="32x32"
            href="/favicon-32x32.png"
        />
        <link
            rel="icon"
            type="image/png"
            sizes="16x16"
            href="/favicon-16x16.png"
        />
        <link rel="manifest" href="/site.webmanifest" />
    </Head>
    <div
        class="flex min-h-screen flex-col items-center bg-[#1B1B1E] p-6 text-[#1b1b18] lg:justify-center lg:p-8 dark:bg-[#0a0a0a]"
    >
        <Cookie class="absolute right-0 bottom-0 left-0" />
        <header
            class="mb-6 w-full max-w-[335px] text-sm not-has-[nav]:hidden lg:max-w-4xl"
        >
            <nav class="flex items-center justify-end gap-4">
                <Link
                    v-if="$page.props.auth.user"
                    :href="dashboard()"
                    class="inline-block rounded-sm border border-white px-5 py-1.5 text-sm leading-normal text-white hover:border-[#1915014a] dark:border-[#3E3E3A] dark:text-[#EDEDEC] dark:hover:border-[#62605b]"
                >
                    Dashboard
                </Link>
                <template v-else>
                    <Link
                        :href="login()"
                        class="inline-block rounded-sm border border-transparent px-5 py-1.5 text-sm leading-normal text-white hover:border-[#19140035] dark:text-[#EDEDEC] dark:hover:border-[#3E3E3A]"
                    >
                        Se connecter
                    </Link>
                    <Link
                        v-if="canRegister"
                        :href="register()"
                        class="inline-block rounded-sm border border-white px-5 py-1.5 text-sm leading-normal text-white hover:border-[#1915014a] dark:border-[#3E3E3A] dark:text-[#EDEDEC] dark:hover:border-[#62605b]"
                    >
                        S'inscrire
                    </Link>
                </template>
            </nav>
        </header>
        <main class="w-full max-w-md text-center lg:max-w-2xl">
            <!--Hero-->
            <AppLogoIcon class="mx-auto mb-6 h-16 w-16 text-white" />
            <h1 class="mb-6 text-4xl leading-tight text-white md:text-5xl">
                Bienvenue sur RunAI
            </h1>
            <p class="mb-6 text-lg text-white">
                Votre assistant IA personnel pour des programmes de course à
                pied personnalisés.
            </p>
            <Button
                as="a"
                href="/conversations/create"
                class="mb-4 self-end bg-[#FF3B30] text-white hover:bg-[#C8FF2E] hover:text-black"
            >
                Nouvelle Conversation
            </Button>

            <!--Section Fonctionnalités-->
            <article class="mb-6 text-left text-white">
                <h2 class="mb-4 text-2xl">Fonctionnalités</h2>
                <ul class="list-disc space-y-2 pl-5">
                    <li>
                        Discussions illimitées avec l'IA pour des conseils
                        personnalisés.
                    </li>
                    <li>
                        Génération de plans d'entraînement adaptés à vos
                        objectifs.
                    </li>
                    <li>Suivi de vos progrès et ajustements en temps réel.</li>
                    <li>Interface conviviale et facile à utiliser.</li>
                </ul>
            </article>

            <!--Section Tarification-->
            <Pricing />

            <!--Témoignages-->
            <Temoin />

            <!--FAQ-->
            <Faq />

            <!--Pied de page-->
            <footer class="mt-12 text-sm text-white">
                <p>
                    &copy; {{ new Date().getFullYear() }} RunAI. Tous droits
                    réservés.
                </p>

                <a href="/Politique" class="ml-4 underline hover:text-gray-300"
                    >Politique de confidentialité</a
                >
                <a href="/Mentions" class="ml-4 underline hover:text-gray-300"
                    >Mentions légales</a
                >
                <a href="/Act" class="ml-4 underline hover:text-gray-300"
                    >AI Act</a
                >
            </footer>
        </main>
    </div>
</template>
