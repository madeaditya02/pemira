<script setup lang="ts">
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import { computed, onMounted, onUnmounted, ref } from 'vue';
import { TriangleAlert, CheckCircle2 } from 'lucide-vue-next';
import type { BreadcrumbItem, Kegiatan, Kandidat } from '@/types';
import { AlertDialog, AlertDialogAction, AlertDialogCancel, AlertDialogContent, AlertDialogDescription, AlertDialogFooter, AlertDialogHeader, AlertDialogTitle, AlertDialogTrigger } from '@/components/ui/alert-dialog';

// Page title and breadcrumbs
const page = usePage();
const auth = computed(() => page.props.auth);
const title = 'Pemilihan';

// Define props
const props = defineProps<{
    kegiatanBem: Kegiatan & { kandidat: Kandidat[] };
    kegiatanHima: Kegiatan & { kandidat: Kandidat[] };
}>();

// Voting state
const currentStep = ref<'bem' | 'hima' | 'complete'>('bem');
const selectedBem = ref<number | null>(null);
const selectedHima = ref<number | null>(null);
const isVoting = ref(true);
const showConfirmDialog = ref(false);
const pendingSelection = ref<{ type: 'bem' | 'hima'; id: number } | null>(null);

// Handle vote selection with confirmation
const handleVoteClick = (type: 'bem' | 'hima', kandidatId: number) => {
    pendingSelection.value = { type, id: kandidatId };
    showConfirmDialog.value = true;
};

// Confirm vote selection
const confirmVote = () => {
    if (!pendingSelection.value) return;

    if (pendingSelection.value.type === 'bem') {
        selectedBem.value = pendingSelection.value.id;
        // Move to next step after selecting BEM
        setTimeout(() => {
            currentStep.value = 'hima';
        }, 500);
    } else {
        selectedHima.value = pendingSelection.value.id;
        // Mark as complete after selecting HIMA
        setTimeout(() => {
            currentStep.value = 'complete';
        }, 500);
    }

    showConfirmDialog.value = false;
    pendingSelection.value = null;
};

// Cancel vote selection
const cancelVote = () => {
    showConfirmDialog.value = false;
    pendingSelection.value = null;
};

// Submit final vote
const submitVote = () => {
    if (!selectedBem.value || !selectedHima.value) return;

    router.post(route('vote.submit'), {
        id_kandidat_bem: selectedBem.value,
        id_kandidat_hima: selectedHima.value,
    }, {
        onSuccess: () => {
            isVoting.value = false;
        },
    });
};

// Warn user before leaving during voting
const handleBeforeUnload = (e: BeforeUnloadEvent) => {
    if (isVoting.value && currentStep.value !== 'complete') {
        e.preventDefault();
        e.returnValue = 'Anda sedang dalam proses voting. Yakin ingin meninggalkan halaman?';
    }
};

// Intercept navigation
const handleInertiaNavigate = (event: any) => {
    if (isVoting.value && currentStep.value !== 'complete') {
        if (!confirm('Anda sedang dalam proses voting. Yakin ingin meninggalkan halaman? Pilihan Anda akan hilang.')) {
            event.preventDefault();
        }
    }
};

let removeInertiaListener: (() => void) | null = null;

onMounted(() => {
    window.addEventListener('beforeunload', handleBeforeUnload);
    removeInertiaListener = router.on('before', handleInertiaNavigate);
});

onUnmounted(() => {
    window.removeEventListener('beforeunload', handleBeforeUnload);
    if (removeInertiaListener) {
        removeInertiaListener();
    }
});

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: currentStep.value === 'bem' ? 'Pemilihan Caka BEM' : currentStep.value === 'hima' ? 'Pemilihan Caka HIMA' : 'Konfirmasi',
        href: '/vote',
    },
];

// Get current kegiatan based on step
const currentKegiatan = computed(() => {
    return currentStep.value === 'bem' ? props.kegiatanBem : props.kegiatanHima;
});

// Get pending kandidat info
const pendingKandidat = computed(() => {
    if (!pendingSelection.value) return null;

    const kegiatan = pendingSelection.value.type === 'bem' ? props.kegiatanBem : props.kegiatanHima;
    return kegiatan.kandidat.find(k => k.id === pendingSelection.value!.id);
});

