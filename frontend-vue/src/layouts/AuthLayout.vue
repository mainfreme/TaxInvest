<script setup lang="ts">
defineProps<{
  title: string
  subtitle?: string
}>()
</script>

<template>
  <div class="auth-page">
    <div class="auth-card">
      <header class="auth-header">
        <span class="logo">TaxInvest</span>
        <h1>{{ title }}</h1>
        <p v-if="subtitle" class="subtitle">{{ subtitle }}</p>
      </header>
      <slot />
    </div>
  </div>
</template>

<style scoped>
.auth-page {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 1.5rem;
  background:
    radial-gradient(circle at top right, rgba(30, 64, 175, 0.12), transparent 45%),
    var(--color-bg);
}

.auth-card {
  width: 100%;
  max-width: 420px;
  background: var(--color-surface);
  border-radius: var(--radius);
  box-shadow: var(--shadow);
  padding: 2rem;
}

.auth-header {
  margin-bottom: 1.75rem;
}

.logo {
  display: block;
  font-size: 0.875rem;
  font-weight: 700;
  letter-spacing: 0.04em;
  color: var(--color-primary);
  margin-bottom: 0.75rem;
}

.auth-header h1 {
  margin: 0;
  font-size: 1.5rem;
  font-weight: 600;
}

.subtitle {
  margin: 0.5rem 0 0;
  color: var(--color-text-muted);
  font-size: 0.9375rem;
}
</style>
