<script setup lang="ts">
import FormInput from '@/components/form-elements/FormInput.vue';
import FormSelect, { SelectOption } from '@/components/form-elements/FormSelect.vue';
import { Button } from '@/components/ui/button';
import {
    Accordion,
    AccordionContent,
    AccordionItem,
    AccordionTrigger,
} from '@/components/ui/accordion';
import { Link, useForm } from '@inertiajs/vue3';
import { Plus, Trash2, X } from 'lucide-vue-next';
import { computed, onBeforeUnmount, ref, watch } from 'vue';
import FormTextArea from '@/components/form-elements/FormTextArea.vue';
import PhoneInput from '@/components/form-elements/PhoneInput.vue';

type ServiceCatalogItem = {
    id: number;
    name: string;
    price_rub: number;
    category: { id: number; name: string } | null;
};

type SparePartCatalogItem = {
    id: number;
    name: string;
    purchase_price_rub: number;
    sale_price_rub: number;
    quantity: number;
    category: { id: number; name: string } | null;
};

type RepairOrder = {
    id: number;
    bike_name: string;
    comment: string;
    client_phone: string | null;
    client_telegram: string | null;
    mechanic_id: number | null;
    status: 'pending' | 'in_work' | 'finished';
    services: Array<{
        id: number;
        name: string;
        category: { id: number; name: string } | null;
        pivot: {
            price_rub: number;
        };
    }>;
    spare_parts: Array<{
        external: boolean;
        workshop_spare_part_id: number | null;
        name: string;
        quantity: number;
        purchase_price_rub: number;
        sale_price_rub: number;
        category_name: string | null;
        stock_quantity: number | null;
    }>;
    photos: Array<{
        id: number;
        photo_url: string;
    }>;
};

type SelectedService = {
    workshop_service_id: number;
    price_rub: number;
    name: string;
    category_name: string | null;
};

type SelectedSparePart = {
    external: boolean;
    workshop_spare_part_id: number | null;
    quantity: number;
    name: string;
    category_name: string | null;
    stock_quantity: number | null;
    purchase_price_rub: number | null;
    sale_price_rub: number | null;
};

type MechanicOption = {
    id: number;
    name: string;
    role: 'admin' | 'regular' | string;
};

const props = defineProps<{
    item: RepairOrder | null;
    serviceCatalog: ServiceCatalogItem[];
    sparePartCatalog: SparePartCatalogItem[];
    mechanics: MechanicOption[];
}>();

const isEdit = computed(() => Boolean(props.item?.id));

const form = useForm({
    bike_name: props.item?.bike_name ?? '',
    comment: props.item?.comment ?? '',
    client_phone: props.item?.client_phone ?? '',
    client_telegram: props.item?.client_telegram ?? '',
    mechanic_id: props.item?.mechanic_id ? String(props.item.mechanic_id) : '',
    status: props.item?.status ?? 'pending',
    services: (props.item?.services ?? []).map((service) => ({
        workshop_service_id: service.id,
        price_rub: service.pivot.price_rub,
        name: service.name,
        category_name: service.category?.name ?? null,
    })) as SelectedService[],
    spare_parts: (props.item?.spare_parts ?? []).map((part) => ({
        external: Boolean(part.external),
        workshop_spare_part_id: part.workshop_spare_part_id,
        quantity: part.quantity,
        name: part.name,
        category_name: part.category_name,
        stock_quantity: part.stock_quantity,
        purchase_price_rub: part.purchase_price_rub,
        sale_price_rub: part.sale_price_rub,
    })) as SelectedSparePart[],
    photos: [] as File[],
    remove_photo_ids: [] as number[],
    _method: isEdit.value ? 'PUT' : undefined,
});

const statusOptions: SelectOption[] = [
    { value: 'pending', label: 'Ожидание' },
    { value: 'in_work', label: 'В работе' },
    { value: 'finished', label: 'Сделано' },
];

const mechanicOptions = computed(() => [
    ...props.mechanics.map((mechanic) => ({
        value: String(mechanic.id),
        label: mechanic.name,
    })),
]);

