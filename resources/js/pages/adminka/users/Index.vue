<script lang="ts" setup="">
import DataTable from '@/components/shared/DataTable.vue';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { User } from '@/types';
import { Link, router, useForm } from '@inertiajs/vue3';
import { ColumnDef } from '@tanstack/vue-table';
import { Pencil, XCircle } from 'lucide-vue-next';
import { h, ref } from 'vue';

type Props = {
    users: User[];
};
const props = defineProps<Props>();

// состояние модалки с кол-вом тренировок
const isOpen = ref(false);
const selectedUser = ref<User | null>(null);
const formValue = ref<number>(0);

const openModal = (user: User) => {
    selectedUser.value = user;
    formValue.value = user.paid_cycling_count;
    isOpen.value = true;
};

const form = useForm({ paid_cycling_count: 0 });

const columns: ColumnDef<User>[] = [
    {
        accessorKey: 'name',
        header: 'Имя',
    },
    {
        accessorKey: 'paid_cycling_count',
        header: 'Куплено тренировок',
        cell: ({ row }) =>
            h(
                'div',
                {
                    class: 'cursor-pointer',
                    onClick: () => openModal(row.original),
                },
                row.getValue('paid_cycling_count'),
            ),
    },
    {
        accessorKey: 'email',
        header: 'Email',
    },
    {
        accessorKey: 'phone',
        header: 'Телефон',
    },
    {
        accessorKey: 'telegram_username',
        header: 'телеграм',
        cell: ({ row }) =>
            h(
                'a',
                {
                    class: 'text-blue-400 hover:underline',
                    href: `https://t.me/${row.getValue('telegram_username')}`,
                    target: '_blank',
                },
                row.getValue('telegram_username'),
            ),
    },
    {
        accessorKey: 'is_coffeerider',
        header: () => h('div', { class: 'text-center w-full' }, 'Кофейник'),
        cell: ({ row }) => {
            const id = row.original.id;
            console.log(row.original.id);
            const isCoffeerider = row.getValue('is_coffeerider');

            const toggle = () => {
                router.put(
                    route('adminka.users.update-is-coffeerider', { user: id }),
                    { is_coffeerider: !isCoffeerider },
                    {
                        preserveScroll: true,
                        preserveState: true,
                        onSuccess: () => console.log('Updated successfully!'),
                        onError: (e) => console.error('Error updating:', e),
                    },
                );
            };

            return h(
                'div',
                {
                    class: 'flex items-center justify-center h-full cursor-pointer',
                    onClick: toggle,
                },
                [
                    isCoffeerider
                        ? h('img', {
                              src: '/img/coffee-riders-logo.jpg',
                              class: 'h-8 w-auto object-contain',
                          })
                        : h(XCircle, {
                              class: 'text-red-500 w-5 h-5',
                          }),
                ],
            );
        },
    },
    {
        header: '',
        id: 'actions',
        cell: ({ row }) =>
            h(
                Link,
                { href: route('adminka.users.edit', { user: row.original.id}) },
                h(
                    Pencil,
                    {
                        class: 'cursor-pointer',
                        href: `https://t.me/${row.getValue('telegram_username')}`,
                        target: '_blank',
                    },
                    row.getValue('telegram_username'),
                ),
            ),
    },
];

const save = () => {
    if (!selectedUser.value) return;
    form.paid_cycling_count = formValue.value;
    form.put(route('adminka.users.update-cycling-activities-count', selectedUser.value.id), {
        onSuccess: () => {
            isOpen.value = false;
        },
    });
};
</script>

<template>
    <h1 class="mb-10">Все Юзеры ёпта</h1>

    <DataTable :columns="columns" :data="props.users" />

    <!-- Модалка с кол-вом тренировок -->
    <Dialog v-model:open="isOpen">
        <DialogContent class="max-w-[320px]!">
            <DialogHeader>
                <DialogTitle>Изменить количество тренировок</DialogTitle>
            </DialogHeader>
            <div class="py-4">
                <Input type="number" v-model="formValue" />
            </div>
            <DialogFooter>
                <Button @click="save" :disabled="form.processing">Сохранить</Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
