<script setup lang="ts">
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import { computed, onMounted, onUnmounted, ref } from 'vue';
import { TriangleAlert, CheckCircle2, LoaderCircle } from 'lucide-vue-next';
import type { BreadcrumbItem, Kegiatan, Kandidat } from '@/types';
import { AlertDialog, AlertDialogCancel, AlertDialogContent, AlertDialogDescription, AlertDialogFooter, AlertDialogHeader, AlertDialogTitle, AlertDialogTrigger } from '@/components/ui/alert-dialog';

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
const isMoved = ref(false);
const isSubmitting = ref(false);
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
        }, 200);
    } else {
        selectedHima.value = pendingSelection.value.id;
        // Mark as complete after selecting HIMA
        setTimeout(() => {
            currentStep.value = 'complete';
        }, 200);
    }

    isMoved.value = false;
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
    isSubmitting.value = true;
    router.post(route('vote.store'), {
        id_kandidat_bem: selectedBem.value,
        id_kandidat_hima: selectedHima.value,
    }, {
        onSuccess: () => {
            isVoting.value = false;
            isSubmitting.value = false;
        },
    });
};

// Warn user before leaving during voting
const handleBeforeUnload = (e: BeforeUnloadEvent) => {
    if (isVoting.value && currentStep.value !== 'complete') {
        e.preventDefault();
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

const handleProgramStudi = (id_program_studi: number) => {
    const programStudiMap: Record<number, string> = {
        1: 'Kimia',
        2: 'Fisika',
        3: 'Biologi',
        4: 'Matematika',
        5: 'Farmasi',
        6: 'Informatika',
    };
    return programStudiMap[id_program_studi] || 'Unknown';
}

const programStudiName = computed(() => {
    if (currentKegiatan.value?.programStudi?.nama) {
        return currentKegiatan.value.programStudi.nama;
    }
    // Fallback to user's program studi
    if (auth.value?.user?.programStudi?.nama) {
        return auth.value.user.programStudi.nama;
    }
    // Last fallback using id_program_studi
    if (props.kegiatanHima?.id_program_studi) {
        return handleProgramStudi(props.kegiatanHima.id_program_studi);
    }
    return 'Program Studi';
});
</script>

<template>

    <Head :title="title" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-col gap-2 overflow-x-auto">
            <!-- Hero Section -->
            <div class="relative flex flex-col items-center justify-center">
                <div class="w-full flex justify-between items-start relative">
                    <img src="/images/corner-image.png" alt="" class="w-20 sm:w-40 lg:w-50">
                    <img :src="`/images/${currentKegiatan.foto.replace('jpg', 'png')}`" alt="" class="h-20 sm:h-50 my-auto">
                    <img src="/images/corner-image.png" alt="" class="w-20 sm:w-40 lg:w-50 transform -scale-x-100">
                    <h1
                        class="text-xl sm:text-3xl lg:text-4xl leading-6 sm:leading-10 font-bold text-center text-primary text-shadow-sm text-shadow-background uppercase absolute left-1/2 -translate-x-1/2 top-1/2 -translate-y-1/2
                            drop-shadow-[0_2px_4px_rgba(255,255,255,0.8)] dark:drop-shadow-[0_2px_4px_rgba(0,0,0,0.8)]">
                        {{ currentKegiatan.nama }}
                    </h1>
                </div>
            </div>

            <!-- Voting Section (BEM and HIMA) -->
            <div v-if="currentStep !== 'complete'" class="mx-auto grid w-full max-w-7xl items-start gap-4 px-4 mt-4">
                <div class="flex flex-col md:flex-row gap-8 md:gap-4 lg:gap-8 justify-center items-center">
                    <div v-for="kandidat in currentKegiatan.kandidat" :key="kandidat.id"
                        class="max-w-md flex flex-col items-center justify-center gap-4 p-4">
                        <div class="relative w-full h-full">
                            <!-- Frame image (base) -->
                            <img :src="`/images/frame-${currentStep}.webp`"
                                class="w-full max-w-xs sm:max-w-sm lg:max-w-md" />

                            <div v-if="currentStep === 'bem'">
                                <img :src="kandidat.foto ? `/storage/${kandidat.foto}` : `/images/kotak-kosong.webp`"
                                    :alt="`Kandidat ${kandidat.no_urut}`"
                                    class="absolute bottom-2 right-2 w-auto place-self-end border-none mask-linear-180 mask-linear-from-40% mask-linear-to-85%"
                                    :class="kandidat.foto ? `max-h-56 sm:max-h-72` : `max-h-40 sm:max-h-48 right-8`" />
                                <h1 class="absolute z-20 top-12 sm:top-16 poppins-font font-black text-white text-sm lg:text-base -rotate-90"
                                    style="text-shadow: 0px 12px 0px rgba(255,255,255,0.3), 0px 24px 0px rgba(255,255,255,0.1);">
                                    PASLON BEM
                                </h1>
                                <h1
                                    class="absolute z-20 bottom-[0.9rem] left-[0.9rem] sm:bottom-4 sm:left-4 lg:bottom-[1.3rem] lg:left-[1.3rem] poppins-font font-black text-white text-xl sm:text-2xl">
                                    {{ kandidat.no_urut }}
                                </h1>
                            </div>

                            <div v-if="currentStep === 'hima'">
                                <img :src="kandidat.foto ? `/storage/${kandidat.foto}` : `/images/kotak-kosong.webp`"
                                    :alt="`Kandidat ${kandidat.no_urut}`"
                                    class="absolute -right-2 w-auto place-self-end border-none mask-linear-180 mask-linear-from-40% mask-linear-to-85%"
                                    :class="kandidat.foto ? `max-h-64 sm:max-h-72 lg:max-h-80` : `max-h-40 sm:max-h-48 right-8`" />
                                <h1 class="absolute z-20 top-16 sm:top-20 poppins-font font-black text-white text-base lg:text-lg -rotate-90"
                                    style="text-shadow: 0px 12px 0px rgba(255,255,255,0.3), 0px 24px 0px rgba(255,255,255,0.1);">
                                    CAKAHIMA
                                </h1>
                                <h1
                                    class="absolute z-20 bottom-7 left-7 sm:bottom-8 sm:left-8 lg:bottom-9 lg:left-9 poppins-font font-black text-white text-xl sm:text-2xl">
                                    {{ kandidat.no_urut }}
                                </h1>
                            </div>
                        </div>

                        <!-- Nama Kandidat dan Program Studi -->
                        <div v-if="!kandidat.mahasiswa.find(m => m.pivot.jabatan === 'ketua')?.nama.includes('Kotak Kosong')"
                            class="text-center">
                            <p :class="currentStep === 'hima' && 'flex flex-col gap-1'" class="text-sm sm:text-base font-semibold">
                                {{kandidat.mahasiswa.find(m => m.pivot.jabatan === 'ketua')?.nama}}
                                <span class="text-sm text-muted-foreground">
                                    ({{handleProgramStudi(kandidat.mahasiswa.find(m => m.pivot.jabatan ===
                                        'ketua')?.id_program_studi!)}}' {{kandidat.mahasiswa.find(m => m.pivot.jabatan ===
                                        'ketua')?.angkatan.toString().slice(-2)}})
                                </span>
                            </p>
                            <p v-if="currentStep === 'bem'" class="text-sm sm:text-base font-semibold">
                                {{kandidat.mahasiswa.find(m => m.pivot.jabatan === 'wakil')?.nama}}
                                <span class="text-sm text-muted-foreground">
                                    ({{handleProgramStudi(kandidat.mahasiswa.find(m => m.pivot.jabatan ===
                                        'wakil')?.id_program_studi!)}}' {{kandidat.mahasiswa.find(m => m.pivot.jabatan ===
                                        'wakil')?.angkatan.toString().slice(-2)}})
                                </span>
                            </p>
                        </div>

                        <p v-else class="font-semibold text-xl sm:text-2xl sm:py-2">Kotak Kosong</p>

                        <div class="justify-center items-center w-full">
                            <AlertDialog>
                                <AlertDialogTrigger as-child class="w-full">
                                    <Button @click="handleVoteClick(currentStep, kandidat.id)" :disabled="isMoved"
                                        class="w-full text-base" size="lg">
                                        {{ (currentStep === 'bem' && selectedBem === kandidat.id) ||
                                            (currentStep === 'hima' && selectedHima === kandidat.id)
                                            ? 'Terpilih'
                                            : 'Vote' }}
                                        {{ kandidat.no_urut }}
                                    </Button>
                                </AlertDialogTrigger>
                            </AlertDialog>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Complete Section -->
            <div v-else class="relative mx-auto my-4 w-full max-w-3xl px-4">
                <div class="bg-background/95 backdrop-blur-sm rounded-lg border shadow-lg p-8">
                    <div class="flex flex-col items-center justify-center gap-4 mb-8">
                        <CheckCircle2 class="w-20 h-20 text-green-500" />
                        <h1 class="text-xl md:text-2xl font-black text-center">KONFIRMASI PILIHAN ANDA</h1>
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
                                <p v-if="kegiatanBem.kandidat.find(k => k.id === selectedBem)?.mahasiswa.find(m =>
                                        m.pivot.jabatan === 'ketua')?.nama.includes('Kotak Kosong')" class="text-sm text-muted-foreground">
                                    Kotak Kosong
                                </p>
                                <span v-else>
                                    <p class="text-sm text-muted-foreground">
                                        {{kegiatanBem.kandidat.find(k => k.id === selectedBem)?.mahasiswa.find(m =>
                                            m.pivot.jabatan === 'ketua')?.nama}}
                                    </p>
                                    <p class="text-sm text-muted-foreground">
                                        {{kegiatanBem.kandidat.find(k => k.id === selectedBem)?.mahasiswa.find(m =>
                                            m.pivot.jabatan === 'wakil')?.nama}}
                                    </p>
                                </span>
                            </div>
                        </div>

                        <!-- HIMA Selection -->
                        <div class="border-2 border-primary rounded-lg p-6 bg-primary/5">
                            <h3 class="font-bold text-lg mb-3 text-center">
                                Pilihan Caka HIMA {{ programStudiName }}
                            </h3>
                            <div v-if="selectedHima" class="text-center">
                                <p class="text-lg font-semibold">
                                    Nomor Urut {{kegiatanHima.kandidat.find(k => k.id === selectedHima)?.no_urut}}
                                </p>
                                <p v-if="kegiatanHima.kandidat.find(k => k.id === selectedHima)?.mahasiswa.find(m =>
                                        m.pivot.jabatan === 'ketua')?.nama.includes('Kotak Kosong')" class="text-sm text-muted-foreground">
                                    Kotak Kosong
                                </p>
                                <p class="text-sm text-muted-foreground">
                                    {{kegiatanHima.kandidat.find(k => k.id === selectedHima)?.mahasiswa.find(m =>
                                        m.pivot.jabatan === 'ketua')?.nama}}
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
                        
                        <Button @click="submitVote" :disabled="isSubmitting" class="w-full bg-primary hover:bg-primary/90">
                            <LoaderCircle v-if="isSubmitting" class="size-4 animate-spin" />
                            Kirim Pilihan
                        </Button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Confirmation Dialog -->
        <AlertDialog :open="showConfirmDialog" @update:open="showConfirmDialog = $event">
            <AlertDialogContent>
                <AlertDialogHeader>
                    <TriangleAlert class="size-16 text-red-700 mx-auto" />
                    <AlertDialogTitle class="mt-2 text-xl text-center">
                        Perhatian
                    </AlertDialogTitle>
                    <AlertDialogDescription class="text-md text-center">
                        <p class="mb-4">
                            Pemilihan hanya dapat dilakukan sekali dan tidak ada pengulangan.
                        </p>
                        <div v-if="pendingKandidat" class="bg-muted p-4 rounded-lg">
                            <p class="font-semibold text-foreground text-lg mb-2">
                                Nomor Urut {{ pendingKandidat.no_urut }}
                            </p>
                            <p v-if="pendingKandidat.mahasiswa.find(m => m.pivot.jabatan)?.nama.includes('Kotak Kosong')" class="text-sm font-normal text-foreground">
                                Kotak Kosong
                            </p>
                            <span v-else>
                                <p class="text-sm font-normal text-foreground">
                                    {{pendingKandidat.mahasiswa.find(m => m.pivot.jabatan === 'ketua')?.nama}}
                                    <span class="text-sm text-muted-foreground">
                                        ({{handleProgramStudi(pendingKandidat.mahasiswa.find(m => m.pivot.jabatan ===
                                            'ketua')?.id_program_studi!)}}' {{pendingKandidat.mahasiswa.find(m =>
                                            m.pivot.jabatan ===
                                            'ketua')?.angkatan.toString().slice(-2)}})
                                    </span>
                                </p>
                                <p v-if="currentStep === 'bem'" class="text-sm font-normal text-foreground">
                                    {{pendingKandidat.mahasiswa.find(m => m.pivot.jabatan === 'wakil')?.nama}}
                                    <span class="text-sm text-muted-foreground">
                                        ({{handleProgramStudi(pendingKandidat.mahasiswa.find(m => m.pivot.jabatan ===
                                            'wakil')?.id_program_studi!)}}' {{pendingKandidat.mahasiswa.find(m =>
                                            m.pivot.jabatan ===
                                            'wakil')?.angkatan.toString().slice(-2)}})
                                    </span>
                                </p>
                            </span>
                        </div>
                    </AlertDialogDescription>
                </AlertDialogHeader>
                <AlertDialogFooter class="sm:justify-center">
                    <div class="grid w-full grid-cols-2 gap-3">
                        <AlertDialogCancel @click="cancelVote" :disabled="isMoved"
                            class="m-0 border border-primary text-primary hover:bg-primary/10">
                            Batal
                        </AlertDialogCancel>
                        <Button @click="confirmVote" :disabled="isMoved" variant="default">
                            Lanjutkan
                        </Button>
                    </div>
                </AlertDialogFooter>
            </AlertDialogContent>
        </AlertDialog>
    </AppLayout>
</template>