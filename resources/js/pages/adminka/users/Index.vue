<script lang="ts" setup="">
import {
    Dialog,
    DialogContent,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/shadecn/dialog';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/shadecn/table';
import { User } from '@/types';
import { ColumnDef, FlexRender, getCoreRowModel, useVueTable } from '@tanstack/vue-table';
import { h, ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { Input } from '@/components/shadecn/input'
import { Button } from '@/components/shadecn/button'

type Props = {
    users: User[];
};
const props = defineProps<Props>();

// состояние модалки с кол-вом тренировок
const isOpen = ref(false)
const selectedUser = ref<User | null>(null)
const formValue = ref<number>(0)


const openModal = (user: User) => {
  selectedUser.value = user
  formValue.value = user.paid_cycling_count
  isOpen.value = true
}

const form = useForm({ paid_cycling_count: 0 })

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
    },
];

const save = () => {
  if (!selectedUser.value) return
  form.paid_cycling_count = formValue.value
  form.put(route('adminka.adminka.users.update-cycling-activities-count', selectedUser.value.id), {
    onSuccess: () => {
      isOpen.value = false
    },
  })
}

const table = useVueTable({
    get data() {
        return props.users;
    },
    get columns() {
        return columns;
    },
    getCoreRowModel: getCoreRowModel(),
});
</script>

<template>
    <h1>Все Юзеры ёпта</h1>
    <Table class="table-auto">
        <TableHeader>
            <TableRow v-for="headerGroup in table.getHeaderGroups()" :key="headerGroup.id">
                <TableHead v-for="header in headerGroup.headers" :key="header.id">
                    <FlexRender
                        v-if="!header.isPlaceholder"
                        :render="header.column.columnDef.header"
                        :props="header.getContext()"
                    />
                </TableHead>
            </TableRow>
        </TableHeader>
        <TableBody>
            <template v-if="table.getRowModel().rows?.length">
                <TableRow
                    v-for="row in table.getRowModel().rows"
                    :key="row.id"
                    :data-state="row.getIsSelected() ? 'selected' : undefined"
                >
                    <TableCell v-for="cell in row.getVisibleCells()" :key="cell.id">
                        <FlexRender
                            :render="cell.column.columnDef.cell"
                            :props="cell.getContext()"
                        />
                    </TableCell>
                </TableRow>
            </template>
            <template v-else>
                <TableRow>
                    <TableCell :colspan="columns.length" class="h-24 text-center">
                        No results.
                    </TableCell>
                </TableRow>
            </template>
        </TableBody>
    </Table>

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
