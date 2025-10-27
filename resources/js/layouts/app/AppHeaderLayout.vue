<script setup lang="ts">
import 'vue-sonner/style.css';
import AppContent from '@/components/AppContent.vue';
import AppFooter from '@/components/AppFooter.vue';
import AppHeader from '@/components/AppHeader.vue';
import AppShell from '@/components/AppShell.vue';
import { Toaster } from '@/components/ui/sonner';
import type { BreadcrumbItemType } from '@/types';

interface Props {
    breadcrumbs?: BreadcrumbItemType[];
}

withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});
</script>

<template>
    <AppShell variant="header">
        <Toaster position="top-right" :expand="true" rich-colors :toast-options="{
            duration: 3000,
            style: {
                zIndex: '3000',
                fontFamily: 'var(--font-sans)',
                fontSize: 'var(--text-sm)',
            }
        }" />
        <AppHeader :breadcrumbs="breadcrumbs" />
        <AppContent class="mb-8">
            <slot />
        </AppContent>
        <AppFooter />
    </AppShell>
</template>
