<script setup lang="ts">
import { Role } from '@/types/enums';
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();
const isAdmin = computed(() => page.props.auth.user?.role === Role.Admin);
</script>

<template>
    <h1 class="mb-5">Жоская админка</h1>

    <ul class="space-y-5">
        <li v-if="isAdmin">
            Администрирование
            <ul class="pl-5">
                <li class="text-blue-400">
                    <Link :href="route('adminka.users.index')">Юзеры</Link>
                </li>
                <li class="text-blue-400">
                    <Link :href="route('adminka.coupons.index')">Купоны</Link>
                </li>
                <li class="text-blue-400">
                    <Link :href="route('adminka.blog.index')">Блог</Link>
                </li>
            </ul>
        </li>
        <li v-if="isAdmin">
            Гонки
            <ul class="pl-5">
                <li class="text-blue-400">
                    <Link :href="route('adminka.races.index')">Гонки</Link>
                </li>
            </ul>
        </li>
        <li>
            Мастерская
            <ul class="pl-5">
                <li class="text-blue-400 mb-5">
                    <Link :href="route('adminka.workshop.repair-orders.index')">Велосипеды в ремонте</Link>
                </li>

                <li v-if="isAdmin" class="text-blue-400">
                    <Link :href="route('adminka.workshop.categories.index')">Категории услуг</Link>
                </li>
                <li v-if="isAdmin" class="text-blue-400  mb-5">
                    <Link :href="route('adminka.workshop.services.index')">Услуги</Link>
                </li>


                <li v-if="isAdmin" class="text-blue-400">
                    <Link :href="route('adminka.workshop.spare-part-categories.index')">
                        Категории запчастей
                    </Link>
                </li>
                <li v-if="isAdmin" class="text-blue-400">
                    <Link :href="route('adminka.workshop.spare-parts.index')">Запчасти</Link>
                </li>
                <li v-if="isAdmin" class="text-blue-400">
                    <Link :href="route('adminka.workshop.spare-part-purchases.index')">
                        Закупки
                    </Link>
                </li>
            </ul>
        </li>
        <li v-if="isAdmin">
            Прокат
            <ul class="pl-5">
                <li class="text-blue-400">
                    <Link :href="route('adminka.rent-bikes.index')">Велики</Link>
                </li>
                <li class="text-blue-400">
                    <Link :href="route('adminka.rent-bikes.bookings.index')">Бронь</Link>
                </li>
            </ul>
        </li>
        <li v-if="isAdmin">
            Студия
            <ul class="pl-5">
                <li class="text-blue-400">
                    <Link :href="route('adminka.cycling-studio.index')">Бронь</Link>
                </li>
            </ul>
        </li>
    </ul>
</template>
