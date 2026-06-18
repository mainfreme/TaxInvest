<script setup lang="ts">
import { onMounted, ref } from 'vue'
import DashboardLayout from '../layouts/DashboardLayout.vue'
import { apiFetch, getApiUrl } from '../api/client'
import { useAuth } from '../composables/useAuth'

const { userEmail } = useAuth()

const health = ref('Sprawdzam API…')
const dbStatus = ref<string | null>(null)

onMounted(async () => {
  try {
    const data = await apiFetch<{ status: string; php: string }>('/api/health')
    health.value = `Backend: ${data.status} (PHP ${data.php})`
  } catch {
    health.value = 'Backend niedostępny'
  }

  try {
    const data = await apiFetch<{ connected: boolean; version?: string }>('/api/db-check')
    dbStatus.value = data.connected
      ? `Połączono — ${data.version ?? 'PostgreSQL'}`
      : 'Brak połączenia'
  } catch {
    dbStatus.value = 'Błąd połączenia z bazą'
  }
})
</script>

<template>
  <DashboardLayout>
    <header class="page-header">
      <h1>Panel główny</h1>
      <p>Witaj, {{ userEmail ?? 'użytkowniku' }}</p>
    </header>

    <section class="cards">
      <article class="card">
        <h2>Status API</h2>
        <p class="card-value">{{ health }}</p>
        <p class="card-meta">Endpoint: {{ getApiUrl() }}/api/health</p>
      </article>

      <article class="card">
        <h2>Baza danych</h2>
        <p class="card-value">{{ dbStatus ?? 'Sprawdzam…' }}</p>
        <p class="card-meta">Endpoint: {{ getApiUrl() }}/api/db-check</p>
      </article>

      <article class="card">
        <h2>Konto</h2>
        <p class="card-value">{{ userEmail }}</p>
        <p class="card-meta">Zalogowany użytkownik</p>
      </article>
    </section>
  </DashboardLayout>
</template>

<style scoped>
.page-header {
  margin-bottom: 2rem;
}

.page-header h1 {
  margin: 0;
  font-size: 1.75rem;
  font-weight: 600;
}

.page-header p {
  margin: 0.5rem 0 0;
  color: var(--color-text-muted);
}

.cards {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
  gap: 1.25rem;
}

.card {
  background: var(--color-surface);
  border: 1px solid var(--color-border);
  border-radius: var(--radius);
  padding: 1.25rem 1.5rem;
  box-shadow: var(--shadow);
}

.card h2 {
  margin: 0 0 0.75rem;
  font-size: 0.875rem;
  font-weight: 600;
  color: var(--color-text-muted);
  text-transform: uppercase;
  letter-spacing: 0.04em;
}

.card-value {
  margin: 0;
  font-size: 1.0625rem;
  font-weight: 500;
}

.card-meta {
  margin: 0.5rem 0 0;
  font-size: 0.8125rem;
  color: var(--color-text-muted);
}
</style>
