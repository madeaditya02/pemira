<script setup lang="ts">
import {
    AlertDialog,
    AlertDialogAction,
    AlertDialogCancel,
    AlertDialogContent,
    AlertDialogDescription,
    AlertDialogFooter,
    AlertDialogHeader,
    AlertDialogTitle,
    AlertDialogTrigger,
} from '@/components/ui/alert-dialog';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem, Kegiatan } from '@/types';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { TriangleAlert } from 'lucide-vue-next';
import { computed, onMounted, onUnmounted, ref } from 'vue';
import { Toaster } from '@/components/ui/sonner'
import { toast } from "vue-sonner"
import 'vue-sonner/style.css'

// Page title and breadcrumbs
const page = usePage();
const auth = computed(() => page.props.auth);
const title = auth.value.user ? 'Pemilihan Caka BEM' : 'Pemilihan Caka BEM';

// define props
const props = defineProps<{
    kegiatan: Kegiatan[];
    waktu: Date | string;
}>();

// Add computed property to filter kegiatan
const filteredKegiatan = computed(() => {
    if (!auth.value.user) return [];

    return props.kegiatan.filter((item) => {
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
        return { text: 'Sedang berlangsung', expired: true };
    }

    const days = Math.floor(diff / (1000 * 60 * 60 * 24));
    const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
    const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));

    let text = '';
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

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Pemilihan Caka BEM',
        href: '/cakabem',
    },
];
</script>

