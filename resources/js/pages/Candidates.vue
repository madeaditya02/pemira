<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Separator } from '@/components/ui/separator';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem, Kandidat, Kegiatan } from '@/types';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { CheckCircle2, Target, Users } from 'lucide-vue-next';
import { computed } from 'vue';

// Page title and breadcrumbs
const page = usePage();
const auth = computed(() => page.props.auth);
const title = 'Kandidat Pemilihan';

// define props
const props = defineProps<{
    kegiatan: Kegiatan;
    kandidat: Kandidat[];
}>();

const handleHeroImage = (foto: string) => {
    const formattedName = foto.replace('png', 'webp');
    return foto ? `/images/${formattedName}` : '/images/banner-fmipa.webp';
}

// Remove kandidat kotak kosong agar tidak tampil
const kandidat = computed(() => {
    return props.kandidat.filter(k => {
        const hasKotakKosong = k.mahasiswa.some(m => m.nama.toLowerCase().includes('kotak kosong'));
        return !hasKotakKosong;
    });
});

// Function to format misi
const formatMisi = (misi: string): string[] => {
    const misiItems = misi.split(/\d+\.\s/).filter(item => item.trim());
    return misiItems.map(item => item.trim());
}

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Kandidat Pemilihan',
        href: '/candidates/{slug}',
    },
];
</script>

