<script lang="ts" setup="">
import ErrorBag from '@/components/form-elements/ErrorBag.vue';
import FormCheckBox from '@/components/form-elements/FormCheckBox.vue';
import FormDatePicker from '@/components/form-elements/FormDatePicker.vue';
import FormInput from '@/components/form-elements/FormInput.vue';
import FormSelect from '@/components/form-elements/FormSelect.vue';
import InputError from '@/components/form-elements/InputError.vue';
import MarkdownEditor from '@/components/shared/MarkdownEditor/MarkdownEditor.vue';
import { Button } from '@/components/ui/button';
import { useTypedForm } from '@/composables/useTypedForm';
import { RaceType } from '@/types/enums';
import { Race, RaceCluster } from '@/types/races';
import { today } from '@internationalized/date';
import { Trash } from 'lucide-vue-next';
import { computed } from 'vue';

type RaceFormData = Partial<Race> & {
    cover_image: File | null;
    clusters: Array<Partial<RaceCluster>>;
    _method?: string;
};

const { race } = defineProps<{ race?: Race }>();

const form = useTypedForm<RaceFormData>({
    id: race?.id,
    name: race?.name,
    location: race?.location ?? null,
    description: race?.description ?? null,
    race_types: race?.race_types?.length ? race.race_types : [RaceType.Road],
    rank: race?.rank ?? 2,
    in_our_studio: race?.in_our_studio ?? false,
    organizer_name: race?.organizer_name ?? null,
    organizer_website_url: race?.organizer_website_url ?? null,
    registration_url: race?.registration_url ?? null,
    yandex_map_url: race?.yandex_map_url ?? null,
    cover_img_url: race?.cover_img_url ?? null,
    cover_image: null,
    date: race?.date ?? today('Europe/Moscow').toString(),
    price: race?.price,
    is_published: race?.is_published ?? true,
    clusters: race?.clusters ?? [],
    _method: race ? 'PUT' : undefined,
});

const isIndoorInStudio = computed(() => form.in_our_studio);

const raceTypeOptions: Array<{ value: RaceType; label: string }> = [
    { value: RaceType.Road, label: 'Шоссе' },
    { value: RaceType.MTB, label: 'МТБ' },
    { value: RaceType.Gravel, label: 'Грэвел' },
    { value: RaceType.Indoor, label: 'Indoor' },
    { value: RaceType.Track, label: 'Track' },
    { value: RaceType.Cyclocross, label: 'Cyclocross' },
];

const rankOptions = [
    { value: 1, label: '1' },
    { value: 2, label: '2' },
    { value: 3, label: '3' },
];

const toggleRaceType = (value: RaceType) => {
    if (form.race_types.includes(value)) {
        form.race_types = form.race_types.filter((it) => it !== value);
        return;
    }

    form.race_types = [...form.race_types, value];
};

const handleInOurStudioChange = (value: boolean) => {
    if (!value) {
        form.clusters = [];
        return;
    }

    if (form.clusters.length === 0) {
        addCluster();
    }
};

const submit = () => {
    form.post(form.id ? route('adminka.races.update', form.id) : route('adminka.races.store'), {
        forceFormData: true,
    });
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

        <section class="grid gap-5 md:grid-cols-2">
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
                class="flex-1"
                v-model="form.location"
                :errorMessage="form.errors.location"
                field-name="location"
                placeholder="Место проведения (город)"
            />

            <FormSelect
                field-name="rank"
                v-model="form.rank"
                :error-message="form.errors.rank"
                :options="rankOptions"
                placeholder="Ранг гонки"
            />

            <div class="md:col-span-2">
                <p class="mb-2 text-sm text-muted-foreground">Типы гонки</p>
                <div class="flex flex-wrap gap-3">
                    <label
                        v-for="option in raceTypeOptions"
                        :key="option.value"
                        class="inline-flex cursor-pointer items-center gap-2 rounded-md border px-3 py-2 text-sm"
                    >
                        <input
                            type="checkbox"
                            :checked="form.race_types.includes(option.value)"
                            @change="toggleRaceType(option.value)"
                        />
                        <span>{{ option.label }}</span>
                    </label>
                </div>
                <InputError :message="form.errors.race_types || form.errors['race_types.0']" />
            </div>

            <FormInput
                class="w-32"
                v-model="form.price"
                :errorMessage="form.errors.price"
                field-name="price"
                type="number"
                placeholder="Цена"
            />

            <FormInput
                v-model="form.yandex_map_url"
                :errorMessage="form.errors.yandex_map_url"
                field-name="yandex_map_url"
                placeholder="Ссылка Яндекс.Карт или координаты: 60.630690, 30.121022"
            />

            <FormInput
                accept="image/*"
                field-name="cover_image"
                type="file"
                class="max-w-[250px] cursor-pointer"
                label="Обложка гонки"
                :error-message="form.errors.cover_image"
                @change="
                    (e: Event) =>
                        (form.cover_image = (e.target as HTMLInputElement).files?.[0] ?? null)
                "
            />
        </section>

        <img
            v-if="form.cover_img_url && !form.cover_image"
            :src="form.cover_img_url"
            alt="Текущая обложка гонки"
            class="h-40 max-w-xs rounded-lg object-cover"
        />

        <FormCheckBox
            fieldName="in_our_studio"
            v-model="form.in_our_studio"
            label="Гонка в нашей студии (с регистрацией на сайте)"
            @change="handleInOurStudioChange"
        />

        <FormInput
            v-model="form.organizer_name"
            :errorMessage="form.errors.organizer_name"
            field-name="organizer_name"
            placeholder="Имя организатора"
        />

        <FormInput
            v-model="form.organizer_website_url"
            :errorMessage="form.errors.organizer_website_url"
            field-name="organizer_website_url"
            placeholder="Сайт организатора гонки"
        />

        <FormInput
            v-if="!form.in_our_studio"
            v-model="form.registration_url"
            :errorMessage="form.errors.registration_url"
            field-name="registration_url"
            placeholder="Ссылка на регистрацию у внешнего организатора"
        />

        <div v-if="isIndoorInStudio" class="mt-5">
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
                    <div class="flex items-center gap-3 max-md:flex-col">
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