<template>

    <Head :title="title" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-col gap-2 overflow-x-auto">
            <div class="relative flex min-h-[90vh] flex-1 flex-col items-start justify-start pt-20 md:pt-32 lg:pt-40">
                <img src="/images/bannercaka.svg" alt="" class="absolute inset-0 top-0 w-full object-cover" />

                <div class="relative mx-auto mt-20 grid w-full max-w-7xl items-start gap-4 px-4 lg:mt-56">
                    <div class="flex flex-col md:flex-row justify-center">
                        <!-- Caka 1 -->
                        <div
                            class="flex max-w-md flex-col items-center justify-center gap-6 p-6 min-h-[450px] sm:min-h-[500px] lg:min-h-[600px]">
                            <!-- frame container (relative) so photos can be positioned inside it -->
                            <div
                                class="relative max-w-md flex items-center justify-center p-6 place-self-center flex-col">
                                <div class="">
                                    <!-- Frame image (base) -->
                                    <img src="/images/Frame_Paslon_BEM.png" alt="Caka1"
                                        class="w-full max-w-xs sm:max-w-sm lg:max-w-md" />

                                    <!-- Photos positioned inside the frame -->
                                    <div>
                                        <img src="/images/BEM_1.png" alt="Caka1"
                                            class="absolute left-20 bottom-15 w-28 z-10 object-cover rounded-md shadow-md mask-b-from-10% mask-l-from-70% mask-linear-200 mask-linear-from-50% mask-linear-to-85% md:w-38 sm:w-40 md:left-22 sm:left-25 md:bottom-20 sm:bottom-21" />
                                        <h3
                                            class="absolute z-20 top-45 left-25 poppins-font font-bold text-white text-xs sm:text-sm md:text-sm lg:text-base xl:text-sm sm:left-35 sm:top-60 md:top-50 md:left-30 lg:top-60 lg:left-30">
                                            Putu Amara</h3>
                                        <h5
                                            class="absolute z-20 top-50 left-29 poppins-font font-thin text-white text-[10px] sm:left-40 sm:top-65 md:top-55 md:left-35 lg:top-65 lg:left-35">
                                            Fisika 23</h5>
                                    </div>
                                    <div>
                                        <img src="/images/BEM_2.png" alt="Caka1"
                                            class="absolute right-6 bottom-15 w-28 z-10 object-cover rounded-md shadow-md mask-b-from-10% mask-r-from-70% mask-linear-155 mask-linear-from-40% mask-linear-to-85% md:w-38 sm:w-42 md:right-6 sm:right-6 md:bottom-20 sm:bottom-18" />
                                        <h3
                                            class="absolute z-20 top-45 right-7 poppins-font font-bold text-white text-xs sm:text-sm md:text-sm lg:text-base xl:text-sm sm:right-10 sm:top-60 md:top-50 md:right-10 lg:top-60 lg:right-10">
                                            Nanda Asmara</h3>
                                        <h5
                                            class="absolute z-20 top-50 right-10 poppins-font font-thin text-white text-[10px] sm:right-10 sm:top-65 md:top-55 md:right-13 lg:top-65 lg:right-15">
                                            Informatika 23</h5>
                                    </div>
                                </div>
                                <!-- Centered button area on top of the frame -->
                                <div class="absolute inset-0 flex items-end justify-center pb-4 z-20">
                                    <AlertDialog>
                                        <AlertDialogTrigger as-child>
                                            <Button variant="default" size="lg"
                                                class="bg-[#5465D1] hover:bg-[#2B346B] text-white"> Vote </Button>
                                        </AlertDialogTrigger>
                                        <AlertDialogContent>
                                            <AlertDialogHeader>
                                                <AlertDialogTitle
                                                    class="flex flex-col items-center justify-center gap-2">
                                                    <TriangleAlert class="h-24 w-24 text-red-700" />
                                                    <h1 class="text-xl font-black text-red-700">ATTENTION</h1>
                                                </AlertDialogTitle>
                                                <AlertDialogDescription class="text-md text-center">
                                                    Pemilihan hanya dapat dilakukan sekali dan tidak ada pengulangan,
                                                    apabila
                                                    anda
                                                    ingin
                                                    melanjutkan silahkan klik
                                                    tombol "Lanjutkan"!
                                                </AlertDialogDescription>
                                            </AlertDialogHeader>
                                            <AlertDialogFooter class="sm:justify-center">
                                                <div class="grid w-full grid-cols-2 gap-3">
                                                    <AlertDialogCancel
                                                        class="m-0 border border-[#5465D1] text-[#5465D1] hover:bg-[#5465D1]/10">
                                                        Batal
                                                    </AlertDialogCancel>
                                                    <AlertDialogAction as-child class="m-0">
                                                        <Link variant="outline" @click="() => {
                                                            toast('Pilihan berhasil disimpan', {
                                                                description: 'Sunday, December 03, 2023 at 9:00 AM',
                                                            })
                                                        }" href="/cakahima" class="bg-[#5465D1] hover:bg-[#5465D1]/90">
                                                        Lanjutkan
                                                        </Link>
                                                    </AlertDialogAction>
                                                </div>
                                            </AlertDialogFooter>
                                        </AlertDialogContent>
                                    </AlertDialog>
                                </div>
                            </div>
                        </div>
                        <!-- Caka 2 -->
                        <div class="flex max-w-md flex-col items-center justify-center gap-6 p-6">
                            <!-- frame container (relative) so photos can be positioned inside it -->
                            <div class="relative max-w-md flex items-center justify-center p-6">
                                <div class=" ">
                                    <!-- Frame image (base) -->
                                    <img src="/images/Frame_Paslon_BEM.png" alt="Caka1" class="w-full max-w-xs" />

                                    <!-- Photos positioned inside the frame -->
                                    <div>
                                        <img src="/images/KotakKosong.png" alt="KotakKosong"
                                            class="absolute left-28 bottom-20 w-30 z-10 object-cover rounded-md shadow-md mask-b-from-10% mask-l-from-70% mask-linear-245 mask-linear-from-40% mask-linear-to-85% sm:w-40 sm:left-35 md:w-30 md:left-28 md:bottom-20 lg:w-40 lg:left-35 lg:bottom-25" />
                                    </div>
                                </div>
                                <!-- Centered button area on top of the frame -->
                                <div class="absolute inset-0 flex items-end justify-center pb-2 z-20">
                                    <AlertDialog>
                                        <AlertDialogTrigger as-child>
                                            <Button variant="default" size="lg"
                                                class="bg-[#5465D1] hover:bg-[#2B346B] text-white"> Vote </Button>
                                        </AlertDialogTrigger>
                                        <AlertDialogContent>
                                            <AlertDialogHeader>
                                                <AlertDialogTitle
                                                    class="flex flex-col items-center justify-center gap-2">
                                                    <TriangleAlert class="h-24 w-24 text-red-700" />
                                                    <h1 class="text-xl font-black text-red-700">ATTENTION</h1>
                                                </AlertDialogTitle>
                                                <AlertDialogDescription class="text-md text-center">
                                                    Pemilihan hanya dapat dilakukan sekali dan tidak ada pengulangan,
                                                    apabila
                                                    anda
                                                    ingin
                                                    melanjutkan silahkan klik
                                                    tombol "Lanjutkan"!
                                                </AlertDialogDescription>
                                            </AlertDialogHeader>
                                            <AlertDialogFooter class="sm:justify-center">
                                                <div class="grid w-full grid-cols-2 gap-3">
                                                    <AlertDialogCancel
                                                        class="m-0 border border-[#5465D1] text-[#5465D1] hover:bg-[#5465D1]/10">
                                                        Batal
                                                    </AlertDialogCancel>
                                                    <AlertDialogAction as-child class="m-0">
                                                        <Link variant="outline" @click="() => {
                                                            toast('Pilihan berhasil disimpan', {
                                                                description: 'Sunday, December 03, 2023 at 9:00 AM',
                                                            })
                                                        }" href="/cakahima" class="bg-[#5465D1] hover:bg-[#5465D1]/90">
                                                        Lanjutkan
                                                        </Link>
                                                    </AlertDialogAction>
                                                </div>
                                            </AlertDialogFooter>
                                        </AlertDialogContent>
                                    </AlertDialog>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
