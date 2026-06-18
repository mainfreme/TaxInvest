<script setup lang="ts">
import { ref } from 'vue'
import AuthLayout from '../layouts/AuthLayout.vue'
import { apiFetch } from '../api/client'

const email = ref('')
const error = ref('')
const success = ref('')
const loading = ref(false)

async function handleSubmit() {
  error.value = ''
  success.value = ''
  loading.value = true

  try {
    const data = await apiFetch<{ message: string }>('/api/forgot-password', {
      method: 'POST',
      body: JSON.stringify({ email: email.value.trim() }),
    })
    success.value = data.message
    email.value = ''
  } catch (e) {
    error.value = e instanceof Error ? e.message : 'Nie udało się wysłać żądania.'
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <AuthLayout
    title="Przypomnienie hasła"
    subtitle="Podaj e-mail powiązany z kontem. Wyślemy instrukcje resetu hasła."
  >
    <form class="form" @submit.prevent="handleSubmit">
      <div v-if="error" class="alert alert-error">{{ error }}</div>
      <div v-if="success" class="alert alert-success">{{ success }}</div>

      <label class="field">
        <span>E-mail</span>
        <input
          v-model="email"
          type="email"
          name="email"
          autocomplete="email"
          required
          placeholder="admin@taxinvest.local"
        />
      </label>

      <button type="submit" class="btn-primary" :disabled="loading">
        {{ loading ? 'Wysyłanie…' : 'Wyślij link resetu' }}
      </button>

      <p class="form-footer">
        <RouterLink to="/login">Wróć do logowania</RouterLink>
      </p>
    </form>
  </AuthLayout>
</template>

<style scoped>
.form {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.field {
  display: flex;
  flex-direction: column;
  gap: 0.375rem;
}

.field span {
  font-size: 0.875rem;
  font-weight: 500;
}

.field input {
  padding: 0.625rem 0.75rem;
  border: 1px solid var(--color-border);
  border-radius: 6px;
  font-size: 1rem;
}

.field input:focus {
  outline: 2px solid rgba(30, 64, 175, 0.35);
  border-color: var(--color-primary);
}

.btn-primary {
  margin-top: 0.25rem;
  padding: 0.75rem 1rem;
  border: none;
  border-radius: 6px;
  background: var(--color-primary);
  color: #fff;
  font-size: 1rem;
  font-weight: 500;
  cursor: pointer;
}

.btn-primary:hover:not(:disabled) {
  background: var(--color-primary-hover);
}

.btn-primary:disabled {
  opacity: 0.65;
  cursor: not-allowed;
}

.alert {
  padding: 0.75rem;
  border-radius: 6px;
  font-size: 0.875rem;
}

.alert-error {
  background: #fef2f2;
  color: var(--color-error);
  border: 1px solid #fecaca;
}

.alert-success {
  background: #ecfdf5;
  color: var(--color-success);
  border: 1px solid #a7f3d0;
}

.form-footer {
  margin: 0;
  text-align: center;
  font-size: 0.875rem;
}
</style>
