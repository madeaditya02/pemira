<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { computed } from 'vue';
import { Progress } from '@/components/ui/progress'

const title = 'Hasil Pemilihan Hima';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Hasil Pemilihan Hima',
        href: '/resultHima',
    },
];

// Data kandidat (nanti bisa dari props/API)
const kandidat1 = {     
    nama: 'Malin Kundang',
    foto: '/images/contoh-caka1.png',
    jumlahSuara: 20,
};

const kandidat2 = {
    nama: 'Jaka Tarub',
    foto: '/images/contoh-caka2.png',
    jumlahSuara: 20,
};

const totalSuara = computed(() => kandidat1.jumlahSuara + kandidat2.jumlahSuara);

// Height untuk bar (max 250px)
const heightKandidat1 = computed(() => {
    const maxHeight = 250;
    if (totalSuara.value === 0) return 0;
    return (kandidat1.jumlahSuara / totalSuara.value) * maxHeight;
});

const heightKandidat2 = computed(() => {
    const maxHeight = 250;
    if (totalSuara.value === 0) return 0;
    return (kandidat2.jumlahSuara / totalSuara.value) * maxHeight;
});
</script>

<template>
    <Head :title="title" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-col overflow-x-auto">
            <div class="relative flex min-h-[90vh] flex-1 flex-col items-center justify-center">
                <img src="/images/banner-himaif.webp" alt="background hasil hima" class="absolute inset-0 z-0 w-full object-cover" />
            </div>

            <div class="mx-auto grid w-full max-w-7xl grid-cols-3 items-end gap-2 px-4 py-4">
                <!-- Kandidat 1 -->
                <div class="col-span-1 flex flex-row items-center justify-end gap-4">
                    <div class="flex flex-col items-end justify-center">
                        <h2 class="text-lg font-bold">{{ kandidat1.nama }}</h2>
                        <p class="mt-2">Sarjana Informatika 2023</p>
                    </div>
                    <img :src="kandidat1.foto" alt="caka hima 1" class="h-64 w-auto" />
                </div>

                <!-- Bar Suara -->
                <div class="col-span-1 flex items-center justify-center gap-8 pb-4">
                    <!-- Bar Kandidat 1 -->
                    <div class="flex flex-col items-center place-items-end">
                        <div
                            class="flex w-16 items-end justify-center rounded-t-lg bg-[#5465D1] font-bold text-white transition-all duration-500"
                            :style="{ height: `${heightKandidat1}px` }"
                        >
                        </div>
                        <div class="mt-2 text-lg font-bold text-[#5465D1]">{{ kandidat1.jumlahSuara }}</div>
                    </div>

                    <!-- Bar Kandidat 2 -->
                    <div class="flex flex-col items-center place-self-end">
                        <div
                            class="flex w-16 items-end justify-center rounded-t-lg bg-[#BD4C4C] font-bold text-white transition-all duration-500"
                            :style="{ height: `${heightKandidat2}px` }"
                        >
                        </div>
                        <div class="mt-2 text-lg font-bold text-[#BD4C4C]">{{ kandidat2.jumlahSuara }}</div>
                    </div>
                </div>

                <!-- Kandidat 2 -->
                <div class="col-span-1 flex flex-row items-center justify-start gap-4">
                    <img :src="kandidat2.foto" alt="caka hima 2" class="h-64 w-auto" />
                    <div class="flex flex-col items-start justify-center">
                        <h2 class="text-lg font-bold">{{ kandidat2.nama }}</h2>
                        <p class="mt-2">Sarjana Informatika 2023</p>
                    </div>
                </div>
            </div>

            <!-- Total Suara -->
            <div class="mb-4 text-center">
                <p class="text-lg font-semibold text-[#5465D1]">Total Suara Masuk: {{ totalSuara }}</p>
            </div>

            <!-- Progress Bar Suara per Akt-->
            <div class="mx-auto w-full max-w-7xl items-end gap-2 px-4 mb-4">
                <div class="mb-4 mx-auto w-full max-w-7xl flex flex-row justify-between">
                    <h1 class="font-medium">2025</h1>
                    <h1 class="font-medium">50% (200/400 suara)</h1>
                </div>
                <Progress :model-value="50" />
            </div>
            <div class="mx-auto w-full max-w-7xl items-end gap-2 px-4 mb-4">
                <div class="mb-4 mx-auto w-full max-w-7xl flex flex-row justify-between">
                    <h1 class="font-medium">2024</h1>
                    <h1 class="font-medium">50% (200/400 suara)</h1>
                </div>
                <Progress :model-value="50" />
            </div>
            <div class="mx-auto w-full max-w-7xl items-end gap-2 px-4 mb-4">
                <div class="mb-4 mx-auto w-full max-w-7xl flex flex-row justify-between">
                    <h1 class="font-medium">2023</h1>
                    <h1 class="font-medium">50% (200/400 suara)</h1>
                </div>
                <Progress :model-value="50" />
            </div>
            <div class="mx-auto w-full max-w-7xl items-end gap-2 px-4 mb-4">
                <div class="mb-4 mx-auto w-full max-w-7xl flex flex-row justify-between">
                    <h1 class="font-medium">2022</h1>
                    <h1 class="font-medium">50% (200/400 suara)</h1>
                </div>
                <Progress :model-value="50" />
            </div>
        </div>
    </AppLayout>
</template>