<script setup lang="ts">
import { Card } from '@/components/ui/card';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
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
const cyclingCount = computed(() => page.props.auth.user.paid_cycling_count);
</script>

<template>
    <h1 class="mb-10">Личный кабинет</h1>

    <Tabs class="mx-auto md:max-w-2xl" default-value="cycling-activities">
        <TabsList class="mx-auto mb-5">
            <TabsTrigger value="cycling-activities"> Студия</TabsTrigger>
            <TabsTrigger value="profile"> Профиль</TabsTrigger>
        </TabsList>

        <TabsContent value="cycling-activities">
            <h2 class="mb-5">Баланс тренировок (абонементы): {{ cyclingCount }} шт.</h2>
            <StudioActivities :activities="props.activities" />
        </TabsContent>
        <TabsContent value="profile" class="mx-auto flex items-start gap-5 max-md:flex-col">
            <Card class="p-5">
                <ProfileForm />
            </Card>

            <Card class="p-5">
                <NewPasswordForm />
            </Card>
        </TabsContent>
    </Tabs>
</template>
