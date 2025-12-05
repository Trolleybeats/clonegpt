<script setup lang="ts">
import { SidebarProvider } from '@/components/ui/sidebar';
import { usePage } from '@inertiajs/vue3';

interface Props {
    variant?: 'header' | 'sidebar';
}

defineProps<Props>();

const isOpen = usePage().props.sidebarOpen;
</script>

<template>
    <div
        v-if="variant === 'header'"
        class="flex min-h-screen w-full flex-col"
        style="background-color: #1b1b1e"
    >
        <slot />
    </div>
    <SidebarProvider
        v-else
        :default-open="isOpen"
        style="background-color: #1b1b1e"
    >
        <slot />
    </SidebarProvider>
</template>
