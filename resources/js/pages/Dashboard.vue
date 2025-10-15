<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, usePage } from '@inertiajs/vue3';
import { computed, onMounted, onUnmounted, ref } from 'vue';
import { Card, CardHeader, CardContent, CardTitle, CardDescription, CardFooter } from '@/components/ui/card';

// define props
const props = defineProps<{
    kegiatan: Array<{
        id: number;
        id_program_studi: number;
        nama: string;
        tahun: number;
        waktu_mulai: Date;
        waktu_selesai: Date;
        foto: string;
        ruang_lingkup: 'fakultas' | 'program studi';
    }>
    waktu: Date | string;
}>();

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
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4 overflow-x-auto">
            <!-- Countdown timer -->
            <div class="relative min-h-[85vh] space-y-4 flex flex-col flex-1 items-center justify-center rounded-xl shadow-sm shadow-foreground/10">
                <!-- Header -->
                <div class="text-center">
                    <h2 class="text-lg md:text-xl lg:text-2xl font-bold text-foreground mb-2">
                        PEMIRA FMIPA
                    </h2>
                    <p class="text-accent-foreground">
                        Pemilihan akan dimulai dalam
                    </p>
                </div>
    
                <!-- Countdown Display -->
                <div class="flex justify-center space-x-4 items-start">
                    <!-- Days -->
                    <div class="text-center">
                        <div class="text-4xl md:text-5xl lg:text-6xl font-bold text-foreground">
                            {{ formatTime(timeRemaining.days) }}
                        </div>
                        <div class="text-sm lg:text-base text-accent-foreground mt-2">
                            Hari
                        </div>
                    </div>

                    <div class="pt-2 text-xl md:text-2xl lg:text-3xl font-bold text-muted-foreground">:</div>

                    <!-- Hours -->
                    <div class="text-center">
                        <div class="text-4xl md:text-5xl lg:text-6xl font-bold text-foreground">
                            {{ formatTime(timeRemaining.hours) }}
                        </div>
                        <div class="text-sm lg:text-base text-accent-foreground mt-2">
                            Jam
                        </div>
                    </div>

                    <div class="pt-2 text-xl md:text-2xl lg:text-3xl font-bold text-muted-foreground">:</div>

                    <!-- Minutes -->
                    <div class="text-center">
                        <div class="text-4xl md:text-5xl lg:text-6xl font-bold text-foreground">
                            {{ formatTime(timeRemaining.minutes) }}
                        </div>
                        <div class="text-sm lg:text-base text-accent-foreground mt-2">
                            Menit
                        </div>
                    </div>

                    <div class="pt-2 text-xl md:text-2xl lg:text-3xl font-bold text-muted-foreground">:</div>

                    <!-- Seconds -->
                    <div class="text-center">
                        <div class="text-4xl md:text-5xl lg:text-6xl font-bold text-foreground">
                            {{ formatTime(timeRemaining.seconds) }}
                        </div>
                        <div class="text-sm lg:text-base text-accent-foreground mt-2">
                            Detik
                        </div>
                    </div>
                </div>

                <!-- Status Message -->
                <div class="text-center">
                    <p v-if="timeRemaining.expired" class="text-red-600 dark:text-red-400 font-semibold">
                        Saatnya lakukan pemilihan
                    </p>
                    <p v-else class="text-accent-foreground">
                        Bersiaplah untuk pemilihan
                    </p>
                </div>
            </div>

            <!-- List Kegiatan -->
            <div v-if="auth.user" class="grid auto-rows-min gap-4 md:grid-cols-2">
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
    </AppLayout>
</template>
