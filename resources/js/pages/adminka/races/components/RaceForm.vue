<script lang="ts" setup="">
import ErrorBag from '@/components/form-elements/ErrorBag.vue';
import FormDatePicker from '@/components/form-elements/FormDatePicker.vue';
import FormInput from '@/components/form-elements/FormInput.vue';
import InputError from '@/components/form-elements/InputError.vue';
import MarkdownEditor from '@/components/shared/MarkdownEditor/MarkdownEditor.vue';
import { Button } from '@/components/ui/button';
import { useTypedForm } from '@/composables/useTypedForm';
import { Race, RaceCluster } from '@/types/races';
import { Trash } from 'lucide-vue-next';

type RaceFormData = Partial<Race> & {
    clusters: Array<Partial<RaceCluster>>;
    _method?: string;
};

const { race } = defineProps<{ race?: Race }>();

const form = useTypedForm<RaceFormData>(
    (race ?? {
        name: '',
        date: undefined,
        price: undefined,
        is_published: true,
        clusters: [],
        description: undefined,
    }) as RaceFormData,
);

const submit = () => {
    if (form.id) {
        form.put(route('adminka.races.update', form.id));
    } else {
        form.post(route('adminka.races.store'));
    }
};

const addCluster = () => {
    if (!form.clusters) {
        form.clusters = [];
    }
    form.clusters.push({
        name: '',
        start_time: '',
        duration_minutes: 60,
        price: form.price,
    });
};

const removeCluster = (index: number) => {
    form.clusters.splice(index, 1);
};
</script>

<template>
    <form
        @submit.prevent="submit"
        class="mx-auto flex w-full flex-col gap-5 rounded-4xl border border-sidebar-border/80 p-10 md:min-w-xl"
    >
        <h1 class="text-center text-2xl" v-if="form.id">Редактирование гонки</h1>
        <h1 class="text-center text-2xl" v-else>Создание гонки</h1>

        <section class="flex gap-5 max-md:flex-col">
            <FormInput
                class="flex-1"
                v-model="form.name"
                :errorMessage="form.errors.name"
                field-name="name"
                placeholder="Название гонки"
            />

            <FormDatePicker
                v-model="form.date"
                :error-message="form.errors.date"
                field-name="date"
                placeholder="Дата гонки"
            />

            <FormInput
                class="w-32"
                v-model="form.price"
                :errorMessage="form.errors.price"
                field-name="price"
                type="number"
                placeholder="Цена"
            />
        </section>

        <div class="mt-5">
            <h2 class="mb-3 text-xl font-semibold">Кластеры (стартовые группы)</h2>

            <section v-for="(cluster, i) in form.clusters" :key="i" class="mb-4 rounded border p-4">
                <div class="mb-2 flex items-center justify-between">
                    <span class="font-medium">Кластер {{ i + 1 }}</span>
                    <button
                        type="button"
                        @click="() => removeCluster(i)"
                        :disabled="form.clusters.length === 1"
                        class="cursor-pointer text-red-600 hover:text-red-800 disabled:hidden"
                    >
                        <Trash />
                    </button>
                </div>

                <div class="flex flex-col gap-3">
                    <div class="flex max-md:flex-col items-center gap-3">
                        <FormInput
                            v-model="form.clusters[i].name"
                            :field-name="'cluster_name_' + i"
                            placeholder="Название кластера"
                        />

                        <FormInput
                            v-model="form.clusters[i].price"
                            :field-name="'cluster_price_' + i"
                            type="number"
                            placeholder="Цена (руб)"
                        />

                        <FormInput
                            v-model="form.clusters[i].start_time"
                            :field-name="'cluster_start_time_' + i"
                            type="time"
                            placeholder="Время старта"
                            class="flex-1"
                        />

                        <FormInput
                            v-model="form.clusters[i].duration_minutes"
                            :field-name="'cluster_duration_' + i"
                            type="number"
                            placeholder="Продолжительность (мин)"
                            class="flex-1"
                        />
                    </div>
                </div>

                <ErrorBag
                    :errors="[
                        form.errors[`clusters.${i}.name`],
                        form.errors[`clusters.${i}.start_time`],
                        form.errors[`clusters.${i}.duration_minutes`],
                        form.errors[`clusters.${i}.price`],
                    ]"
                    class="mt-2"
                />
            </section>

            <InputError :message="form.errors.clusters" />
            <Button class="w-fit cursor-pointer" @click="addCluster" type="button"> + </Button>
        </div>

        <div class="mt-5">
            <h2 class="mb-3 text-xl font-semibold">Описание</h2>
            <MarkdownEditor v-model="form.description" />
            <InputError :message="form.errors.description" />
        </div>

        <Button class="mt-10 cursor-pointer p-7" v-if="form.id">Сохранить</Button>
        <Button class="mt-10 cursor-pointer p-7" v-else>Создать</Button>
    </form>
</template>