const ensureStatusCompatibleWithMechanic = () => {
    if (!form.mechanic_id && (form.status === 'in_work' || form.status === 'finished')) {
        form.status = 'pending';
    }
};

watch(() => form.mechanic_id, ensureStatusCompatibleWithMechanic);
watch(() => form.status, ensureStatusCompatibleWithMechanic);
const newPhotoPreviews = ref<Array<{ file: File; url: string }>>([]);

const groupedCatalog = computed(() => {
    const map = new Map<string, ServiceCatalogItem[]>();

    props.serviceCatalog.forEach((service) => {
        const category = service.category?.name ?? 'Без категории';
        if (!map.has(category)) {
            map.set(category, []);
        }
        map.get(category)!.push(service);
    });

    return Array.from(map.entries()).map(([category, services]) => ({ category, services }));
});

const groupedSparePartCatalog = computed(() => {
    const map = new Map<string, SparePartCatalogItem[]>();

    props.sparePartCatalog.forEach((part) => {
        const category = part.category?.name ?? 'Без категории';
        if (!map.has(category)) {
            map.set(category, []);
        }
        map.get(category)!.push(part);
    });

    return Array.from(map.entries()).map(([category, parts]) => ({ category, parts }));
});

const addService = (service: ServiceCatalogItem) => {
    form.services.push({
        workshop_service_id: service.id,
        price_rub: service.price_rub,
        name: service.name,
        category_name: service.category?.name ?? null,
    });
};

const removeService = (index: number) => {
    form.services.splice(index, 1);
};

const addSparePart = (part: SparePartCatalogItem) => {
    const existing = form.spare_parts.find(
        (row) => !row.external && row.workshop_spare_part_id === part.id,
    );
    if (existing) {
        existing.quantity += 1;
        existing.stock_quantity = part.quantity;
        return;
    }

    form.spare_parts.push({
        external: false,
        workshop_spare_part_id: part.id,
        quantity: 1,
        name: part.name,
        category_name: part.category?.name ?? null,
        stock_quantity: part.quantity,
        purchase_price_rub: part.purchase_price_rub,
        sale_price_rub: part.sale_price_rub,
    });
};

const addExternalSparePart = () => {
    form.spare_parts.push({
        external: true,
        workshop_spare_part_id: null,
        quantity: 1,
        name: '',
        category_name: null,
        stock_quantity: null,
        purchase_price_rub: 0,
        sale_price_rub: 0,
    });
};

const removeSparePart = (index: number) => {
    form.spare_parts.splice(index, 1);
};

const clearNewPhotoPreviews = () => {
    newPhotoPreviews.value.forEach((preview) => URL.revokeObjectURL(preview.url));
    newPhotoPreviews.value = [];
};

const onPhotosSelected = (event: Event) => {
    const input = event.target as HTMLInputElement;
    const files = Array.from(input.files ?? []);
    if (!files.length) {
        return;
    }

    form.photos.push(...files);
    newPhotoPreviews.value.push(
        ...files.map((file) => ({
            file,
            url: URL.createObjectURL(file),
        })),
    );

    // Allow selecting the same file again if needed.
    input.value = '';
};

const removeNewPhoto = (index: number) => {
    const preview = newPhotoPreviews.value[index];
    if (preview) {
        URL.revokeObjectURL(preview.url);
    }

    newPhotoPreviews.value.splice(index, 1);
    form.photos.splice(index, 1);
};

const markPhotoForRemove = (photoId: number) => {
    if (!form.remove_photo_ids.includes(photoId)) {
        form.remove_photo_ids.push(photoId);
    }
};

const unmarkPhotoForRemove = (photoId: number) => {
    form.remove_photo_ids = form.remove_photo_ids.filter((id) => id !== photoId);
};

const isPhotoMarkedForRemove = (photoId: number): boolean =>
    form.remove_photo_ids.includes(photoId);

onBeforeUnmount(() => {
    clearNewPhotoPreviews();
});

const submit = () => {
    if (isEdit.value) {
        form.post(route('adminka.workshop.repair-orders.update', props.item!.id), {
            forceFormData: true,
        });
        return;
    }

    form.post(route('adminka.workshop.repair-orders.store'), {
        forceFormData: true,
    });
};

