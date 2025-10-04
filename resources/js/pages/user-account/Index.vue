<script setup lang="ts">
import { Card } from '@/components/shadecn/card';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/shadecn/tabs';
import NewPasswordForm from '@/pages/user-account/components/NewPasswordForm.vue';
import ProfileForm from '@/pages/user-account/components/ProfileForm.vue';
import StudioActivities from '@/pages/user-account/components/StudioActivities.vue';
import { CyclingActivity } from '@/types';
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

type Props = {
    activities: CyclingActivity[];
};
const props = defineProps<Props>();

const page = usePage();
const cyclingCount =  computed(() => page.props.auth.user.paid_cycling_count);
</script>



<template>
    <h1 class="mb-10">Личный кабинет</h1>

    <Tabs class="mx-auto md:max-w-2xl" default-value="profile">
        <TabsList class="mx-auto mb-5">
            <TabsTrigger value="profile"> Профиль</TabsTrigger>
            <TabsTrigger value="cycling-activities"> Студия</TabsTrigger>
        </TabsList>
        <TabsContent value="profile" class="mx-auto flex gap-5 max-md:flex-col">
            <Card class="p-5">
                <NewPasswordForm />
            </Card>

            <Card class="p-5">
                <ProfileForm />
            </Card>
        </TabsContent>
        <TabsContent value="cycling-activities">
            <h2 class="mb-5">У вас осталось оплаченных тренировок: {{ cyclingCount }} шт.</h2>
            <StudioActivities :activities="props.activities" />
        </TabsContent>
    </Tabs>
</template>
