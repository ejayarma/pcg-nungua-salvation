<script setup lang="ts">
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import WebsiteHeader from '@/Layouts/Partials/WebsiteHeader.vue';
import WebsiteFooter from '@/Layouts/Partials/WebsiteFooter.vue';
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="min-h-screen bg-fixed bg-no-repeat bg-cover bg-blend-overlay bg-hero-pattern dark:bg-gray-900 text-black/50 dark:text-white/50">
        <header>
            <WebsiteHeader />
        </header>
        <main>
            <slot />
        </main>
        <footer>
            <WebsiteFooter />
        </footer>
    </div>
</template>
