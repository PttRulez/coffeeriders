<script setup lang="ts"></script>

<template>
    <h1>Жоская админка</h1>

    <ul class="space-y-5">
        <li>
            Прокат
            <ul class="pl-5">
                <li class="text-blue-400">
                    <Link :href="route('adminka.rent-bikes.index')">Велики</Link>
                </li>
                <li class="text-blue-400">
                    <Link :href="route('adminka.rent-bikes.bookings')">Бронь</Link>
                </li>
            </ul>
        </li>
        <li>
            Студия
            <ul class="pl-5">
                <li class="text-blue-400">
                    <Link :href="route('adminka.cycling-studio.index')">Бронь</Link>
                </li>
            </ul>
        </li>
    </ul>
</template>
