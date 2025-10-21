<script setup lang="ts">
import { cn } from "@/lib/utils"
import { computed, h, ref } from 'vue';
import { Input } from "@/components/ui/input"
import { type BreadcrumbItem } from '@/types';
import { Button } from "@/components/ui/button"
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, usePage } from '@inertiajs/vue3';
import { Checkbox } from "@/components/ui/checkbox"
import { ChevronDown, ChevronsUpDown } from "lucide-vue-next"
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from "@/components/ui/table"
import type { ColumnFiltersState, ExpandedState, SortingState, VisibilityState } from "@tanstack/vue-table"
import { DropdownMenu, DropdownMenuCheckboxItem, DropdownMenuContent, DropdownMenuTrigger } from "@/components/ui/dropdown-menu"
import { createColumnHelper, FlexRender, getCoreRowModel, getExpandedRowModel, getFilteredRowModel, getPaginationRowModel, getSortedRowModel, useVueTable } from "@tanstack/vue-table"

// define interfaces
export interface Kegiatan {
    id: number;
    kode: string;
    nama: string;
}

// define props
const props = defineProps<{
    // 
}>();

// Page title and breadcrumbs
const page = usePage();
const auth = computed(() => page.props.auth);

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Mahasiswa',
        href: '/users',
    },
];
</script>

<template>
    <Head title="Mahasiswa" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="w-full md:max-w-7xl mx-auto px-4 py-6">
            <h1 class="text-xl md:text-2xl font-bold mb-4">Halaman Mahasiswa</h1>
            <p>Selamat datang di halaman mahasiswa.</p>
        </div>
    </AppLayout>
</template>