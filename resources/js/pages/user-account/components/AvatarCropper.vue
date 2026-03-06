<script setup lang="ts">
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { ref } from 'vue';
import { CircleStencil, Cropper } from 'vue-advanced-cropper';
import 'vue-advanced-cropper/dist/style.css';

const props = defineProps<{
    image: string;
    open: boolean;
}>();

const emit = defineEmits<{
    (e: 'close'): void;
    (e: 'crop', file: File): void;
}>();

const cropperRef = ref<any>(null);
const saving = ref(false);

const handleSave = async () => {
    if (!props.image || !cropperRef.value) {
        return;
    }

    const result = cropperRef.value.getResult();
    const canvas = result?.canvas;
    if (!canvas) {
        return;
    }

    saving.value = true;
    try {
        const croppedImageFile = await new Promise<File>((resolve, reject) => {
            canvas.toBlob((blob) => {
                if (!blob) {
                    reject(new Error('Failed to create blob from canvas.'));
                    return;
                }
                resolve(new File([blob], 'avatar.png', { type: 'image/png' }));
            }, 'image/png');
        });

        emit('crop', croppedImageFile);
        emit('close');
    } finally {
        saving.value = false;
    }
};

const handleOpenChange = (open: boolean) => {
    if (!open) {
        emit('close');
    }
};
</script>

<template>
    <Dialog :open="open" @update:open="handleOpenChange">
        <DialogContent class="sm:max-w-md">
            <DialogHeader>
                <DialogTitle>Обрезать аватар</DialogTitle>
            </DialogHeader>

            <div class="relative h-64 w-full overflow-hidden rounded-md bg-black/70">
                <Cropper
                    ref="cropperRef"
                    :src="image"
                    :stencil-component="CircleStencil"
                    :stencil-props="{ aspectRatio: 1 }"
                    image-restriction="stencil"
                />
            </div>

            <DialogFooter>
                <Button variant="outline" type="button" @click="emit('close')">Отмена</Button>
                <Button type="button" :disabled="saving" @click="handleSave">Сохранить</Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
