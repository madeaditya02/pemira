<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { useWindowSize } from '@vueuse/core';
import { Progress } from '@/components/ui/progress'

const title = 'Hasil Pemilihan Bem';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Hasil Pemilihan Bem',
        href: '/result-bem',
    },
];

// Data kandidat (nanti bisa dari props/API)
const kandidat1 = {
    nama: 'Malin Kundang',
    foto: '/images/caka1.png',
    jumlahSuara: 20,
};

const kandidat2 = {
    nama: 'Jaka Tarub',
    foto: '/images/caka1.png',
    jumlahSuara: 20,
};

const totalSuara = computed(() => kandidat1.jumlahSuara + kandidat2.jumlahSuara);
const { width } = useWindowSize();

// Height untuk bar (max 250px)
const maxHeight = computed(() => {
    if (width.value < 768) return 130; // mobile
    if (width.value < 1024) return 220; // tablet
    return 240; // desktop
});

const heightKandidat1 = computed(() => {
    if (totalSuara.value === 0) return 0;
    return (kandidat1.jumlahSuara / totalSuara.value) * maxHeight.value;
});

const heightKandidat2 = computed(() => {
    if (totalSuara.value === 0) return 0;
    return (kandidat2.jumlahSuara / totalSuara.value) * maxHeight.value;
});
</script>

<template>
    <Head :title="title" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-col overflow-x-auto">
            <!-- Banner sebagai background dengan content di atasnya -->
            <div class="relative md:min-h-[90vh] min-h-[50vh] flex items-end justify-center">
                <img src="/images/banner-bem.webp" alt="background hasil bem" class="absolute inset-0 z-0 w-full object-cover" />
                
                <!-- Content di atas banner -->
                <div class="relative mx-auto grid w-full max-w-7xl grid-cols-3 items-end gap-2 px-4 md:pb-8 pb-4">
                    <!-- Kandidat 1 -->
                    <div class="col-span-1 flex flex-row items-center justify-end gap-4">
                        <!-- <div class="md:flex hidden flex-col items-end justify-center">
                            <h2 class="md:text-lg text-sm font-bold">{{ kandidat1.nama }}</h2>
                            <h2 class="md:text-lg text-sm font-bold">&</h2>
                            <h2 class="md:text-lg text-sm font-bold">{{ kandidat2.nama }}</h2>
                        </div> -->
                        <img :src="kandidat1.foto" alt="caka hima 1" class="md:h-64 h-28 w-auto" />
                    </div>

                    <!-- Bar Suara -->
                    <div class="col-span-1 flex items-end justify-center md:gap-8 gap-3">
                        <!-- Bar Kandidat 1 -->
                        <div class="flex flex-col items-center">
                            <div
                                class="flex md:w-16 w-6 items-end justify-center rounded-t-lg bg-[#5465D1] font-bold text-white transition-all duration-500"
                                :style="{ height: `${heightKandidat1}px` }"
                            >
                            </div>
                            <div class="mt-2 md:text-lg text-sm font-bold text-[#5465D1]">{{ kandidat1.jumlahSuara }}</div>
                        </div>

                        <!-- Bar Kandidat 2 -->
                        <div class="flex flex-col items-center">
                            <div
                                class="flex md:w-16 w-6 items-end justify-center rounded-t-lg bg-[#BD4C4C] font-bold text-white transition-all duration-500"
                                :style="{ height: `${heightKandidat2}px` }"
                            >
                            </div>
                            <div class="mt-2 md:text-lg text-sm font-bold text-[#BD4C4C]">{{ kandidat2.jumlahSuara }}</div>
                        </div>
                    </div>

                    <!-- Kandidat 2 -->
                    <div class="col-span-1 flex flex-row items-center justify-start gap-4">
                        <img :src="kandidat2.foto" alt="caka hima 2" class="md:h-64 h-28 w-auto" />
                        <!-- <div class="md:flex hidden flex-col items-start justify-center">
                            <h2 class="md:text-lg text-sm font-bold">{{ kandidat2.nama }}</h2>
                            <p class="md:text-lg text-sm mt-2">Sarjana Informatika 2023</p>
                        </div> -->
                    </div>
                </div>
            </div>

            <!-- Total Suara -->
            <div class="mb-4 text-center mt-4">
                <p class="md:text-lg text-md font-semibold text-[#5465D1]">Total Suara Masuk: {{ totalSuara }}</p>
            </div>

            <!-- Progress Bar Suara per Akt-->
            <div class="mx-auto w-full max-w-7xl items-end gap-2 px-4 md:mb-4 mb-2">
                <div class="md:mb-4 mb-2 mx-auto w-full max-w-7xl flex flex-row justify-between">
                    <h1 class="md:text-lg text-sm font-medium text-[#5465D1]">Kimia</h1>
                    <h1 class="md:text-lg text-sm font-medium text-[#5465D1]">50% (200/400 suara)</h1>
                </div>
                <Progress :model-value="50" />
            </div>
            <div class="mx-auto w-full max-w-7xl items-end gap-2 px-4 md:mb-4 mb-2">
                <div class="md:mb-4 mb-2 mx-auto w-full max-w-7xl flex flex-row justify-between">
                    <h1 class="md:text-lg text-sm font-medium text-[#5465D1]">Fisika</h1>
                    <h1 class="md:text-lg text-sm font-medium text-[#5465D1]">50% (200/400 suara)</h1>
                </div>
                <Progress :model-value="50" />
            </div>
            <div class="mx-auto w-full max-w-7xl items-end gap-2 px-4 md:mb-4 mb-2">
                <div class="md:mb-4 mb-2 mx-auto w-full max-w-7xl flex flex-row justify-between">
                    <h1 class="md:text-lg text-sm font-medium text-[#5465D1]">Biologi</h1>
                    <h1 class="md:text-lg text-sm font-medium text-[#5465D1]">50% (200/400 suara)</h1>
                </div>
                <Progress :model-value="50" />
            </div>
            <div class="mx-auto w-full max-w-7xl items-end gap-2 px-4 md:mb-4 mb-2">
                <div class="md:mb-4 mb-2 mx-auto w-full max-w-7xl flex flex-row justify-between">
                    <h1 class="md:text-lg text-sm font-medium text-[#5465D1]">Matematika</h1>
                    <h1 class="md:text-lg text-sm font-medium text-[#5465D1]">50% (200/400 suara)</h1>
                </div>
                <Progress :model-value="50" />
            </div>
            <div class="mx-auto w-full max-w-7xl items-end gap-2 px-4 md:mb-4 mb-2">
                <div class="md:mb-4 mb-2 mx-auto w-full max-w-7xl flex flex-row justify-between">
                    <h1 class="md:text-lg text-sm font-medium text-[#5465D1]">Farmasi</h1>
                    <h1 class="md:text-lg text-sm font-medium text-[#5465D1]">50% (200/400 suara)</h1>
                </div>
                <Progress :model-value="50" />
            </div>
            <div class="mx-auto w-full max-w-7xl items-end gap-2 px-4 md:mb-4 mb-2 pb-8">
                <div class="md:mb-4 mb-2 mx-auto w-full max-w-7xl flex flex-row justify-between">
                    <h1 class="md:text-lg text-sm font-medium text-[#5465D1]">Informatika</h1>
                    <h1 class="md:text-lg text-sm font-medium text-[#5465D1]">50% (200/400 suara)</h1>
                </div>
                <Progress :model-value="50" />
            </div>
        </div>
    </AppLayout>
</template>