<script lang="ts" setup="">
import ErrorBag from '@/components/form/ErrorBag.vue';
import FormInput from '@/components/form/FormInput.vue';
import FormSelect, { SelectOption } from '@/components/form/FormSelect.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/shadecn/button';
import { Textarea } from '@/components/shadecn/textarea';
import BikeForm from '@/pages/adminka/rent-bikes/BikeForm.vue';
import { Bike } from '@/types';
import { BikeCategory } from '@/types/enums';
import { useForm } from '@inertiajs/vue3';
import { Trash } from 'lucide-vue-next';

interface BikeForm extends Partial<Bike>, Record<string, any> {
    img: File | null;
    prices: Array<{
        period: string;
        price: number;
    }>;
}

const { bike } = defineProps<{ bike?: Bike }>();

const form = useForm<BikeForm>(
    bike
        ? {
              ...bike,
              img: null,
              _method: 'PUT',
          }
        : ({
              category: undefined,
              full_description: undefined,
              id: undefined,
              img: null,
              name: undefined,
              short_description: undefined,
              prices: [{ price: 2500, period: 'сутки' }],
          } as BikeForm),
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
    console.log(form);
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
</script>

<template>
    <form @submit.prevent="submit" class="mx-auto flex flex-col gap-5 rounded-4xl border border-sidebar-border/80 p-10 md:min-w-xl">
        <h1 class="text-center text-2xl" v-if="form.id">Правка вела</h1>
        <h1 class="text-center text-2xl" v-else>Форма нового вела</h1>
        <FormInput v-model="form.name" :errorMessage="form.errors.name" field-name="name" placeholder="название велика" />

        <FormSelect v-model="form.category" :errorMessage="form.errors.category" :options="bikeCategories" placeholder="категория" field-name="" />

        <section v-for="(_, i) in form.prices" :key="i">
            <div class="flex items-center gap-2 md:gap-10">
                <FormInput v-model="form.prices[i].price" type="number" :field-name="'price' + ' ' + i" placeholder="цена" class="max-md:px-2 max-md:text-sm" />
                <FormInput v-model="form.prices[i].period" :field-name="'period' + ' ' + i" placeholder="период" class="max-md:px-2 max-md:text-sm" />
                <button
                    type="button"
                    @click="() => form.prices.splice(i, 1)"
                    :disabled="form.prices.length === 1"
                    class="cursor-pointer text-red-600 hover:text-red-800 disabled:hidden"
                >
                    <Trash />
                </button>
            </div>
            <ErrorBag :errors="[form.errors[`prices.${i}.price`], form.errors[`prices.${i}.period`]]" class="mt-5" />
        </section>

        <InputError :message="form.errors.prices" />
        <Button class="w-fit cursor-pointer" @click="() => form.prices.push({})" type="button">Добавить цену</Button>

        <FormInput @input="form.img = $event.target.files[0]" field-name="img" type="file" class="cursor-pointer" enctype="multipart/form-data" />

        <Textarea placeholder="Краткое описание" class="resize-none" v-model="form.short_description" />
        <InputError :message="form.errors.short_description" />

        <Textarea placeholder="Полное описание" class="hidden resize-none" v-model="form.full_description" />
        <InputError class="hidden" :message="form.errors.full_description" />

        <Button class="mt-10 cursor-pointer p-7" v-if="form.id">Сохранить</Button>
        <Button class="mt-10 cursor-pointer p-7" v-else>Создать</Button>
    </form>
</template>
