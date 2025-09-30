<script setup lang="ts">
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/shadecn/tabs';
import NewPasswordForm from '@/pages/user-account/components/NewPasswordForm.vue';
</script>

<template>
    <h1 class="mb-10">Личный кабинет</h1>

    <Tabs class="md:max-w-2xl mx-auto" default-value="profile">
        <TabsList class="mx-auto mb-5">
            <TabsTrigger value="profile"> Профиль </TabsTrigger>
            <TabsTrigger value="cycling-activities"> Студия </TabsTrigger>
        </TabsList>
        <TabsContent value="profile" class="mx-auto">
            <NewPasswordForm />
        </TabsContent>
        <TabsContent value="cycling-activities"> Табличка тренировок в студии </TabsContent>
    </Tabs>
</template>
