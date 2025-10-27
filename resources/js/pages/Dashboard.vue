<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import Button from '@/components/ui/button/Button.vue';
import { type BreadcrumbItem, Kegiatan } from '@/types';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed, onMounted, onUnmounted, ref } from 'vue';
import { Accordion, AccordionContent, AccordionItem, AccordionTrigger } from "@/components/ui/accordion"
import { Card, CardHeader, CardContent, CardTitle, CardDescription, CardFooter } from '@/components/ui/card';
import Autoplay from "embla-carousel-autoplay";
import { Carousel, CarouselContent, CarouselItem, CarouselNext, CarouselPrevious } from "@/components/ui/carousel"

const plugin = Autoplay({
    delay: 2000,
    stopOnMouseEnter: true,
    stopOnInteraction: false,
})

// define props
const props = defineProps<{
    kegiatan: Kegiatan[];
    waktu: Date | string;
}>();

// CTA Link handler
const ctaLink = computed(() => {
    return auth.value.user ? '/terms' : '/login';
});

// Add computed property to filter kegiatan
const filteredKegiatan = computed(() => {
    if (!auth.value.user) return [];

    return props.kegiatan.filter(item => {
        // Always show fakultas level activities
        if (item.ruang_lingkup === 'fakultas') {
            return true;
        }
        // Show program studi level activities only for matching program studi
        if (item.ruang_lingkup === 'program studi') {
            return item.id_program_studi === auth.value.user.id_program_studi;
        }
        return false;
    });
});

// Countdown timer logic
const currentTime = ref(new Date());
let interval: number | null = null;

const timeRemaining = computed(() => {
    const target = new Date(props.waktu);
    const now = currentTime.value;
    const diff = target.getTime() - now.getTime();

    if (diff <= 0) {
        return { days: 0, hours: 0, minutes: 0, seconds: 0, expired: true };
    }

    const days = Math.floor(diff / (1000 * 60 * 60 * 24));
    const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
    const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
    const seconds = Math.floor((diff % (1000 * 60)) / 1000);

    return { days, hours, minutes, seconds, expired: false };
});

// Add a function to calculate time difference for individual activities
const getTimeUntilStart = (startTime: Date) => {
    const target = new Date(startTime);
    const now = currentTime.value;
    const diff = target.getTime() - now.getTime();

    if (diff <= 0) {
        return { text: "Sedang berlangsung", expired: true };
    }

    const days = Math.floor(diff / (1000 * 60 * 60 * 24));
    const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
    const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));

    let text = "";
    if (days > 0) {
        text = `${days} hari ${hours} jam`;
    } else if (hours > 0) {
        text = `${hours} jam ${minutes} menit`;
    } else {
        text = `${minutes} menit`;
    }

    return { text, expired: false };
};

// Helper function to format time values
const formatTime = (time: number) => {
    return time.toString().padStart(2, '0');
};

// Set up interval to update current time every second
onMounted(() => {
    interval = setInterval(() => {
        currentTime.value = new Date();
    }, 1000);
});

onUnmounted(() => {
    if (interval) {
        clearInterval(interval);
    }
});

// Accordion FAQ data
const defaultValue = "item-1"
const accordionItems = [
    { value: "item-1", title: "Is it accessible?", content: "Yes. It adheres to the WAI-ARIA design pattern." },
    { value: "item-2", title: "Is it unstyled?", content: "Yes. It's unstyled by default, giving you freedom over the look and feel." },
    { value: "item-3", title: "Can it be animated?", content: "Yes! You can use the transition prop to configure the animation." },
]

// Page title and breadcrumbs
const page = usePage();
const auth = computed(() => page.props.auth);
const title = auth.value.user ? 'Beranda' : 'Selamat Datang';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Beranda',
        href: '/dashboard',
    },
];
</script>