const worksTotalForClient = computed(() =>
    form.services.reduce((sum, service) => sum + (Number(service.price_rub) || 0), 0),
);

const sparePartsTotalForClient = computed(() =>
    form.spare_parts.reduce(
        (sum, part) => sum + (Number(part.quantity) || 0) * (Number(part.sale_price_rub) || 0),
        0,
    ),
);

const totalForClient = computed(() => worksTotalForClient.value + sparePartsTotalForClient.value);

const formatPrice = (price: number): string => `${new Intl.NumberFormat('ru-RU').format(price)} ₽`;
</script>

<template>
    <div class="space-y-6">
        <h1 class="text-xl font-semibold">
            {{ isEdit ? 'Редактирование заказа ремонта' : 'Новый заказ ремонта' }}
        </h1>

        <form @submit.prevent="submit" class="grid gap-6 lg:grid-cols-[1fr_420px]">
            <section class="space-y-4">
                <FormInput
                    field-name="bike_name"
                    v-model="form.bike_name"
                    label="Название велосипеда"
                    :error-message="form.errors.bike_name"
                />

                <div class="grid gap-4 md:grid-cols-2">
                    <PhoneInput
                        field-name="client_phone"
                        v-model="form.client_phone"
                        label="Телефон клиента"
                        :error-message="form.errors.client_phone"
                    />
                    <FormInput
                        field-name="client_telegram"
                        v-model="form.client_telegram"
                        label="Telegram клиента"
                        :error-message="form.errors.client_telegram"
                    />
                </div>

                <FormTextArea
                    field-name="comment"
                    v-model="form.comment"
                    label="Комментарий"
                    :error-message="form.errors.comment"
                />

                <div class="grid gap-4 md:grid-cols-2">
                    <FormSelect
                        field-name="mechanic_id"
                        v-model="form.mechanic_id"
                        label="Механик"
                        :options="mechanicOptions"
                        :error-message="form.errors.mechanic_id"
                    />

                    <FormSelect
                        field-name="status"
                        v-model="form.status"
                        label="Статус"
                        :options="statusOptions"
                        :disabled="!isEdit"
                        :error-message="form.errors.status"
                    />
                </div>

                <div class="rounded-xl border p-4">
                    <div class="mb-3 md:grid grid-cols-3 items-center gap-3">
                        <div></div>
                        <div class="flex items-center justify-center gap-2">
                            <h2 class="text-center text-lg font-semibold">Работы</h2>
                            <p class="text-sm font-semibold text-muted-foreground">
                                {{ formatPrice(worksTotalForClient) }}
                            </p>
                        </div>
                        <div></div>
                    </div>

                    <div v-if="!form.services.length" class="text-sm text-muted-foreground">
                        Пока не добавлено ни одной услуги
                    </div>

                    <div v-else class="space-y-2">
                        <div
                            v-for="(service, index) in form.services"
                            :key="`${service.workshop_service_id}-${index}`"
                            class="grid items-center gap-2 rounded-lg border p-3 md:grid-cols-[1fr_140px_40px]"
                        >
                            <div class="min-w-0">
                                <div class="flex items-start justify-between gap-2">
                                    <p class="truncate font-medium">{{ service.name }}</p>
                                    <button
                                        type="button"
                                        class="text-red-600 md:hidden"
                                        @click="removeService(index)"
                                    >
                                        <Trash2 class="h-4 w-4 cursor-pointer" />
                                    </button>
                                </div>
                            </div>
                            <div>
                                <FormInput
                                    :field-name="`service-price-${service.workshop_service_id}-${index}`"
                                    v-model="service.price_rub"
                                    type="number"
                                    placeholder="цена"
                                    class="h-9 !p-2 !text-sm"
                                />
                                <p
                                    v-if="form.errors[`services.${index}.price_rub`]"
                                    class="mt-1 text-xs text-red-600"
                                >
                                    {{ form.errors[`services.${index}.price_rub`] }}
                                </p>
                            </div>
                            <button
                                type="button"
                                class="hidden text-red-600 md:block"
                                @click="removeService(index)"
                            >
                                <Trash2 class="h-4 w-4 cursor-pointer" />
                            </button>
                        </div>
                    </div>

                    <p v-if="form.errors.services" class="mt-1 text-xs text-red-600">
                        {{ form.errors.services }}
                    </p>
                </div>

                <div class="rounded-xl border p-4">
                    <div class="mb-3 grid grid-cols-2 md:grid-cols-3 items-center gap-3">
                        <div class="max-md:hidden"></div>
                        <div class="flex items-center justify-center gap-2">
                            <h2 class="text-center text-lg font-semibold">Запчасти</h2>
                            <p class="text-sm font-semibold text-muted-foreground">
                                {{ formatPrice(sparePartsTotalForClient) }}
                            </p>
                        </div>
                        <div class="flex justify-end">
                            <button
                                type="button"
                                class="inline-flex cursor-pointer items-center gap-1 rounded-md border px-2 py-1 text-sm"
                                @click="addExternalSparePart"
                            >
                                <Plus class="h-4 w-4" />
                                Докупаем
                            </button>
                        </div>
                    </div>

                    <div v-if="!form.spare_parts.length" class="text-sm text-muted-foreground">
                        Пока не добавлено ни одной запчасти
                    </div>

                    <div v-else class="space-y-2">
                        <div
                            v-for="(part, index) in form.spare_parts"
                            :key="`${part.external ? 'external' : part.workshop_spare_part_id}-${index}`"
                            class="rounded-lg border p-3"
                        >
                            <div
                                v-if="!part.external"
                                class="grid items-center gap-2 md:grid-cols-[1fr_120px_140px_40px]"
                            >
                                <div class="min-w-0">
                                    <div class="flex items-start justify-between gap-2">
                                        <p class="truncate font-medium">{{ part.name }}</p>
                                        <button
                                            type="button"
                                            class="text-red-600 md:hidden"
                                            @click="removeSparePart(index)"
                                        >
                                            <Trash2 class="h-4 w-4 cursor-pointer" />
                                        </button>
                                    </div>

                                    <p class="text-xs text-muted-foreground">
                                        Цена продажи: {{ formatPrice(part.sale_price_rub ?? 0) }}
                                    </p>
                                </div>
                                <p class="text-xs text-muted-foreground">
                                    Остаток: {{ part.stock_quantity }}
                                </p>
                                <div>
                                    <FormInput
                                        :field-name="`spare-part-quantity-${part.workshop_spare_part_id}-${index}`"
                                        v-model="part.quantity"
                                        type="number"
                                        min="1"
                                        class="h-9 !p-2 !text-sm"
                                        placeholder="Кол-во"
                                    />
                                    <p
                                        v-if="form.errors[`spare_parts.${index}.quantity`]"
                                        class="mt-1 text-xs text-red-600"
                                    >
                                        {{ form.errors[`spare_parts.${index}.quantity`] }}
                                    </p>
                                </div>
                                <button
                                    type="button"
                                    class="hidden text-red-600 md:block"
                                    @click="removeSparePart(index)"
                                >
                                    <Trash2 class="h-4 w-4 cursor-pointer" />
                                </button>
                            </div>

                            <div
                                v-else
                                class="grid items-center gap-2 md:grid-cols-[1fr_120px_140px_140px_40px]"
                            >
                                <div>
                                    <div
                                        class="mb-5 flex items-center justify-between gap-2 md:mb-0"
                                    >
                                        <span class="text-xs text-muted-foreground md:hidden"
                                            >Внешняя запчасть</span
                                        >
                                        <button
                                            type="button"
                                            class="text-red-600 md:hidden"
                                            @click="removeSparePart(index)"
                                        >
                                            <Trash2 class="h-4 w-4 cursor-pointer" />
                                        </button>
                                    </div>
                                    <FormInput
                                        :field-name="`external-spare-part-name-${index}`"
                                        v-model="part.name"
                                        type="text"
                                        class="h-9 !p-2 !text-sm"
                                        placeholder="Название внешней запчасти"
                                    />
                                    <p
                                        v-if="form.errors[`spare_parts.${index}.name`]"
                                        class="mt-1 text-xs text-red-600"
                                    >
                                        {{ form.errors[`spare_parts.${index}.name`] }}
                                    </p>
                                </div>
                                <div>
                                    <FormInput
                                        :field-name="`external-spare-part-quantity-${index}`"
                                        v-model="part.quantity"
                                        type="number"
                                        min="1"
                                        class="h-9 !p-2 !text-sm"
                                        placeholder="Кол-во"
                                    />
                                    <p
                                        v-if="form.errors[`spare_parts.${index}.quantity`]"
                                        class="mt-1 text-xs text-red-600"
                                    >
                                        {{ form.errors[`spare_parts.${index}.quantity`] }}
                                    </p>
                                </div>
                                <div>
                                    <FormInput
                                        :field-name="`external-spare-part-purchase-price-${index}`"
                                        v-model="part.purchase_price_rub"
                                        type="number"
                                        min="0"
                                        class="h-9 !p-2 !text-sm"
                                        placeholder="Цена покупки"
                                    />
                                    <p
                                        v-if="
                                            form.errors[`spare_parts.${index}.purchase_price_rub`]
                                        "
                                        class="mt-1 text-xs text-red-600"
                                    >
                                        {{ form.errors[`spare_parts.${index}.purchase_price_rub`] }}
                                    </p>
                                </div>
                                <div>
                                    <FormInput
                                        :field-name="`external-spare-part-sale-price-${index}`"
                                        v-model="part.sale_price_rub"
                                        type="number"
                                        min="0"
                                        class="h-9 !p-2 !text-sm"
                                        placeholder="Цена продажи"
                                    />
                                    <p
                                        v-if="form.errors[`spare_parts.${index}.sale_price_rub`]"
                                        class="mt-1 text-xs text-red-600"
                                    >
                                        {{ form.errors[`spare_parts.${index}.sale_price_rub`] }}
                                    </p>
                                </div>
                                <button
                                    type="button"
                                    class="hidden text-red-600 md:block"
                                    @click="removeSparePart(index)"
                                >
                                    <Trash2 class="h-4 w-4 cursor-pointer" />
                                </button>
                            </div>
                        </div>
                    </div>

                    <p v-if="form.errors.spare_parts" class="mt-2 text-xs text-red-600">
                        {{ form.errors.spare_parts }}
                    </p>
                </div>

                <div class="rounded-xl border p-4">
                    <FormInput
                        field-name="photos"
                        type="file"
                        multiple
                        accept="image/*"
                        button-text="Добавить фотографии"
                        @change="onPhotosSelected"
                    />
                    <p v-if="form.errors.photos" class="mt-1 text-xs text-red-600">
                        {{ form.errors.photos }}
                    </p>
                    <p v-if="form.errors['photos.0']" class="mt-1 text-xs text-red-600">
                        {{ form.errors['photos.0'] }}
                    </p>

                    <div v-if="newPhotoPreviews.length" class="mt-3 flex flex-wrap gap-2">
                        <div
                            v-for="(preview, index) in newPhotoPreviews"
                            :key="preview.url"
                            class="space-y-1"
                        >
                            <img
                                :src="preview.url"
                                alt="Новое фото велосипеда"
                                class="h-20 w-20 rounded object-cover"
                            />
                            <button
                                type="button"
                                class="inline-flex items-center gap-1 text-xs text-red-600"
                                @click="removeNewPhoto(index)"
                            >
                                <X class="h-3 w-3" />
                                Убрать фото
                            </button>
                        </div>
                    </div>

                    <div
                        v-if="isEdit && props.item?.photos.length"
                        class="mt-3 flex flex-wrap gap-2"
                    >
                        <div v-for="photo in props.item.photos" :key="photo.id" class="space-y-1">
                            <img
                                :src="photo.photo_url"
                                alt="Фото велосипеда"
                                class="h-20 w-20 rounded object-cover"
                            />
                            <button
                                v-if="!isPhotoMarkedForRemove(photo.id)"
                                type="button"
                                class="text-xs text-red-600"
                                @click="markPhotoForRemove(photo.id)"
                            >
                                Удалить фото
                            </button>
                            <button
                                v-else
                                type="button"
                                class="text-xs text-blue-600"
                                @click="unmarkPhotoForRemove(photo.id)"
                            >
                                Отменить удаление
                            </button>
                        </div>
                    </div>
                </div>

                <div class="rounded-xl border p-4">
                    <h2 class="mb-3 text-lg font-semibold">Стоимость</h2>
                    <div class="space-y-1 text-sm">
                        <p class="flex items-center justify-between">
                            <span class="text-muted-foreground">Работы</span>
                            <span class="font-medium">{{ formatPrice(worksTotalForClient) }}</span>
                        </p>
                        <p class="flex items-center justify-between">
                            <span class="text-muted-foreground">Запчасти</span>
                            <span class="font-medium">{{
                                formatPrice(sparePartsTotalForClient)
                            }}</span>
                        </p>
                        <p
                            class="mt-2 flex items-center justify-between border-t pt-2 text-base font-semibold"
                        >
                            <span>Итого</span>
                            <span>{{ formatPrice(totalForClient) }}</span>
                        </p>
                    </div>
                </div>

                <div class="flex gap-3">
                    <Button type="submit" :disabled="form.processing">
                        {{ isEdit ? 'Сохранить' : 'Создать заказ' }}
                    </Button>
                    <Button as-child variant="outline">
                        <Link :href="route('adminka.workshop.repair-orders.index')">Отмена</Link>
                    </Button>
                </div>
            </section>

            <section class="rounded-xl border p-4">
                <h2 class="mb-3 text-lg font-semibold">Каталог работ</h2>
                <Accordion type="multiple" class="space-y-2">
                    <AccordionItem
                        v-for="(group, index) in groupedCatalog"
                        :key="group.category"
                        :value="`service-category-${index}`"
                    >
                        <AccordionTrigger class="cursor-pointer text-sm font-semibold">
                            {{ group.category }}
                        </AccordionTrigger>
                        <AccordionContent>
                            <div class="space-y-1 pb-2">
                                <button
                                    v-for="service in group.services"
                                    :key="service.id"
                                    type="button"
                                    class="flex w-full items-center justify-between rounded-md border px-3 py-2 text-left hover:bg-muted"
                                    @click="addService(service)"
                                >
                                    <span class="min-w-0 truncate pr-2 text-sm">{{
                                        service.name
                                    }}</span>
                                    <span
                                        class="flex items-center gap-2 text-xs text-muted-foreground"
                                    >
                                        {{ service.price_rub }} ₽
                                        <Plus class="h-3.5 w-3.5" />
                                    </span>
                                </button>
                            </div>
                        </AccordionContent>
                    </AccordionItem>
                </Accordion>

                <h2 class="mt-6 mb-3 text-lg font-semibold">Каталог запчастей</h2>
                <Accordion type="multiple" class="space-y-2">
                    <AccordionItem
                        v-for="(group, index) in groupedSparePartCatalog"
                        :key="group.category"
                        :value="`spare-part-category-${index}`"
                    >
                        <AccordionTrigger class="cursor-pointer text-sm font-semibold">
                            {{ group.category }}
                        </AccordionTrigger>
                        <AccordionContent>
                            <div class="space-y-1 pb-2">
                                <button
                                    v-for="part in group.parts"
                                    :key="part.id"
                                    type="button"
                                    class="flex w-full items-center justify-between rounded-md border px-3 py-2 text-left hover:bg-muted"
                                    @click="addSparePart(part)"
                                >
                                    <span class="min-w-0 truncate pr-2 text-sm">{{
                                        part.name
                                    }}</span>
                                    <span
                                        class="flex items-center gap-2 text-xs text-muted-foreground"
                                    >
                                        {{ formatPrice(part.sale_price_rub) }} • Остаток:
                                        {{ part.quantity }}
                                        <Plus class="h-3.5 w-3.5" />
                                    </span>
                                </button>
                            </div>
                        </AccordionContent>
                    </AccordionItem>
                </Accordion>
            </section>
        </form>
    </div>
</template>
