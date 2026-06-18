<script setup lang="ts">
import { ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import AuthLayout from '../layouts/AuthLayout.vue'
import { useAuth } from '../composables/useAuth'

const router = useRouter()
const route = useRoute()
const { login } = useAuth()

const email = ref('')
const password = ref('')
const error = ref('')
const loading = ref(false)

async function handleSubmit() {
  error.value = ''
  loading.value = true

  try {
    await login(email.value.trim(), password.value)
    const redirect = (route.query.redirect as string) || '/dashboard'
    router.push(redirect)
  } catch (e) {
    error.value = e instanceof Error ? e.message : 'Logowanie nie powiodło się.'
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <AuthLayout title="Logowanie" subtitle="Zaloguj się do panelu TaxInvest">
    <form class="form" @submit.prevent="handleSubmit">
      <div v-if="error" class="alert alert-error">{{ error }}</div>

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

      <label class="field">
        <span>Hasło</span>
        <input
          v-model="password"
          type="password"
          name="password"
          autocomplete="current-password"
          required
          placeholder="••••••••"
        />
      </label>

      <button type="submit" class="btn-primary" :disabled="loading">
        {{ loading ? 'Logowanie…' : 'Zaloguj się' }}
      </button>

      <p class="form-footer">
        <RouterLink to="/forgot-password">Przypomnij hasło</RouterLink>
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

.form-footer {
  margin: 0;
  text-align: center;
  font-size: 0.875rem;
}
</style>
