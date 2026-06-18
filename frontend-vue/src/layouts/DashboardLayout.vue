<script setup lang="ts">
import { RouterLink, useRouter } from 'vue-router'
import { useAuth } from '../composables/useAuth'

const router = useRouter()
const { userEmail, logout } = useAuth()

function handleLogout() {
  logout()
  router.push({ name: 'login' })
}
</script>

<template>
  <div class="dashboard">
    <aside class="sidebar">
      <div class="sidebar-brand">TaxInvest</div>
      <nav class="sidebar-nav">
        <RouterLink to="/dashboard" class="nav-link active">Panel</RouterLink>
      </nav>
      <div class="sidebar-footer">
        <span class="user-email">{{ userEmail }}</span>
        <button type="button" class="btn-logout" @click="handleLogout">Wyloguj</button>
      </div>
    </aside>
    <main class="content">
      <slot />
    </main>
  </div>
</template>

<style scoped>
.dashboard {
  display: flex;
  min-height: 100vh;
}

.sidebar {
  width: 240px;
  background: #0f172a;
  color: #f8fafc;
  display: flex;
  flex-direction: column;
  padding: 1.5rem 1rem;
}

.sidebar-brand {
  font-weight: 700;
  font-size: 1.125rem;
  padding: 0 0.75rem;
  margin-bottom: 2rem;
}

.sidebar-nav {
  flex: 1;
}

.nav-link {
  display: block;
  padding: 0.625rem 0.75rem;
  border-radius: 6px;
  color: #cbd5e1;
  text-decoration: none;
  font-size: 0.9375rem;
}

.nav-link:hover,
.nav-link.active {
  background: rgba(255, 255, 255, 0.1);
  color: #fff;
  text-decoration: none;
}

.sidebar-footer {
  border-top: 1px solid rgba(255, 255, 255, 0.1);
  padding-top: 1rem;
}

.user-email {
  display: block;
  font-size: 0.8125rem;
  color: #94a3b8;
  margin-bottom: 0.75rem;
  padding: 0 0.75rem;
}

.btn-logout {
  width: 100%;
  padding: 0.5rem 0.75rem;
  border: 1px solid rgba(255, 255, 255, 0.2);
  border-radius: 6px;
  background: transparent;
  color: #f8fafc;
  cursor: pointer;
  font-size: 0.875rem;
}

.btn-logout:hover {
  background: rgba(255, 255, 255, 0.08);
}

.content {
  flex: 1;
  padding: 2rem;
  overflow: auto;
}

@media (max-width: 768px) {
  .dashboard {
    flex-direction: column;
  }

  .sidebar {
    width: 100%;
  }
}
</style>
