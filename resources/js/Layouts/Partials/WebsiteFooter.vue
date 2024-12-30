<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { ref } from 'vue';
import { Mail, Phone, Twitter, Facebook, Linkedin } from 'lucide-vue-next';

</script>

<template>
    <div class="bg-white ">
        <div class="container grid grid-cols-4 px-4 py-10 mx-auto">
            <div class="flex gap-4">
                <img src="@/images/presby-logo.png" class="h-20" alt="Presby Logo">
                <p class="text-sm leading-tight">
                Presbyterian Church of Ghana <br>
                Ga Presbytery <br>
                Salvation Congregation <br>
                <b>Nungua</b>
            </p>
            </div>
            <div>
                <div class="flex gap-4 align-baseline">
                    <i class="p-1 text-center bg-gray-300 rounded-full size-6">
                        <Mail class="stroke-1 fill-white stroke-gray-300" :size="16" />
                    </i>

                    <a href="mailto:johndoe@example.com">johndoe@example.com</a>
                </div>
                <h4 class="mt-4 font-semibold">Get Involved</h4>
                <ul class="mt-1">
                    <li>
                        <a href="#">Events</a>
                    </li>
                    <li>
                        <a href="#">Give</a>
                    </li>
                    <li>
                        <a href="#">I'm New</a>
                    </li>
                </ul>
            </div>
            <div>
                <div class="flex gap-4 align-baseline">

                    <i class="p-1 text-center bg-gray-300 rounded-full size-6">
                        <Phone class="stroke-1 fill-white stroke-gray-300" :size="16" />
                    </i>

                    <!-- <a href="tel:+"></a> -->
                    <a href="#">02323 253 323 902</a>
                </div>
                <h4 class="mt-4 font-semibold">Quick Links</h4>
                <ul class="mt-1">
                    <li>
                        <a href="#">Gallery</a>
                    </li>
                    <li>
                        <a href="#">Blog</a>
                    </li>
                    <li>
                        <a href="#">News</a>
                    </li>
                </ul>
            </div>
            <div>
                <div class="flex gap-4 align-baseline">
                    <i class="p-1 text-center bg-blue-700 rounded-full size-6">
                        <Facebook class="stroke-1 fill-white stroke-gray-300" :size="16" />
                    </i>
                    <i class="p-1 text-center rounded-full bg-cyan-400 size-6">
                        <Twitter class="stroke-1 fill-white stroke-gray-300" :size="16" />
                    </i>
                    <i class="p-1 text-center bg-blue-500 rounded-full size-6">
                        <Linkedin class="stroke-1 fill-white stroke-gray-300" :size="16" />
                    </i>

                </div>
                <h4 class="mt-4 font-semibold">About US</h4>
                <ul class="mt-1">
                    <li>
                        <a href="#">What we believe</a>
                    </li>
                    <li>
                        <a href="#">Our Team</a>
                    </li>
                    <li>
                        <a href="#">Vision & Mission</a>
                    </li>
                </ul>
            </div>

        </div>
    </div>
</template>
