<script setup lang="ts">
import WebsiteLayout from '@/Layouts/WebsiteLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { Heart, HandHelping, CirclePlay, Sparkles, MapPin, Search, MessagesSquare, SquarePlay } from 'lucide-vue-next';

defineProps<{
    canLogin?: boolean;
    canRegister?: boolean;
    laravelVersion: string;
    phpVersion: string;
}>();

</script>

<template>

    <Head title="Welcome" />
    <WebsiteLayout>
        <div class="container grid px-4 py-24 mx-auto text-center text-white/90 place-items-center">
            <!-- <div class="container grid min-h-screen px-4 py-24 mx-auto text-center text-white/90 place-items-center"> -->
            <div>
                <h1 class="text-2xl tracking-widest sm:text-6xl">SALVATION</h1>
                <!-- <h2 class="mt-2 tracking-widest text-red-500">
                    ●
                </h2> -->
                <p class="my-4 text-lg tracking-wide text-red-400">Through the blood of Jesus
                </p>

                <!-- <p class="mt-6">
                    <a href="#" class="px-10 py-2 text-lg font-medium border rounded-full">Contact</a>
                </p> -->
            </div>
        </div>

        <div class="bg-blue-50">

            <div class="container relative px-4 mx-auto -top-10">
                <div class="grid grid-cols-5 bg-white border-[0.1px] shadow-xl shadow-blue-100 rounded-lg">
                    <div class="inline-flex flex-col items-center gap-4 p-4 border-blue-200 rounded-md hover:border-2">
                        <Heart :size="32" />
                        <b>Care</b>
                    </div>
                    <div class="inline-flex flex-col items-center gap-4 p-4 border-blue-200 rounded-md hover:border-2">
                        <HandHelping :size="32" />
                        <b>Give</b>
                    </div>
                    <div class="inline-flex flex-col items-center gap-4 p-4 border-blue-200 rounded-md hover:border-2">
                        <CirclePlay :size="32" />
                        <b>Media</b>
                    </div>
                    <div class="inline-flex flex-col items-center gap-4 p-4 border-blue-200 rounded-md hover:border-2">
                        <Sparkles :size="32" />
                        <b>Services</b>
                    </div>
                    <div class="inline-flex flex-col items-center gap-4 p-4 border-blue-200 rounded-md hover:border-2">
                        <MapPin :size="32" />
                        <b>Location</b>
                    </div>
                </div>
            </div>


            <div class="container px-4 mx-auto text-center">

                <!-- <div class="text-center"> -->
                <h2 class="text-3xl font-bold text-blue-800">It all starts with faith</h2>
                <p class="mt-4">Lorem ipsum, dolor sit amet consectetur adipisicing elit. <br> Enim ipsa magni quia
                    accusamus, corporis
                    libero molestiae.</p>
                <!-- </div> -->

                <div class="grid grid-cols-3 gap-10 mt-6">
                    <div class="inline-flex flex-col items-center gap-4 p-10 bg-white rounded-lg shadow-md">
                        <Search :size="48" />
                        <h3 class="text-xl font-bold text-blue-800">About Us</h3>
                        <p class="text-sm text-gray-400">Lorem ipsum dolor sit amet laboriosam repudiandae dolorum quas
                            alias officia, facere
                            veritatis.
                        </p>
                    </div>
                    <div class="inline-flex flex-col items-center gap-4 p-10 bg-white rounded-lg shadow-md">
                        <SquarePlay :size="48" />
                        <h3 class="text-xl font-bold text-blue-800">Media</h3>
                        <p class="text-sm text-gray-400">Lorem ipsum dolor sit amet laboriosam repudiandae dolorum quas
                            alias officia, facere
                            veritatis.
                        </p>
                    </div>
                    <div class="inline-flex flex-col items-center gap-4 p-10 bg-white rounded-lg shadow-md">
                        <MessagesSquare :size="48" />
                        <h3 class="text-xl font-bold text-blue-800">Testimonials</h3>
                        <p class="text-sm text-gray-400">Lorem ipsum dolor sit amet laboriosam repudiandae dolorum quas
                            alias officia, facere
                            veritatis.
                        </p>
                    </div>
                </div>
            </div>
            <!-- EVENTS SECTION START -->
            <section class="py-16 mt-10 bg-white">
                <div class="container px-4 mx-auto">
                    <h2 class="text-3xl font-bold text-center text-blue-800">Services</h2>
                    <div class="flex flex-col items-center justify-around gap-4 mt-6 lg:flex-row">
                        <div class="inline-flex items-stretch justify-center gap-4 p-4 bg-white rounded-md shadow-xl ">
                            <div class="text-center rounded-md text-nowrap overflow-clip">
                                <div class="px-4 py-2 text-red-500 bg-red-50 h-1/2">Feb 12</div>
                                <div class="px-4 py-2 text-white bg-red-500 h-1/2">7:00 am</div>
                            </div>
                            <p><b>First Fruits</b> <br>
                                Monthly Prayer and Fasting Service
                            </p>
                        </div>
                        <div class="w-0.5 h-10 bg-gray-400"></div>
                        <div class="inline-flex items-stretch justify-center gap-4 p-4 bg-white rounded-md shadow-xl">
                            <div class="text-center rounded-md text-nowrap overflow-clip">
                                <div class="px-4 py-2 text-red-500 bg-red-50 h-1/2">Feb 12</div>
                                <div class="px-4 py-2 text-white bg-red-500 h-1/2">9:00 am</div>
                            </div>
                            <p><b>Sunday Service</b><br>
                                Inspiring, spirit-filled
                                Sunday experience
                            </p>
                        </div>
                        <div class="w-0.5 h-10 bg-gray-400"></div>
                        <div class="inline-flex items-stretch justify-center gap-4 p-4 bg-white rounded-md shadow-xl">
                            <div class="text-center rounded-md text-nowrap overflow-clip">
                                <div class="px-4 py-2 text-red-500 bg-red-50 h-1/2">Feb 12</div>
                                <div class="px-4 py-2 text-white bg-red-500 h-1/2">7:00 pm</div>
                            </div>
                            <p><b>Salvation Hour</b> <br>
                                Energizing and power-packed prayers.
                            </p>
                        </div>
                    </div>
                </div>

            </section>
            <!-- EVENTS SECTION END -->

            <!-- CONTACT SECTION START -->
            <section class="py-16 mt-10 ">
                <div class="container px-4 mx-auto">
                    <h2 class="text-3xl font-bold text-center text-blue-800">Contact Us</h2>
                    <div class="flex mt-6">
                        <div class="w-1/2 p-4 bg-white rounded-3xl">
                            <div class="">
                                <label class="block form-label" for="name">Your name</label>
                                <input class="form-input" name="name" type="text">
                            </div>
                            <div class="mt-4">
                                <label class="block form-label" for="email">Email</label>
                                <input class="form-input" name="email" type="email">
                            </div>
                            <div class="mt-4">
                                <label class="block form-label" for="phone">Phone</label>
                                <input class="form-input" name="phone" type="text">
                            </div>
                            <div class="mt-4">
                                <label class="block form-label" for="message">Message</label>
                                <textarea class="form-input" name="message" id="message" rows="5"></textarea>
                            </div>
                            <button
                                class="w-full py-2 mt-4 text-xl font-bold text-white bg-red-500 rounded-full hover:bg-red-600 focus:bg-red-700"
                                type="submit">
                                Send
                            </button>
                        </div>
                        <div id="map" class="w-1/2 h-full">
                            <gmp-map class="h-[500px] pl-3 rounded-2xl" center="37.4220656,-122.0840897" zoom="10"
                                map-id="PCG-SALVATION-NUNGUA">
                            </gmp-map>
                        </div>
                    </div>
                </div>

            </section>
            <!-- CONTACT SECTION END -->





        </div>
    </WebsiteLayout>
</template>
