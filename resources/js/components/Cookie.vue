<script setup>
import { ref } from 'vue';

const KEY = 'cookieConsent';

const getCookieConsent = () => {
    const ls = localStorage.getItem(KEY);
    if (ls === 'accepted' || ls === 'rejected') return ls;
    const m = document.cookie.match(
        /(?:^|; )cookie_consent=(accepted|rejected)/,
    );
    const val = m?.[1] || null;
    if (val) localStorage.setItem(KEY, val);
    return val;
};

const show = ref(!getCookieConsent());

const accept = () => {
    localStorage.setItem(KEY, 'accepted');
    show.value = false;
    console.log('Cookies accepted');
};

const reject = () => {
    localStorage.setItem(KEY, 'rejected');
    show.value = false;
    console.log('Cookies rejected');
};
</script>

<template>
    <section
        v-if="show"
        aria-live="polite"
        class="fixed inset-x-0 bottom-0 z-50"
    >
        <div class="mx-auto max-w-5xl p-4">
            <div
                role="dialog"
                aria-label="Bandeau de consentement aux cookies"
                class="rounded-lg border border-[#2A2A2F] bg-[#1B1B1E] p-4 text-white shadow-2xl"
            >
                <div
                    class="flex flex-col items-start justify-between gap-4 md:flex-row md:items-center"
                >
                    <div class="space-y-1">
                        <h4 class="text-base font-semibold">
                            Nous utilisons des cookies
                        </h4>
                        <p class="text-sm text-[#C5C5C9]">
                            Nous utilisons des cookies essentiels et de mesure
                            d’audience pour améliorer votre expérience. Vous
                            pouvez accepter ou refuser.
                        </p>
                    </div>
                    <div
                        class="flex w-full flex-none items-center gap-2 md:w-auto"
                    >
                        <button
                            @click="reject"
                            class="flex-1 rounded-md bg-[#C8FF2E] px-4 py-2 text-sm font-medium text-black md:flex-none"
                        >
                            Refuser
                        </button>
                        <button
                            @click="accept"
                            class="flex-1 rounded-md bg-[#C8FF2E] px-4 py-2 text-sm font-medium text-black md:flex-none"
                        >
                            Accepter
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>
