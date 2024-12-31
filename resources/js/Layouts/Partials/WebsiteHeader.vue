<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { ref } from 'vue';
import { Menu, X } from 'lucide-vue-next';


const showMobileNav = ref<Boolean>(false);

function toggleNav() {
    showMobileNav.value = !showMobileNav.value;

    if (showMobileNav.value) {
        document.body.classList.add('no-scroll'); // Disable scroll
    } else {
        document.body.classList.remove('no-scroll'); // Enable scroll
    }
}

</script>

<template>
    <!-- <div class="fixed"> -->
    <div class="container relative top-0 flex justify-between w-full p-4 mx-auto text-white">
        <a href="#" class="inline-flex gap-2">
            <img src="@/images/presby-logo.png" class="h-10" alt="Presby Logo">
            <p class="text-sm leading-tight">
                Salvation Presby Church <br>
                <b>Nungua</b>
            </p>
        </a>
        <button @click="toggleNav">
            <Menu class="sm:hidden" />
        </button>
        <!-- <X /> -->

        <!-- MOBILE NAV START -->
        <transition enter-active-class="animate__animated animate__fadeInRight"
            leave-active-class="animate__animated animate__fadeOutRight">
            <nav v-if="showMobileNav"
                class="fixed top-0 right-0 z-10 w-full h-screen gap-4 overflow-hidden text-black bg-slate-50">
                <div class="flex justify-between p-4">
                    <a href="#" class="inline-flex gap-2">
                        <img src="@/images/presby-logo.png" class="h-10" alt="Presby Logo">
                        <p class="text-sm leading-tight">
                            Salvation Presby Church <br>
                            <b>Nungua</b>
                        </p>
                    </a>
                    <button @click="toggleNav">
                        <X />
                    </button>
                </div>
                <ul class="flex flex-col items-center text-center justify-stretch *:w-full">
                    <li><a class="block py-4" href="#">About Us</a></li>
                    <li><a class="block py-4" href="#">Services</a></li>
                    <li><a class="block py-4" href="#">Events</a></li>
                    <li><a class="block py-4" href="#">Contact</a></li>
                    <li><a class="block py-4" href="#">Give</a></li>
                </ul>
            </nav>
        </transition>
        <!-- MOBILE NAV END -->

        <!-- DESKTOP NAV START -->
        <nav class="hidden sm:inline-block">
            <ul class="items-center justify-between gap-4 sm:inline-flex *:w-full text-nowrap">
                <li><a class="block" href="#">About Us</a></li>
                <li><a class="block" href="#">Services</a></li>
                <li><a class="block" href="#">Events</a></li>
                <li><a class="block" href="#">Contact</a></li>
                <li><a class="block" href="#">Give</a></li>
            </ul>
        </nav>
        <!-- DESKTOP NAV END -->
    </div>
    <!-- </div> -->

</template>
