import { computed, ref } from 'vue'
import {
  apiFetch,
  clearToken,
  decodeJwtPayload,
  getToken,
  setToken,
} from '../api/client'

const token = ref<string | null>(getToken())

export function useAuth() {
  const isAuthenticated = computed(() => token.value !== null)

  const userEmail = computed(() => {
    if (!token.value) return null
    const payload = decodeJwtPayload(token.value)
    if (!payload) return null
    return (payload.username as string) ?? (payload.email as string) ?? null
  })

  async function login(email: string, password: string): Promise<void> {
    const data = await apiFetch<{ token: string }>('/api/login', {
      method: 'POST',
      body: JSON.stringify({ email, password }),
    })
    token.value = data.token
    setToken(data.token)
  }

  function logout(): void {
    token.value = null
    clearToken()
  }

  return {
    token,
    isAuthenticated,
    userEmail,
    login,
    logout,
  }
}
