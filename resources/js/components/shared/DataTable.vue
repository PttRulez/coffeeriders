<script setup lang="ts" generic="TData, TValue">
import { Button } from '@/components/shadecn/button';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/shadecn/table';
import type { ColumnDef } from '@tanstack/vue-table';
import {
    FlexRender,
    getCoreRowModel,
    getPaginationRowModel,
    useVueTable,
} from '@tanstack/vue-table';
import { StepBack, StepForward } from 'lucide-vue-next';

const props = defineProps<{
    columns: ColumnDef<TData, TValue>[];
    data: TData[];
    pageSize?: number;
    pageIndex?: number;
}>();

const next = () => {
    table.nextPage();
    console.log(table.getPageCount());
    console.log(table.getState().pagination);
};

const table = useVueTable({
    get data() {
        return props.data;
    },
    get columns() {
        return props.columns;
    },
    initialState: {
        pagination: {
            pageIndex: props.pageIndex ?? 0,
            pageSize: props.pageSize ?? 10,
        },
    },

    getCoreRowModel: getCoreRowModel(),
    getPaginationRowModel: getPaginationRowModel(),
});
</script>

<template>
    <div>
        <div class="rounded-md border">
            <Table>
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
                                Нет данных.
                            </TableCell>
                        </TableRow>
                    </template>
                </TableBody>
            </Table>
        </div>

        <div class="flex items-center justify-end space-x-2 py-4">
            <Button
                variant="outline"
                size="sm"
                :disabled="!table.getCanPreviousPage()"
                @click="table.previousPage()"
            >
                <StepBack />
            </Button>
            <span
                >{{ table.getState().pagination.pageIndex + 1 }} из {{ table.getPageCount() }}</span
            >
            <Button variant="outline" size="sm" :disabled="!table.getCanNextPage()" @click="next">
                <StepForward />
            </Button>
        </div>
    </div>
</template>
