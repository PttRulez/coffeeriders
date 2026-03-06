<script setup lang="ts">
import InputError from '@/components/form-elements/InputError.vue';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Button } from '@/components/ui/button';
import { cn } from '@/lib/utils';
import { ref, computed } from 'vue';
import { Upload } from 'lucide-vue-next';

const model = defineModel<any>();

type Props = {
    class?: string;
    errorMessage?: string;
    fieldName: string;
    label?: string;
    placeholder?: string;
    type?: string;
    buttonText?: string;
    showFileName?: boolean;
};

defineOptions({
    inheritAttrs: false,
});

const props = withDefaults(defineProps<Props>(), {
    type: 'text',
    buttonText: 'Выбрать файл',
    showFileName: true,
});

const fileInputRef = ref<HTMLInputElement>();
const selectedFileName = ref<string>('');

const handleFileSelect = (event: Event) => {
    const input = event.target as HTMLInputElement;
    const file = input.files?.[0];
    if (file) {
        selectedFileName.value = file.name;
    } else {
        selectedFileName.value = '';
    }
};

const openFileDialog = () => {
    fileInputRef.value?.click();
};

const isFileInput = computed(() => props.type === 'file');
</script>

<template>
    <div class="grid gap-2">
        <div v-if="props.label" class="flex items-center justify-between">
            <Label :for="props.fieldName" class="md:text-lg">
                {{ props.label }}
            </Label>

            <slot name="additionToLabel" />
        </div>

        <!-- Custom file input with button -->
        <template v-if="isFileInput">
            <input
                ref="fileInputRef"
                :id="props.fieldName"
                :name="props.fieldName"
                type="file"
                class="sr-only"
                @change="handleFileSelect"
                v-bind="$attrs"
                @input="$emit('change', $event)"
            />

            <div class="flex flex-col gap-2">
                <Button
                    type="button"
                    variant="outline"
                    @click="openFileDialog"
                    :class="cn('w-full justify-start text-left font-normal', props.class)"
                >
                    <Upload class="mr-2 h-4 w-4" />
                    {{ props.buttonText }}
                </Button>

                <span v-if="props.showFileName && selectedFileName" class="text-sm text-muted-foreground">
                    Выбран файл: {{ selectedFileName }}
                </span>
            </div>
        </template>

        <!-- Regular input for other types -->
        <Input
            v-else
            v-model="model"
            :id="props.fieldName"
            :name="props.fieldName"
            :type="props.type"
            :class="cn('p-6 md:text-xl text-base', props.class)"
            :placeholder="props.placeholder"
            autocomplete="off"
            v-bind="$attrs"
        />

        <InputError class="text-xs!" :message="props.errorMessage" />
    </div>
</template>

<style scoped>
:deep(input[type='number'])::-webkit-outer-spin-button,
:deep(input[type='number'])::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
}

:deep(input[type='number']) {
    -moz-appearance: textfield;
}
</style>