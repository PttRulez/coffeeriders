<script  lang="ts" setup="">
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow
} from '@/components/ui/table';
import { dateTimePrettify } from '../../../helpers';
import { CyclingActivity } from '@/types';

type Props = {
    activities: CyclingActivity[];
};
const props = defineProps<Props>();

</script>

<template>
    <Table>
        <TableHeader>
            <TableRow>
                <TableHead>время</TableHead>
                <TableHead>дистанция</TableHead>
            </TableRow>
        </TableHeader>
        <TableBody>
            <TableRow v-for="activity in props.activities" :key="activity.id">
                <TableCell>
                    {{ dateTimePrettify(activity.starts_at) }}
                </TableCell>

                <TableCell >
                    {{ activity.distance }}
                </TableCell>
            </TableRow>
        </TableBody>
    </Table>
</template>