<template>
    <Head :title="title" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-col gap-2 overflow-x-auto">
            <!-- Hero Section -->
            <div class="relative flex min-h-[30vh] md:min-h-[45vh] flex-col items-center justify-center">
                <img :src="handleHeroImage(props.kegiatan.foto)" alt="background"
                    class="absolute inset-0 w-full h-full object-cover" />
            </div>

            <!-- Kandidat Cards -->
            <div class="flex flex-col gap-4 mx-auto w-full max-w-7xl px-4 py-8 md:py-12 space-y-8">
                <h1 class="text-xl font-bold md:text-2xl text-center">Kandidat {{ props.kegiatan.nama }}</h1>
                <Card v-for="(k, index) in kandidat" :key="k.id"
                    class="overflow-hidden hover:shadow-xl transition-all duration-300 border-2 py-0"
                    :class="index % 2 === 0 ? 'hover:border-primary/50' : 'hover:border-secondary/50'">

                    <div class="grid grid-cols-1 lg:grid-cols-12 items-start gap-0">
                        <!-- Foto Section -->
                        <div :class="`lg:col-span-4 relative ${index % 2 !== 0 ? 'lg:order-last' : ''}`">
                            <!-- Mobile View - Full Image with Overlay -->
                            <div class="relative h-64 min-h-[400px] lg:hidden">
                                <img :src="k.foto !== null ? `/storage/${k.foto}` : '/images/blank-profile-picture.webp'"
                                    alt="Foto Kandidat" class="absolute inset-0 w-full h-full object-cover md:object-contain" />

                                <!-- Gradient Overlay -->
                                <div class="absolute inset-0 bg-linear-to-t from-black/60 to-transparent">
                                </div>

                                <!-- Nomor Urut Badge - Mobile -->
                                <div class="absolute top-4 left-4 z-10">
                                    <div
                                        class="bg-primary text-primary-foreground px-4 py-2 rounded-full font-bold text-lg shadow-xl flex items-center gap-1.5">
                                        <span class="text-xs opacity-75">No.</span>
                                        <span>{{ k.no_urut }}</span>
                                    </div>
                                </div>

                                <!-- Nama di Mobile (overlay di foto) -->
                                <div class="absolute bottom-0 left-0 right-0 p-6 text-white">
                                    <h2 class="text-xl font-bold mb-1">
                                        <span v-for="(mhs, idx) in k.mahasiswa" :key="mhs.nim">
                                            {{ mhs.nama }}
                                            <span v-if="idx < k.mahasiswa.length - 1"> & </span>
                                        </span>
                                    </h2>
                                    <p class="text-sm opacity-90">
                                        <span v-for="(mhs, idx) in k.mahasiswa" :key="mhs.nim">
                                            {{ mhs.nim }}
                                            <span v-if="idx < k.mahasiswa.length - 1"> | </span>
                                        </span>
                                    </p>
                                </div>
                            </div>

                            <!-- Desktop View - Circular Avatar -->
                            <div class="hidden lg:flex lg:flex-col lg:items-center lg:justify-center lg:p-10 lg:h-full relative">
                                <div class="relative">
                                    <!-- Circular Photo -->
                                    <div class="size-72 rounded-full overflow-hidden border-4 border-primary/20 shadow-xl">
                                        <img :src="k.foto !== null ? `/storage/${k.foto}` : '/images/blank-profile-picture.webp'"
                                            alt="Foto Kandidat" 
                                            class="w-full h-full object-cover" />
                                    </div>
                                    
                                    <!-- Nomor Urut Badge - Desktop (Floating on Avatar) -->
                                    <div class="absolute -top-2 -right-2">
                                        <div class="bg-primary text-primary-foreground px-3 py-1.5 rounded-full font-bold text-sm shadow-lg flex items-center gap-1">
                                            <span class="text-xs opacity-75">No.</span>
                                            <span>{{ k.no_urut }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Content Section -->
                        <div class="lg:col-span-8 p-6 md:p-8 lg:p-10">
                            <!-- Header - Hidden di mobile karena ada di foto -->
                            <div class="hidden lg:block mb-6">
                                <div class="flex items-start justify-between mb-4">
                                    <div>
                                        <h2 class="text-2xl font-bold mb-2 text-foreground">
                                            <span v-for="(mhs, idx) in k.mahasiswa" :key="mhs.nim">
                                                {{ mhs.nama }}
                                                <span v-if="idx < k.mahasiswa.length - 1"> & </span>
                                            </span>
                                        </h2>
                                        <div class="flex flex-wrap gap-2">
                                            <Badge v-for="(mhs, idx) in k.mahasiswa" :key="mhs.nim" variant="secondary"
                                                class="text-sm">
                                                <Users class="w-3 h-3 mr-1" />
                                                {{ mhs.nim }} - Calon {{ mhs.pivot.jabatan.charAt(0).toUpperCase() + mhs.pivot.jabatan.slice(1) }}
                                            </Badge>
                                        </div>
                                    </div>
                                </div>
                                <Separator class="my-4" />
                            </div>

                            <!-- Visi Section -->
                            <div class="mb-8">
                                <div class="flex items-center gap-2 mb-3">
                                    <div class="p-2 bg-primary/10 rounded-lg">
                                        <Target class="w-5 h-5 text-primary" />
                                    </div>
                                    <h3 class="text-xl font-bold text-primary">Visi</h3>
                                </div>
                                <p class="text-base leading-relaxed text-muted-foreground lg:pl-11">
                                    {{ k.visi }}
                                </p>
                            </div>

                            <!-- Misi Section -->
                            <div>
                                <div class="flex items-center gap-2 mb-4">
                                    <div class="p-2 bg-primary/10 rounded-lg">
                                        <CheckCircle2 class="w-5 h-5 text-primary" />
                                    </div>
                                    <h3 class="text-xl font-bold text-primary">Misi</h3>
                                </div>
                                <ol class="space-y-3 pl-2 lg:pl-10">
                                    <li v-for="(misiItem, idx) in formatMisi(k.misi)" :key="idx"
                                        class="flex items-start gap-3 text-base text-muted-foreground">
                                        <span
                                            class="shrink-0 w-6 h-6 rounded-full bg-primary/20 text-primary flex items-center justify-center text-sm font-semibold">
                                            {{ idx + 1 }}
                                        </span>
                                        <span class="leading-relaxed">{{ misiItem }}</span>
                                    </li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </Card>
            </div>

            <!-- CTA Buttons -->
            <div class="mx-auto w-full max-w-md px-4 pb-12">
                <div class="grid grid-cols-2 gap-4">
                    <Link href="/dashboard" class="w-full">
                        <Button variant="outline" size="lg" class="w-full border-2 hover:bg-primary/5">
                            <span class="flex text-base items-center gap-2">
                                Ke Beranda
                            </span>
                        </Button>
                    </Link>
                    <Link href="/terms" class="w-full">
                        <Button variant="default" size="lg" class="w-full shadow-lg hover:shadow-xl transition-shadow">
                            <span class="flex text-base items-center gap-2">
                                Mulai Memilih
                            </span>
                        </Button>
                    </Link>
                </div>
            </div>
        </div>
    </AppLayout>
</template>