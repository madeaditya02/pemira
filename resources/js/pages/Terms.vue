<script setup lang="ts">
import { AlertDialog, AlertDialogAction, AlertDialogCancel, AlertDialogContent, AlertDialogDescription, AlertDialogFooter, AlertDialogHeader, AlertDialogTitle, AlertDialogTrigger } from '@/components/ui/alert-dialog';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem, Kegiatan } from '@/types';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { TriangleAlert } from 'lucide-vue-next';
import { computed, onMounted, onUnmounted, ref } from 'vue';

// Page title and breadcrumbs
const page = usePage();
const auth = computed(() => page.props.auth);
const title = 'Syarat dan Ketentuan';

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

const candidateLink = (nama: string) => {
    const formattedName = nama.toLowerCase().replace(/\s+/g, '-');
    return `/candidates/${formattedName}`;
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
        title: 'Syarat dan Ketentuan',
        href: '/terms',
    },
];
</script>

<template>

    <Head :title="title" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-col gap-2 overflow-x-auto">
            <div class="relative flex min-h-[90vh] flex-1 flex-col items-start justify-start pt-20 md:pt-32 lg:pt-40">
                <img src="/images/banner-fmipa.webp" alt="background syarat"
                    class="absolute inset-0 top-0 w-full object-cover" />

                <div class="mx-auto mt-12 grid w-full max-w-7xl items-start gap-4 px-4 lg:mt-56">
                    <h1 class="md:text-md font-semibold lg:text-xl">Syarat & Ketentuan :</h1>
                    <ol class="md:text-md ml-2 list-inside list-decimal space-y-2 text-sm md:ml-10 lg:text-lg">
                        <li>
                            Mahasiswa atau mahasiswi yang dapat memilih adalah mahasiswa atau mahasiswi Fakultas
                            Matematika dan Ilmu Pengetahuan Alam.
                        </li>
                        <li>Mahasiswa atau mahasiswi yang dapat memilih adalah mahasiswa atau mahasiswi aktif.</li>
                        <li>Mahasiswa atau mahasiswi hanya bisa melakukan pemilihan sebanyak satu kali.</li>
                        <li>Mahasiswa atau mahasiswi diharapkan memilih dengan jujur.</li>
                        <li>Hasil pemilihan bersifat mutlak dan tidak dapat diganggu gugat, sesuai dengan aturan yang
                            telah ditetapkan.</li>
                        <li>Setiap pelanggaran terhadap syarat dan ketentuan ini akan dikenakan sanksi sesuai dengan
                            peraturan yang berlaku.</li>
                    </ol>
                </div>

                <div v-if="auth.user && filteredKegiatan.length > 0" class="w-full px-4">
                    <h1 class="text-lg md:text-xl lg:text-2xl mt-12 mb-6 font-bold text-center">Kegiatan Mendatang</h1>
                    <div class="md:max-w-7xl mb-6 w-full grid place-self-center auto-rows-min gap-4 md:grid-cols-2">
                        <Card v-for="item in filteredKegiatan" :key="item.id"
                            class="border shadow-sm shadow-foreground/10">
                            <CardHeader>
                                <img :src="`/storage/${item.foto}`" alt="" class="w-full h-64 object-cover rounded-md">
                            </CardHeader>
                            <CardContent class="space-y-2">
                                <CardTitle class="text-lg md:text-xl">{{ item.nama }}</CardTitle>
                                <CardDescription>
                                    {{ getTimeUntilStart(item.waktu_mulai as Date).expired ?
                                        getTimeUntilStart(item.waktu_mulai as Date).text :
                                        `Dimulai dalam ${getTimeUntilStart(item.waktu_mulai as Date).text}`
                                    }}
                                </CardDescription>
                            </CardContent>
                            <CardFooter>
                                <Button variant="default" size="default">
                                    <Link :href="candidateLink(item.nama)">
                                    Lihat Kandidat
                                    </Link>
                                </Button>
                            </CardFooter>
                        </Card>
                    </div>
                </div>

                <div class="mx-auto mt-10 max-w-md grid grid-cols-2 gap-4 px-4">
                    <Link href="/dashboard" class="w-full col-span-1">
                        <Button variant="outline" size="lg"
                            class="border-primary border-2 text-base font-semibold text-primary hover:text-primary w-full">
                            Batal
                        </Button>
                    </Link>
                    <AlertDialog>
                        <AlertDialogTrigger as-child>
                            <Button variant="default" size="lg" class="text-base font-semibold">
                                Mulai Memilih
                            </Button>
                        </AlertDialogTrigger>
                        <AlertDialogContent>
                            <AlertDialogHeader>
                                <AlertDialogTitle class="flex flex-col items-center justify-center gap-2">
                                    <TriangleAlert class="h-24 w-24 text-red-700" />
                                    <h1 class="text-xl font-black text-red-700">ATTENTION</h1>
                                </AlertDialogTitle>
                                <AlertDialogDescription class="text-md text-center">
                                    Pemilihan hanya dapat dilakukan sekali dan tidak ada pengulangan, apabila anda ingin
                                    melanjutkan silahkan klik
                                    tombol "Lanjutkan"!
                                </AlertDialogDescription>
                            </AlertDialogHeader>
                            <AlertDialogFooter class="sm:justify-center">
                                <div class="grid w-full grid-cols-2 gap-3">
                                    <AlertDialogCancel
                                        class="m-0 border border-primary text-primary hover:bg-primary/10">
                                        Batal
                                    </AlertDialogCancel>
                                    <AlertDialogAction as-child class="m-0">
                                        <Link href="/vote" class="bg-primary hover:bg-primary/90">
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
    </AppLayout>
</template>
