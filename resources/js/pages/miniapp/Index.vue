<script setup>
import { onMounted } from 'vue'

onMounted(() => {
  const tg = window.Telegram.WebApp
  tg.ready()
  tg.expand()

  fetch('/api/telegram-auth', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ initData: tg.initData })
  })
    .then(res => res.json())
    .then(data => console.log('Auth result:', data))
    .catch(err => console.error('Telegram auth failed:', err))
})
</script>

<template>
  <div class="p-4 text-center">
    <h1 class="text-2xl font-bold">Привет из Telegram Mini App</h1>
    <p class="text-gray-600">Это SPA через Laravel + Inertia + Vue</p>
  </div>
</template>
