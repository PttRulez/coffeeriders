<script lang="ts" setup="">
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import { CyclingActivity } from '@/types';
import { Pencil, Trash } from 'lucide-vue-next';
import { dateTimePrettify } from '../../../helpers';
import { router } from '@inertiajs/vue3';

type Props = {
    activities: CyclingActivity[];
};

const props = defineProps<Props>();

const deleteActivity = (id: number) => {
    router.delete(route('cycling-studio.destroy', id), {
        onSuccess: () => {
            router.reload({ only: ['activities'] });
        },
    });
};
</script>

<template>
    <Table>
        <TableHeader>
            <TableRow>
                <TableHead>время</TableHead>
                <TableHead>дистанция</TableHead>
                <TableHead></TableHead>
            </TableRow>
        </TableHeader>
        <TableBody>
            <TableRow v-for="activity in props.activities" :key="activity.id">
                <TableCell>
                    {{ dateTimePrettify(activity.starts_at) }}
                </TableCell>

                <TableCell>
                    {{ activity.distance }}
                </TableCell>

                <TableCell class="flex gap-5">
                    <Link
                        v-if="activity.can.update"
                        :href="route('cycling-studio.edit', activity.id)"
                    >
                        <Pencil />
                    </Link>
                    <Trash
                        v-if="activity.can.update"
                        class="cursor-pointer text-red-400"
                        @click="deleteActivity(activity.id)"
                    />
                </TableCell>
            </TableRow>
        </TableBody>
    </Table>
</template>
