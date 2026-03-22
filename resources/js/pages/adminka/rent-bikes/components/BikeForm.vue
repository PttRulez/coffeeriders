<script lang="ts" setup="">
import ErrorBag from '@/components/form-elements/ErrorBag.vue';
import FormInput from '@/components/form-elements/FormInput.vue';
import FormSelect, { SelectOption } from '@/components/form-elements/FormSelect.vue';
import InputError from '@/components/form-elements/InputError.vue';
import MarkdownEditor from '@/components/shared/MarkdownEditor/MarkdownEditor.vue';
import { Button } from '@/components/ui/button';
import { Textarea } from '@/components/ui/textarea';
import { useTypedForm } from '@/composables/useTypedForm';
import { BikeCategory } from '@/types/enums';
import { Bike } from '@/types/rent-bikes';
import { router } from '@inertiajs/vue3';
import { Star, Trash } from 'lucide-vue-next';
import { toast } from 'vue-sonner';

interface BikeForm extends Partial<Bike>, Record<string, any> {
    images: File[];
    is_published?: boolean;
    prices: Array<{
        period: string;
        price: number;
    }>;
}

const props = defineProps<{ bike?: Bike }>();

const form = useTypedForm<BikeForm>(
    props.bike
        ? {
              ...props.bike,
              images: [],
              _method: 'PUT',
          }
        : {
              category: undefined,
              full_description: undefined,
              id: undefined,
              images: [],
              name: undefined,
              short_description: undefined,
              is_published: true,
              prices: [{ price: 2500, period: 'сутки' }],
          },
);

const bikeCategories: SelectOption[] = [
    {
        label: 'Шоссер',
        value: BikeCategory.Road,
    },
    {
        label: 'Гравийник',
        value: BikeCategory.Gravel,
    },
    {
        label: 'МТБшка',
        value: BikeCategory.MTB,
    },
];

const validatePrices = () => {
    if (!form.prices || !Array.isArray(form.prices)) {
        form.setError('prices', 'Цены обязательны');
        return false;
    }

    const hasValidPrice = form.prices.some((p) => p.price && p.price.toString().trim() !== '');

    if (!hasValidPrice) {
        form.setError('prices', 'Добавьте хотя бы одну цену');
        return false;
    }

    delete form.errors.prices;
    return true;
};

const submit = () => {
    if (!validatePrices()) {
        return;
    }

    if (form.id) {
        form['_method'] = 'PUT';
        form.post(route('adminka.rent-bikes.update', form.id), {
            forceFormData: true,
        });
    } else {
        form.post(route('adminka.rent-bikes.store'), {
            forceFormData: true,
        });
    }
};

const deleteImage = (imageId: number) => {
    if (!form.id) return;
    if (!confirm('Удалить фото?')) return;

    router.delete(route('adminka.rent-bikes.images.destroy', { bike: form.id, image: imageId }), {
        onSuccess: () => {
            toast.success('Фотография удалена');
            router.reload({ only: ['bike'] });
        },
    });
};

const setPrimaryImage = (imageId: number) => {
    if (!form.id) return;

    router.patch(
        route('adminka.rent-bikes.images.set-primary', { bike: form.id, image: imageId }),
        {},
        {
            onSuccess: () => {
                toast.success('Главное фото обновлено');
                router.reload({ only: ['bike'] });
            },
        },
    );
};
</script>

<template>
    <form
        @submit.prevent="submit"
        class="mx-auto flex w-full flex-col gap-5 rounded-4xl border border-sidebar-border/80 p-10 md:min-w-xl"
    >
        <h1 class="text-center text-2xl" v-if="form.id">Правка вела</h1>
        <h1 class="text-center text-2xl" v-else>Форма нового вела</h1>
        <FormInput
            v-model="form.name"
            :errorMessage="form.errors.name"
            field-name="name"
            placeholder="название велика"
        />

        <label class="inline-flex items-center gap-2 text-sm text-muted-foreground">
            <input type="checkbox" v-model="form.is_published" />
            <span>Опубликован</span>
        </label>

        <FormSelect
            v-model="form.category"
            :errorMessage="form.errors.category"
            :options="bikeCategories"
            placeholder="категория"
            field-name=""
        />

        <section v-for="(_, i) in form.prices" :key="i">
            <div class="flex items-center gap-2 md:gap-10">
                <FormInput
                    v-model="form.prices[i].price"
                    type="number"
                    :field-name="'price' + ' ' + i"
                    placeholder="цена"
                    class="max-md:px-2 max-md:text-sm"
                />
                <FormInput
                    v-model="form.prices[i].period"
                    :field-name="'period' + ' ' + i"
                    placeholder="период"
                    class="max-md:px-2 max-md:text-sm"
                />
                <button
                    type="button"
                    @click="() => form.prices.splice(i, 1)"
                    :disabled="form.prices.length === 1"
                    class="cursor-pointer text-red-600 hover:text-red-800 disabled:hidden"
                >
                    <Trash />
                </button>
            </div>
            <ErrorBag
                :errors="[form.errors[`prices.${i}.price`], form.errors[`prices.${i}.period`]]"
                class="mt-5"
            />
        </section>

        <InputError :message="form.errors.prices" />
        <Button class="w-fit cursor-pointer" @click="() => form.prices.push({})" type="button"
            >Добавить цену</Button
        >

        <FormInput
            @change="form.images = Array.from(($event.target as HTMLInputElement).files || [])"
            field-name="images"
            type="file"
            class="cursor-pointer max-w-[250px]"
            enctype="multipart/form-data"
            button-text="Фотки велосипеда"
            multiple
        />

        <section v-if="props.bike?.images?.length" class="flex flex-col gap-3">
            <h2 class="text-lg">Текущие фото</h2>
            <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                <div
                    v-for="img in props.bike.images"
                    :key="img.id"
                    class="flex flex-col gap-2 rounded-2xl border border-sidebar-border/80 p-3"
                >
                    <img :src="img.url" :alt="img.alt || props.bike?.name" class="rounded-xl" />
                    <div class="flex items-center justify-between gap-2">
                        <Button
                            type="button"
                            variant="secondary"
                            class="h-9 px-3"
                            @click="setPrimaryImage(img.id)"
                            :disabled="img.is_primary"
                        >
                            <Star class="mr-2" />
                            {{ img.is_primary ? 'Главная' : 'Сделать главной' }}
                        </Button>
                        <Button
                            type="button"
                            variant="destructive"
                            class="h-9 px-3"
                            @click="deleteImage(img.id)"
                        >
                            <Trash class="mr-2" />
                            Удалить
                        </Button>
                    </div>
                </div>
            </div>
        </section>

        <Textarea
            placeholder="Краткое описание"
            class="resize-none"
            v-model="form.short_description"
        />
        <InputError :message="form.errors.short_description" />

        <MarkdownEditor v-model="form.full_description" />
        <InputError :message="form.errors.full_description" />

        <Button class="mt-10 cursor-pointer p-7" v-if="form.id">Сохранить</Button>
        <Button class="mt-10 cursor-pointer p-7" v-else>Создать</Button>
    </form>
</template>