<template>

    <Head :title="title" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-8 overflow-hidden">
            <!-- Countdown timer -->
            <div class="relative min-h-[90vh] flex flex-1 justify-center items-center">
                <!-- Content with relative positioning and higher z-index -->
                <Carousel class="absolute w-full saturate-0 md:saturate-0 md:backdrop-blur"
                    :plugins="[plugin]" @mouseenter="plugin.stop" @mouseleave="[plugin.reset(), plugin.play()]">
                    <CarouselContent>
                        <CarouselItem>
                            <div class="p-1 flex items-center justify-center">
                                <Card class="w-full">
                                    <CardContent class="flex items-center justify-center">
                                        <img src="/images/20250603_181544.jpg" alt="Placeholder"
                                            class="w-full h-[90vh] object-cover" />
                                    </CardContent>
                                </Card>
                            </div>
                        </CarouselItem>
                        <CarouselItem>
                            <div class="p-1 flex items-center justify-center">
                                <Card class="w-full">
                                    <CardContent class="flex items-center justify-center">
                                        <img src="/images/20250603_181816.jpg" alt="Placeholder"
                                            class="w-full h-[90vh] object-cover" />
                                    </CardContent>
                                </Card>
                            </div>
                        </CarouselItem>
                        <CarouselItem>
                            <div class="p-1 flex items-center justify-center">
                                <Card class="w-full">
                                    <CardContent class="flex items-center justify-center">
                                        <img src="/images/20250603_185507.jpg" alt="Placeholder"
                                            class="w-full h-[90vh] object-cover" />
                                    </CardContent>
                                </Card>
                            </div>
                        </CarouselItem>
                    </CarouselContent>
                    <CarouselPrevious />
                    <CarouselNext />
                </Carousel>
                <div class="relative z-10 px-4 space-y-4 md:space-y-6 flex flex-col items-center justify-center">
                    <!-- Header -->
                    <div class="stroke-black-500 text-center">
                        <h2 class="text-xl md:text-2xl lg:text-3xl font-bold text-white mb-2">
                            PEMIRA FMIPA
                        </h2>
                        <p class="text-white font-medium text-sm md:text-base max-w-lg">
                            Pemilihan Umum Raya Fakultas Matematika dan Ilmu Pengetahuan Alam akan dimulai dalam
                        </p>
                    </div>

                    <!-- Countdown Display -->
                    <div class="flex justify-center space-x-4 items-start">
                        <!-- Days -->
                        <div class="text-center">
                            <p class="text-4xl md:text-5xl lg:text-6xl font-bold text-white">
                                {{ formatTime(timeRemaining.days) }}
                            </p>
                            <p class="text-sm lg:text-base text-white font-medium mt-2">
                                Hari
                            </p>
                        </div>

                        <div class="pt-2 text-xl md:text-2xl lg:text-3xl font-bold text-white">:</div>

                        <!-- Hours -->
                        <div class="text-center">
                            <p class="text-4xl md:text-5xl lg:text-6xl font-bold text-white">
                                {{ formatTime(timeRemaining.hours) }}
                            </p>
                            <p class="text-sm lg:text-base text-white font-medium mt-2">
                                Jam
                            </p>
                        </div>

                        <div class="pt-2 text-xl md:text-2xl lg:text-3xl font-bold text-white">:</div>

                        <!-- Minutes -->
                        <div class="text-center">
                            <p class="text-4xl md:text-5xl lg:text-6xl font-bold text-white">
                                {{ formatTime(timeRemaining.minutes) }}
                            </p>
                            <p class="text-sm lg:text-base text-white font-medium mt-2">
                                Menit
                            </p>
                        </div>

                        <div class="pt-2 text-xl md:text-2xl lg:text-3xl font-bold text-white">:</div>

                        <!-- Seconds -->
                        <div class="text-center">
                            <p class="text-4xl md:text-5xl lg:text-6xl font-bold text-white">
                                {{ formatTime(timeRemaining.seconds) }}
                            </p>
                            <p class="text-sm lg:text-base text-white font-medium mt-2">
                                Detik
                            </p>
                        </div>
                    </div>

                    <!-- Status Message -->
                    <Button variant="outline" size="lg" class="text-base">
                        <Link :href="ctaLink">
                        Mulai Sekarang!
                        </Link>
                    </Button>
                </div>
            </div>

            <!-- List Kegiatan -->
            <div v-if="auth.user && filteredKegiatan.length > 0">
                <h1 class="text-lg md:text-xl lg:text-2xl mt-2 mb-6 font-bold text-center">Kegiatan Mendatang</h1>
                <div class="max-w-7xl mb-6 w-full grid place-self-center auto-rows-min gap-4 md:grid-cols-2">
                    <Card v-for="item in filteredKegiatan" :key="item.id">
                        <CardHeader>
                            <img :src="`/storage/${item.foto}`" alt="" class="w-full h-64 object-cover rounded-md">
                        </CardHeader>
                        <CardContent class="space-y-2">
                            <CardTitle class="text-lg md:text-xl">{{ item.nama }}</CardTitle>
                            <CardDescription>
                                {{ getTimeUntilStart(item.waktu_mulai).expired ?
                                    getTimeUntilStart(item.waktu_mulai).text :
                                    `Dimulai dalam ${getTimeUntilStart(item.waktu_mulai).text}`
                                }}
                            </CardDescription>
                        </CardContent>
                        <CardFooter>
                            <Button>Ikuti Kegiatan</Button>
                        </CardFooter>
                    </Card>
                </div>
            </div>

            <!-- FAQ Accordion -->
            <div class="w-full md:max-w-7xl place-self-center px-4">
                <h1 class="text-lg md:text-xl lg:text-2xl mt-2 mb-6 font-bold text-center">Yang Sering Ditanyakan</h1>
                <Accordion type="single" class="w-full" collapsible :default-value="defaultValue">
                    <AccordionItem v-for="item in accordionItems" :key="item.value" :value="item.value">
                        <AccordionTrigger>{{ item.title }}</AccordionTrigger>
                        <AccordionContent>
                            {{ item.content }}
                        </AccordionContent>
                    </AccordionItem>
                </Accordion>
            </div>
        </div>
    </AppLayout>
</template>
