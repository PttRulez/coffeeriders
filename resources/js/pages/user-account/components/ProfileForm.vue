<script setup lang="ts">
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import FormInput from '@/components/form-elements/FormInput.vue';
import FormSelect from '@/components/form-elements/FormSelect.vue';
import PhoneInput from '@/components/form-elements/PhoneInput.vue';
import { Button } from '@/components/ui/button';
import { useInitials } from '@/composables/useInitials';
import AvatarCropper from '@/pages/user-account/components/AvatarCropper.vue';
import { Pedals } from '@/types/enums';
import { useForm, usePage } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';
import { ref } from 'vue';

const page = usePage();
const { auth } = page.props;
const { getInitials } = useInitials();
const cropperOpen = ref(false);
const sourceAvatarImage = ref<string | null>(null);
const previewAvatarImage = ref<string | null>(auth.user.avatar ?? null);

const form = useForm({
    email: auth.user.email,
    name: auth.user.name,
    height: auth.user.height,
    pedals: auth.user.pedals,
    avatar_image: null as File | null,
    phone: auth.user.phone,
    telegram_username: auth.user.telegram_username,
    weight: auth.user.weight,
});

const pedalOptions = [
    { value: Pedals.Shimano, label: 'Shimano' },
    { value: Pedals.Look, label: 'Look' },
];

const openCropper = (event: Event) => {
    const input = event.target as HTMLInputElement;
    const file = input.files?.[0];
    if (!file) {
        return;
    }

    sourceAvatarImage.value = URL.createObjectURL(file);
    cropperOpen.value = true;
    input.value = '';
};

const closeCropper = () => {
    cropperOpen.value = false;
    if (sourceAvatarImage.value) {
        URL.revokeObjectURL(sourceAvatarImage.value);
    }
    sourceAvatarImage.value = null;
};

const handleCrop = (croppedImage: File) => {
    form.avatar_image = croppedImage;
    if (previewAvatarImage.value?.startsWith('blob:')) {
        URL.revokeObjectURL(previewAvatarImage.value);
    }
    previewAvatarImage.value = URL.createObjectURL(croppedImage);
};

const submit = () => {
    form.post(route('user-account.update-info'), {
        preserveScroll: true,
        forceFormData: true,
    });
};
</script>

<template>
    <form @submit.prevent="submit" class="grid gap-6 md:max-w-sm">
        <div class="mx-auto">
            <Avatar class="size-20">
                <AvatarImage v-if="previewAvatarImage" :src="previewAvatarImage" :alt="form.name" />
                <AvatarFallback>{{ getInitials(form.name || auth.user.name) }}</AvatarFallback>
            </Avatar>
        </div>

        <FormInput
            @change="openCropper"
            field-name="avatar_image"
            type="file"
            label="Аватарка"
            class="cursor-pointer"
            accept="image/*"
            :error-message="form.errors.avatar_image"
        />

        <FormInput
            field-name="email"
            type="email"
            placeholder="мыло"
            v-model="form.email"
            :error-message="form.errors.email"
        />

        <FormInput
            field-name="name"
            placeholder="Ваше имя"
            v-model="form.name"
            :error-message="form.errors.name"
        />

        <PhoneInput
            field-name="phone"
            placeholder="телефон"
            v-model="form.phone"
            :error-message="form.errors.phone"
        />

        <FormInput
            field-name="telegram_username"
            placeholder="telegram"
            v-model="form.telegram_username"
            :error-message="form.errors.telegram_username"
        />

        <PhoneInput
            field-name="phone"
            placeholder="телефон"
            v-model="form.phone"
            :error-message="form.errors.phone"
        />

        <FormInput
            type="number"
            field-name="height"
            placeholder="рост, см"
            v-model="form.height"
            :error-message="form.errors.height"
        />

        <FormInput
            type="number"
            field-name="weight"
            placeholder="вес, кг"
            v-model="form.weight"
            :error-message="form.errors.weight"
        />

        <FormSelect field-name="pedals" :options="pedalOptions" v-model="form.pedals" />

        <Button type="submit" :disabled="form.processing">
            <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" />
            Сохранить данные
        </Button>
    </form>

    <AvatarCropper
        :image="sourceAvatarImage || ''"
        :open="cropperOpen"
        @close="closeCropper"
        @crop="handleCrop"
    />
</template>
