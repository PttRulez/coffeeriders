<script setup lang="ts">
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'

const amount = ref(1500)

const pay = () => {
  router.post(route('tinkoff.init-payment'), {
    amount: amount.value,
    order_id: 'bike-123', // например, id бронирования велосипеда
  })
}
</script>

<template>
  <div>
    <input v-model="amount" type="number" placeholder="Сумма" class="border p-2" />
    <button @click="pay" class="bg-blue-500 text-white p-2 rounded">Оплатить</button>
  </div>
</template>