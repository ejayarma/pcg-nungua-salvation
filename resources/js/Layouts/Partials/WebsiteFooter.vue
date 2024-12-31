<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { ref } from 'vue';
import { Mail, Phone, Twitter, Facebook, Linkedin, Instagram, XIcon } from 'lucide-vue-next';

</script>

<template>


    <div class="relative  bg-blue-800 text-slate-50  mt-[-20px] shadow-3xl shadow-inner  ">

        <div
            class="absolute inset-0 h-full w-full bg-transparent bg-[radial-gradient(#2563eb_1px,transparent_1px)] [background-size:16px_16px] [mask-image:radial-gradient(ellipse_50%_50%_at_50%_50%,#000_60%,transparent_100%)]">
        </div>
        <div
            class="container relative grid items-center grid-cols-1 gap-10 p-6 px-4 py-10 mx-auto sm:gap-0 sm:grid-cols-2 lg:grid-cols-4">
            <div class="flex gap-4">
                <img src="@/images/presby-logo.png" class="h-16" alt="Presby Logo">
                <p class="text-sm leading-tight">
                    Presbyterian Church of Ghana <br>
                    Ga Presbytery <br>
                    <!-- Salvation Congregation <br> -->
                    <b>Salvation Congregation</b>
                </p>
            </div>
            <div class="tracking-wider">

                <h4 class="font-semibold">Get Involved</h4>
                <ul>
                    <li>
                        <a href="#" class="block py-1">Events</a>
                    </li>
                    <li>
                        <a href="#" class="block py-1">Give</a>
                    </li>
                    <li>
                        <a href="#" class="block py-1">I'm New</a>
                    </li>
                </ul>
            </div>
            <!-- <div>

                <h4 class="font-semibold">Quick Links</h4>
                <ul >
                    <li>
                        <a href="#" class="block py-1">Gallery</a>
                    </li>
                    <li>
                        <a href="#" class="block py-1">Blog</a>
                    </li>
                    <li>
                        <a href="#" class="block py-1">News</a>
                    </li>
                </ul>
            </div> -->
            <div class="tracking-wider">

                <h4 class="font-semibold">About US</h4>
                <ul>
                    <li>
                        <a href="#" class="block py-1">What we believe</a>
                    </li>
                    <li>
                        <a href="#" class="block py-1">Our Team</a>
                    </li>
                    <li>
                        <a href="#" class="block py-1">Vision & Mission</a>
                    </li>
                </ul>


            </div>
            <div class="tracking-wider">

                <h4 class="font-semibold">Contact US</h4>

                <div class="flex gap-4 mt-2 align-baseline">
                    <Mail class="stroke-1 fill-white stroke-gray-300" :size="24" />

                    <a href="mailto:johndoe@example.com">johndoe@example.com</a>
                </div>

                <div class="flex gap-4 mt-2 align-baseline">

                    <Phone class="stroke-1 fill-white stroke-gray-300" :size="24" />

                    <a href="tel:+02323 253 323 902">02323 253 323 902</a>
                </div>

                <div class="flex gap-6 mt-4 align-baseline">
                    <Facebook class="stroke-1 fill-red-400 stroke-red-400" :size="24" />
                    <Instagram class="stroke-3 stroke-red-400" :size="24" />
                    <Linkedin class="stroke-1 fill-red-400 stroke-red-400" :size="24" />
                </div>
            </div>



        </div>

        <p class="relative py-1 text-xs text-center text-gray-700 bg-white ">
            &copy {{ new Date().getFullYear() }} &nbsp; ● &nbsp;
            Crafted with ❤️ by <a href="https://ejayarma.github.io" target="_blank"
                rel="noopener noreferrer">@ejayarma</a>
        </p>
    </div>
</template>
