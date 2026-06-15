<script setup lang="ts">
import { onMounted, ref } from 'vue'

const apiUrl = import.meta.env.VITE_API_URL ?? 'http://localhost:8080'
const health = ref<string>('Sprawdzam API...')

onMounted(async () => {
  try {
    const response = await fetch(`${apiUrl}/api/health`)
    const data = await response.json()
    health.value = `Backend: ${data.status} (PHP ${data.php})`
  } catch {
    health.value = 'Backend niedostępny'
  }
})
</script>

<template>
  <main class="page">
    <h1>TaxInvest</h1>
    <p>Frontend Vue 3 + Vite</p>
    <p class="status">{{ health }}</p>
  </main>
</template>

<style scoped>
.page {
  font-family: system-ui, sans-serif;
  max-width: 640px;
  margin: 4rem auto;
  padding: 0 1rem;
}

.status {
  color: #2563eb;
}
</style>
