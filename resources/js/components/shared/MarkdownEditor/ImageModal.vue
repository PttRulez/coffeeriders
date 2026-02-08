<script lang="ts" setup="">
import Modal from '@/components/shared/Modal.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { computed, ref, watch } from 'vue';
import axios from 'axios';

const openImgModal = defineModel<boolean>('open', { default: false });

const emit = defineEmits<{
    addImage: [data: { url?: string; file?: File }]
}>()

const url = ref('');
const file = ref<File | null>(null);

// Кнопка активна только если есть URL ИЛИ файл
const isButtonDisabled = computed(() => {
    return !url.value && !file.value;
});

// Когда выбирают файл, стираем URL
watch(file, (newFile) => {
    if (newFile) {
        url.value = '';
    }
});

const handleFileChange = (event: Event) => {
  const target = event.target as HTMLInputElement
  file.value = target.files?.[0] || null
}

const handleAdd = () => {
    if (url.value) {
        emit('addImage', { url: url.value })
    } else if (file.value) {
        emit('addImage', { file: file.value })
    }

    // Сбрасываем форму
    url.value = ''
    file.value = null
    openImgModal.value = false
}
</script>


<template>
    <Modal title="Добавление изображения" v-model:open="openImgModal">
        <Input placeholder="URL изображения" v-model="url"/>
        <Input
          type="file"
          accept="image/*"
          class="max-w-[250px]"
          @change="handleFileChange"
        />
        <Button :disabled="isButtonDisabled" @click="handleAdd">Добавить</Button>
    </Modal>
</template>