// handle background image banner
const handleBannerImage = () => {
    if (currentStep.value === 'bem') {
        const namaKegiatan = props.kegiatanBem.nama.toLowerCase().replace(' fmipa', '').replace(/\s+/g, '-');
        return `/images/foto-kegiatan/${namaKegiatan}.webp`;
    } else if (currentStep.value === 'hima') {
        const namaKegiatan = props.kegiatanHima.nama.toLowerCase().replace(/\s+/g, '-');
        return `/images/foto-kegiatan/${namaKegiatan}.webp`;
    } else {
        return '/images/banner-fmipa.webp';
    }
}
</script>

<template>

    <Head :title="title" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-col gap-2 overflow-x-auto">
            <div class="relative flex min-h-[90vh] flex-1 flex-col items-start justify-start pt-20 md:pt-32 lg:pt-40">
                <!-- Background Image -->
                <img :src="handleBannerImage()" alt="Banner"
                    class="absolute inset-0 top-0 w-full object-cover transition-opacity duration-500" />

                <!-- Voting Section (BEM and HIMA) -->
                <div v-if="currentStep !== 'complete'"
                    class="relative mx-auto mt-20 grid w-full max-w-7xl items-start gap-4 px-4 lg:mt-56">
                    <div class="flex flex-col md:flex-row justify-center">
                        <div v-for="kandidat in currentKegiatan.kandidat" :key="kandidat.id"
                            class="max-w-md flex flex-col items-center justify-center gap-6 p-6 relative">
                            <!-- Selected Badge -->
                            <div v-if="(currentStep === 'bem' && selectedBem === kandidat.id) ||
                                (currentStep === 'hima' && selectedHima === kandidat.id)"
                                class="absolute top-4 right-4 z-10 bg-green-500 text-white px-3 py-1 rounded-full text-sm font-semibold flex items-center gap-1">
                                <CheckCircle2 class="w-4 h-4" />
                                Dipilih
                            </div>

                            <img :src="`/storage/${kandidat.foto}`" :alt="`Kandidat ${kandidat.no_urut}`"
                                class="w-full rounded-lg shadow-lg" :class="[
                                    (currentStep === 'bem' && selectedBem === kandidat.id) ||
                                        (currentStep === 'hima' && selectedHima === kandidat.id)
                                        ? 'ring-4 ring-green-500'
                                        : ''
                                ]" />

                            <div class="justify-center items-center">
                                <AlertDialog>
                                    <AlertDialogTrigger as-child>
                                        <Button @click="handleVoteClick(currentStep, kandidat.id)">
                                            {{ (currentStep === 'bem' && selectedBem === kandidat.id) ||
                                                (currentStep === 'hima' && selectedHima === kandidat.id)
                                                ? 'Terpilih'
                                                : 'Vote' }}
                                        </Button>
                                    </AlertDialogTrigger>
                                </AlertDialog>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Complete Section -->
                <div v-else class="relative mx-auto mt-20 w-full max-w-3xl px-4 lg:mt-56">
                    <div class="bg-white/95 backdrop-blur-sm rounded-lg shadow-2xl p-8">
                        <div class="flex flex-col items-center justify-center gap-4 mb-8">
                            <CheckCircle2 class="w-20 h-20 text-green-500" />
                            <h1 class="text-2xl md:text-3xl font-black text-center">KONFIRMASI PILIHAN ANDA</h1>
                            <p class="text-center text-muted-foreground">
                                Pastikan pilihan Anda sudah benar sebelum mengirim
                            </p>
                        </div>

                        <div class="space-y-6">
                            <!-- BEM Selection -->
                            <div class="border-2 border-primary rounded-lg p-6 bg-primary/5">
                                <h3 class="font-bold text-lg mb-3 text-center">Pilihan Caka BEM FMIPA</h3>
                                <div v-if="selectedBem" class="text-center">
                                    <p class="text-lg font-semibold">
                                        Nomor Urut {{kegiatanBem.kandidat.find(k => k.id === selectedBem)?.no_urut}}
                                    </p>
                                    <p class="text-base font-medium">
                                        {{kegiatanBem.kandidat.find(k => k.id === selectedBem)?.mahasiswa.find(m =>
                                        m.pivot.jabatan === 'ketua')?.nama }}
                                    </p>
                                    <p class="text-sm text-muted-foreground">
                                        {{kegiatanBem.kandidat.find(k => k.id === selectedBem)?.mahasiswa.find(m =>
                                        m.pivot.jabatan === 'wakil')?.nama }}
                                    </p>
                                </div>
                            </div>

                            <!-- HIMA Selection -->
                            <div class="border-2 border-primary rounded-lg p-6 bg-primary/5">
                                <h3 class="font-bold text-lg mb-3 text-center">
                                    Pilihan Caka HIMA {{ auth.user.programStudi.nama }}
                                </h3>
                                <div v-if="selectedHima" class="text-center">
                                    <p class="text-lg font-semibold">
                                        Nomor Urut {{kegiatanHima.kandidat.find(k => k.id === selectedHima)?.no_urut}}
                                    </p>
                                    <p class="text-base font-medium">
                                        {{kegiatanHima.kandidat.find(k => k.id === selectedHima)?.mahasiswa.find(m =>
                                        m.pivot.jabatan === 'ketua')?.nama }}
                                    </p>
                                    <p class="text-sm text-muted-foreground">
                                        {{kegiatanHima.kandidat.find(k => k.id === selectedHima)?.mahasiswa.find(m =>
                                        m.pivot.jabatan === 'wakil')?.nama }}
                                    </p>
                                </div>
                            </div>

                            <div class="bg-yellow-50 border-2 border-yellow-400 rounded-lg p-4">
                                <div class="flex gap-2">
                                    <TriangleAlert class="h-5 w-5 text-yellow-600 shrink-0 mt-0.5" />
                                    <p class="text-sm text-yellow-800">
                                        <strong>PERHATIAN:</strong> Setelah mengirim, pilihan Anda tidak dapat diubah.
                                        Pastikan pilihan Anda sudah benar.
                                    </p>
                                </div>
                            </div>

                            <div class="grid w-full grid-cols-2 gap-4 mt-8">
                                <Button variant="outline" @click="currentStep = 'bem'"
                                    class="border-primary text-primary hover:bg-primary/10">
                                    Ubah Pilihan
                                </Button>
                                <Button @click="submitVote" class="bg-primary hover:bg-primary/90">
                                    Kirim Pilihan
                                </Button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Confirmation Dialog -->
        <AlertDialog :open="showConfirmDialog" @update:open="showConfirmDialog = $event">
            <AlertDialogContent>
                <AlertDialogHeader>
                    <AlertDialogTitle class="flex flex-col items-center justify-center gap-2">
                        <TriangleAlert class="h-24 w-24 text-red-700" />
                        <h1 class="text-xl font-black text-red-700">ATTENTION</h1>
                    </AlertDialogTitle>
                    <AlertDialogDescription class="text-md text-center">
                        <p class="mb-4">
                            Pemilihan hanya dapat dilakukan sekali dan tidak ada pengulangan.
                        </p>
                        <div v-if="pendingKandidat" class="bg-muted p-4 rounded-lg">
                            <p class="font-semibold text-foreground text-lg">
                                Nomor Urut {{ pendingKandidat.no_urut }}
                            </p>
                            <p class="font-semibold text-foreground">
                                {{pendingKandidat.mahasiswa.find(m => m.pivot.jabatan === 'ketua')?.nama}}
                            </p>
                            <p class="text-sm text-muted-foreground">
                                {{pendingKandidat.mahasiswa.find(m => m.pivot.jabatan === 'wakil')?.nama}}
                            </p>
                        </div>
                        <p class="mt-4">
                            Apabila anda ingin melanjutkan silahkan klik tombol "Lanjutkan"!
                        </p>
                    </AlertDialogDescription>
                </AlertDialogHeader>
                <AlertDialogFooter class="sm:justify-center">
                    <div class="grid w-full grid-cols-2 gap-3">
                        <AlertDialogCancel @click="cancelVote"
                            class="m-0 border border-[#5465D1] text-[#5465D1] hover:bg-[#5465D1]/10">
                            Batal
                        </AlertDialogCancel>
                        <AlertDialogAction @click="confirmVote" class="m-0 bg-[#5465D1] hover:bg-[#5465D1]/90">
                            Lanjutkan
                        </AlertDialogAction>
                    </div>
                </AlertDialogFooter>
            </AlertDialogContent>
        </AlertDialog>
    </AppLayout>
</template>