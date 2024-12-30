<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { ref } from 'vue';
import { Menu, X } from 'lucide-vue-next';

</script>

<template>
    <!-- <div class="fixed"> -->
    <div class="container sticky top-0 flex justify-between w-full p-4 mx-auto text-white">
        <a href="#" class="inline-flex gap-2">
            <img src="@/images/presby-logo.png" class="h-10" alt="Presby Logo">
            <p class="text-sm leading-tight">
                Salvation Presbyterian Church <br>
                <b>Nungua</b>
            </p>
        </a>
        <Menu class="sm:hidden" />
        <!-- <X /> -->
        <div class="items-center justify-between hidden gap-4 sm:inline-flex">
            <a href="#">About Us</a>
            <a href="#">Services</a>
            <a href="#">Events</a>
            <a href="#">Contact</a>
            <a href="#">Give</a>
        </div>
    </div>
    <!-- </div> -->

</template>
