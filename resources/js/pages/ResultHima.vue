<script setup lang="ts">
import { Progress } from '@/components/ui/progress';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { useWindowSize } from '@vueuse/core';
import { computed, ref } from 'vue';

const title = 'Hasil Pemilihan Hima';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Hasil Pemilihan Hima',
        href: '/result-hima',
    },
];

// Data kandidat (nanti dari props)
const kandidat = ref([
    {
        nama: 'Malin Kundang',
        foto: '/images/contoh-caka1.png',
        jumlahSuara: 200,
        angkatan: 'Sarjana Informatika 2023',
    },
    {
        nama: 'Jaka Tarub',
        foto: '/images/contoh-caka2.png',
        jumlahSuara: 20,
        angkatan: 'Sarjana Informatika 2023',
    },
    {
        nama: 'Si Pitung',
        foto: '/images/contoh-caka2.png',
        jumlahSuara: 25,
        angkatan: 'Sarjana Informatika 2023',
    },
]);

const totalSuara = computed(() => kandidat.value.reduce((sum, k) => sum + k.jumlahSuara, 0));
const { width } = useWindowSize();
const isTwoKandidat = computed(() => kandidat.value.length === 2);

// Height untuk bar
const maxHeight = computed(() => {
    if (width.value < 768) return 130;
    if (width.value < 1024) return 220;
    return 240;
});

const getBarHeight = (jumlahSuara: number) => {
    if (totalSuara.value === 0) return 0;
    return (jumlahSuara / totalSuara.value) * maxHeight.value;
};

// Warna untuk bar
const barColors = ['#5465D1', '#BD4C4C', '#4CAF50', '#FF9800', '#9C27B0'];
</script>

