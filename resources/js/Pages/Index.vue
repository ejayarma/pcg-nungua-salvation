<script setup lang="ts">
import WebsiteLayout from '@/Layouts/WebsiteLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { Heart, HandHelping, CirclePlay, Sparkles, MapPin, Search, MessagesSquare, SquarePlay, Lightbulb, Star, Cross, Handshake } from 'lucide-vue-next';

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
                <h1 class="text-2xl tracking-widest sm:text-6xl">S▲LV▲TI●N</h1>
                <!-- <h2 class="mt-2 tracking-widest text-blue-800">
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
                <div class="grid grid-cols-3 bg-white border-[0.1px] shadow-xl shadow-blue-100 rounded-lg">
                    <!-- <div class="inline-flex flex-col items-center gap-4 py-2 m-4 border-blue-200 rounded-md hover:bg-blue-100">
                        <Heart :size="32" />
                        <b>Care</b>
                    </div> -->
                    <div
                        class="inline-flex flex-col items-center gap-4 py-2 m-4 border-blue-200 rounded-md hover:bg-blue-100">
                        <HandHelping :size="32" />
                        <b>Give</b>
                    </div>
                    <div
                        class="inline-flex flex-col items-center gap-4 py-2 m-4 border-blue-200 rounded-md hover:bg-blue-100">
                        <CirclePlay :size="32" />
                        <b>Media</b>
                    </div>
                    <div
                        class="inline-flex flex-col items-center gap-4 py-2 m-4 border-blue-200 rounded-md hover:bg-blue-100">
                        <Sparkles :size="32" />
                        <b>Services</b>
                    </div>
                    <!-- <div
                        class="inline-flex flex-col items-center gap-4 py-2 m-4 border-blue-200 rounded-md hover:bg-blue-100">
                        <MapPin :size="32" />
                        <b>Location</b>
                    </div> -->
                </div>
            </div>


            <div class="container px-4 mx-auto text-center">

                <!-- <div class="text-center"> -->
                <h2 class="text-3xl font-bold text-blue-800">It all starts with faith</h2>
                <p class="mt-4">We are focused on making Jesus Christ known. It's all about Jesus</p>
                <!-- </div> -->

                <div class="grid grid-cols-1 gap-10 mt-6 sm:grid-cols-3">
                    <div class="inline-flex flex-col items-center gap-4 p-10 bg-white rounded-lg shadow-md">
                        <Handshake :size="48" />
                        <h3 class="text-xl font-bold text-blue-800">About Us</h3>
                        <p class="text-sm text-gray-400">Our church is open and friendly with many social activities. To
                            keep our church running smoothly, we have committees.
                        </p>
                    </div>
                    <div class="inline-flex flex-col items-center gap-4 p-10 bg-white rounded-lg shadow-md">
                        <Cross :size="48" />
                        <h3 class="text-xl font-bold text-blue-800">Christ-Centered</h3>
                        <p class="text-sm text-gray-400">We exist to represent Christ to our community and world so that
                            they might see Jesus and realize all that God has planned for them!


                        </p>
                    </div>
                    <div class="inline-flex flex-col items-center gap-4 p-10 bg-white rounded-lg shadow-md">
                        <Lightbulb :size="48" />
                        <h3 class="text-xl font-bold text-blue-800">Testimonials</h3>
                        <p class="text-sm text-gray-400">We invite you to see for yourself how exciting church can be
                            when the focus is simple and people are free to go after God with passion..
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
                                <div class="px-4 py-2 text-blue-800 bg-blue-50 h-1/2">Sunday</div>
                                <div class="px-4 py-2 text-white bg-blue-800 h-1/2">9:00 am</div>
                            </div>
                            <p><b>First Fruits</b> <br>
                                Fasting Service, 1st Sunday of every month
                            </p>
                        </div>
                        <div class="hidden bg-blue-800 rounded-full size-2 sm:inline-block"></div>
                        <div class="inline-flex items-stretch justify-center gap-4 p-4 bg-white rounded-md shadow-xl">
                            <div class="text-center rounded-md text-nowrap overflow-clip">
                                <div class="px-4 py-2 text-blue-800 bg-blue-50 h-1/2">Sunday</div>
                                <div class="px-4 py-2 text-white bg-blue-800 h-1/2">9:00 am</div>
                            </div>
                            <p><b>Sunday Service</b><br>
                                Inspiring, spirit-filled
                                Sunday experience
                            </p>
                        </div>
                        <div class="hidden bg-blue-800 rounded-full size-2 sm:inline-block"> </div>
                        <div class="inline-flex items-stretch justify-center gap-4 p-4 bg-white rounded-md shadow-xl">
                            <div class="text-center rounded-md text-nowrap overflow-clip">
                                <div class="px-4 py-2 text-blue-800 bg-blue-50 h-1/2">Friday</div>
                                <div class="px-4 py-2 text-white bg-blue-800 h-1/2">7:00 pm</div>
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
                    <div class="grid grid-cols-1 gap-0 mt-6 overflow-hidden bg-white sm:grid-cols-2 rounded-3xl">
                        <div class="p-10">
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
                                class="w-full py-2 mt-4 text-xl font-bold text-white bg-blue-800 rounded-full hover:bg-blue-900 focus:bg-blue-700"
                                type="submit">
                                Send
                            </button>
                        </div>
                        <div
                            class="bg-fixed bg-gray-600 bg-no-repeat bg-cover hidden sm:block min-h-[200px] sm:min-h-[400px] bg-blend-overlay bg-church-inside sm:rounded-none rounded-b-3xl ">
                            <div class="relative size-full">
                                <!-- <div
                                    class="absolute bottom-0 right-0 p-4 text-white rounded sm:right-5 sm:bottom-5 sm:size-fit size-full bg-white/25 text-end backdrop-blur-sm">
                                    <h2 class="text-2xl font-bold text-red-400">Location</h2>
                                    <p>Nungua</p>
                                    <p>
                                        <b>Cocoa beach road</b>
                                    </p>

                                </div> -->
                            </div>
                        </div>

                        <div id="map" class="">
                            <iframe src="https://snazzymaps.com/embed/671861" width="100%" height="400px"
                                style="border:none;"></iframe>
                        </div>
                        <div class="p-10">

                            <div class="flex flex-col justify-center h-full gap-4">
                                <h3 class="text-4xl font-bold">Join Us On Sunday</h3>
                                <p class="font-semibold">Nungua, Off the Cocobeach Road</p>

                                <p>When you come to Salvation Presby Church, you can expect to be welcomed into a
                                    friendly
                                    environment
                                    by people who are genuinely excited to see you. Our gatherings last about 1 hour and
                                    15
                                    minutes, and are filled with gospel-centered music and teachings from the Bible.</p>
                            </div>
                        </div>
                    </div>
                </div>

            </section>
            <!-- CONTACT SECTION END -->

        </div>
    </WebsiteLayout>
</template>
