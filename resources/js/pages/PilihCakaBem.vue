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
                        <div>
                            <div class=" max-w-md flex flex-col items-center justify-center gap-6 p-6">
                                <img src="/images/Caka1.png" alt="Caka1" class="w-md" />
                                <div class="justify-center items-center">
                                    <AlertDialog>
                                        <AlertDialogTrigger as-child>
                                            <Button> Vote </Button>
                                        </AlertDialogTrigger>
                                        <AlertDialogContent>
                                            <AlertDialogHeader>
                                                <AlertDialogTitle class="flex flex-col items-center justify-center gap-2">
                                                    <TriangleAlert class="h-24 w-24 text-red-700" />
                                                    <h1 class="text-xl font-black text-red-700">ATTENTION</h1>
                                                </AlertDialogTitle>
                                                <AlertDialogDescription class="text-md text-center">
                                                    Pemilihan hanya dapat dilakukan sekali dan tidak ada pengulangan, apabila
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
                        <div>
                            <!-- Caka 2 -->
                            <div class=" max-w-md flex flex-col items-center justify-center gap-6 p-6">
                                <img src="/images/Caka1.png" alt="Caka1" class="w-md" />
                                <div class="justify-center items-center">
                                    <AlertDialog>
                                        <AlertDialogTrigger as-child>
                                            <Button> Vote </Button>
                                        </AlertDialogTrigger>
                                        <AlertDialogContent>
                                            <AlertDialogHeader>
                                                <AlertDialogTitle class="flex flex-col items-center justify-center gap-2">
                                                    <TriangleAlert class="h-24 w-24 text-red-700" />
                                                    <h1 class="text-xl font-black text-red-700">ATTENTION</h1>
                                                </AlertDialogTitle>
                                                <AlertDialogDescription class="text-md text-center">
                                                    Pemilihan hanya dapat dilakukan sekali dan tidak ada pengulangan, apabila
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
                                                    <AlertDialogAction variant="outline" @click="() => {
                                                        toast('Pilihan berhasil disimpan', {
                                                            description: 'Sunday, December 03, 2023 at 9:00 AM',
                                                        })
                                                    }" as-child class="m-0">
                                                        <Link href="/cakahima" class="bg-[#5465D1] hover:bg-[#5465D1]/90">
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