<template>
    <Head :title="title" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-col overflow-x-auto">
            <!-- Banner sebagai background dengan content di atasnya -->
            <div class="relative flex min-h-[50vh] items-end justify-center md:min-h-[90vh]">
                <img src="/images/banner-himaif.webp" alt="background hasil bem" class="absolute inset-0 z-0 w-full object-cover" />

                <!-- LAYOUT UNTUK 2 KANDIDAT -->
                <div v-if="isTwoKandidat" class="relative mx-auto grid w-full max-w-7xl grid-cols-3 items-end gap-4 px-4 pb-4 md:gap-2 md:pb-8">
                    <!-- Kandidat 1 -->
                    <div class="col-span-1 flex flex-row items-center justify-end gap-4">
                        <div class="hidden flex-col items-end justify-center md:flex">
                            <h2 class="text-sm font-bold md:text-lg">{{ kandidat[0].nama }}</h2>
                            <p class="mt-2 text-sm md:text-lg">{{ kandidat[0].angkatan }}</p>
                        </div>
                        <img :src="kandidat[0].foto" alt="caka hima 1" class="h-36 w-auto md:h-64" />
                    </div>

                    <!-- Bar Suara -->
                    <div class="col-span-1 flex items-end justify-center gap-4 md:gap-8">
                        <div v-for="(k, index) in kandidat" :key="index" class="flex flex-col items-center">
                            <div
                                class="flex w-8 items-end justify-center rounded-t-lg font-bold text-white transition-all duration-500 md:w-16"
                                :style="{
                                    height: `${getBarHeight(k.jumlahSuara)}px`,
                                    backgroundColor: barColors[index],
                                }"
                            ></div>
                            <div class="mt-2 text-sm font-bold md:text-lg" :style="{ color: barColors[index] }">
                                {{ k.jumlahSuara }}
                            </div>
                        </div>
                    </div>

                    <!-- Kandidat 2 -->
                    <div class="col-span-1 flex flex-row items-center justify-start gap-4">
                        <img :src="kandidat[1].foto" alt="caka hima 2" class="h-36 w-auto md:h-64" />
                        <div class="hidden flex-col items-start justify-center md:flex">
                            <h2 class="text-sm font-bold md:text-lg">{{ kandidat[1].nama }}</h2>
                            <p class="mt-2 text-sm md:text-lg">{{ kandidat[1].angkatan }}</p>
                        </div>
                    </div>
                </div>

                <!-- LAYOUT UNTUK 3+ KANDIDAT -->
                <div v-else class="relative mx-auto w-full max-w-7xl px-4 pb-4 md:pb-8">
                    <div class="mt-30 flex flex-wrap items-center justify-center gap-6 md:mt-64 lg:mt-0 md:flex-row md:gap-12">
                        <!-- Loop untuk setiap kandidat -->
                        <div v-for="(k, index) in kandidat" :key="index" class="flex flex-row items-center place-self-end gap-4">
                            <div class="flex max-w-[150px] flex-col items-center md:max-w-[180px] lg:max-w-[220px]">
                                <!-- Foto kandidat -->
                                <div class="relative mb-2 md:mb-4">
                                    <img :src="k.foto" :alt="`kandidat ${index + 1}`" class="h-32 w-auto rounded-full object-cover md:h-40 lg:h-52" />
                                </div>
                                <!-- Nama dan angkatan -->
                                <div class="mt-1 flex w-full flex-col items-center text-center md:mt-3">
                                    <h2 class="line-clamp-2 w-full text-sm font-bold wrap-break-words md:text-lg">{{ k.nama }}</h2>
                                    <p class="mt-1 w-full truncate text-xs md:text-sm">{{ k.angkatan }}</p>
                                </div>
                            </div>

                            <!-- Bar suara -->
                            <div class="mx-4 flex flex-col items-center place-self-end">
                                <div
                                    class="flex w-6 items-end justify-center rounded-t-lg text-lg font-bold text-white transition-all duration-500 md:w-12 lg:w-16 md:text-xl"
                                    :style="{
                                        height: `${getBarHeight(k.jumlahSuara)}px`,
                                        backgroundColor: barColors[index],
                                    }"
                                ></div>
                                <!-- Jumlah suara -->
                                <div class="text-md mt-2 mb-0 font-bold md:mb-4 md:text-xl" :style="{ color: barColors[index] }">
                                    {{ k.jumlahSuara }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Suara -->
            <div class="mt-4 mb-4 text-center">
                <p class="text-md font-semibold text-[#5465D1] md:text-lg">Total Suara Masuk: {{ totalSuara }}</p>
            </div>

            <!-- Progress Bar Suara per Akt-->
            <div class="mx-auto mb-2 w-full max-w-7xl items-end gap-2 px-4 md:mb-4">
                <div class="mx-auto mb-2 flex w-full max-w-7xl flex-row justify-between md:mb-4">
                    <h1 class="text-sm font-medium md:text-lg text-[#5465D1]">2025</h1>
                    <h1 class="text-sm font-medium md:text-lg text-[#5465D1]">50% (200/400 suara)</h1>
                </div>
                <Progress :model-value="50" />
            </div>
            <div class="mx-auto mb-2 w-full max-w-7xl items-end gap-2 px-4 md:mb-4">
                <div class="mx-auto mb-2 flex w-full max-w-7xl flex-row justify-between md:mb-4">
                    <h1 class="text-sm font-medium md:text-lg text-[#5465D1]">2024</h1>
                    <h1 class="text-sm font-medium md:text-lg text-[#5465D1]">50% (200/400 suara)</h1>
                </div>
                <Progress :model-value="50" />
            </div>
            <div class="mx-auto mb-2 w-full max-w-7xl items-end gap-2 px-4 md:mb-4">
                <div class="mx-auto mb-2 flex w-full max-w-7xl flex-row justify-between md:mb-4">
                    <h1 class="text-sm font-medium md:text-lg text-[#5465D1]">2023</h1>
                    <h1 class="text-sm font-medium md:text-lg text-[#5465D1]">50% (200/400 suara)</h1>
                </div>
                <Progress :model-value="50" />
            </div>
            <div class="mx-auto mb-2 w-full max-w-7xl items-end gap-2 px-4 pb-8 md:mb-4">
                <div class="mx-auto mb-2 flex w-full max-w-7xl flex-row justify-between md:mb-4">
                    <h1 class="text-sm font-medium md:text-lg text-[#5465D1]">2022</h1>
                    <h1 class="text-sm font-medium md:text-lg text-[#5465D1]">50% (200/400 suara)</h1>
                </div>
                <Progress :model-value="50" />
            </div>
        </div>
    </AppLayout>
</template>
