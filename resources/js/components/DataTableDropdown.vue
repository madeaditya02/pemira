<script setup lang="ts">
import { ref } from 'vue';
import { Button } from '@/components/ui/button';
import { MoreVertical, Pencil, Trash2 } from 'lucide-vue-next';
import { Dialog, DialogTrigger } from '@/components/ui/dialog';
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';

// Define props
const props = defineProps<{
    data?: any;
}>();

// Define emits
const emit = defineEmits<{
    edit: [data: any];
    delete: [data: any];
}>();

// Dialog state
const isEditDialogOpen = ref(false);

// Handlers
const handleEdit = () => {
    isEditDialogOpen.value = true;
};

const handleDelete = () => {
    emit('delete', props.data);
};
</script>

<template>
    <DropdownMenu>
        <DropdownMenuTrigger as-child>
            <Button variant="ghost" class="w-8 h-8 p-0">
                <span class="sr-only">Open menu</span>
                <MoreVertical class="w-4 h-4" />
            </Button>
        </DropdownMenuTrigger>
        <DropdownMenuContent align="end">
            <!-- Edit Dialog with Slot -->
            <Dialog v-model:open="isEditDialogOpen">
                <DialogTrigger as-child>
                    <DropdownMenuItem class="flex items-center" @select.prevent>
                        <Pencil class="w-4 h-4 mr-1" />
                        Ubah
                    </DropdownMenuItem>
                </DialogTrigger>
                <!-- Slot untuk form edit yang dinamis -->
                <slot name="edit-form" :data="data" :close="() => isEditDialogOpen = false" />
            </Dialog>

            <!-- Delete MenuItem -->
            <DropdownMenuItem variant="destructive" class="flex items-center" @click="handleDelete">
                <Trash2 class="w-4 h-4 mr-1" color="hsl(0 84.2% 60.2%)" />
                Hapus
            </DropdownMenuItem>
        </DropdownMenuContent>
    </DropdownMenu>
</template>