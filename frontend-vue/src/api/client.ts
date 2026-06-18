const API_URL = import.meta.env.VITE_API_URL ?? 'http://localhost:8080'

export function getApiUrl(): string {
  return API_URL
}

export function getToken(): string | null {
  return localStorage.getItem('taxinvest_token')
}

export function setToken(token: string): void {
  localStorage.setItem('taxinvest_token', token)
}

export function clearToken(): void {
  localStorage.removeItem('taxinvest_token')
}

export function decodeJwtPayload(token: string): Record<string, unknown> | null {
  try {
    const parts = token.split('.')
    if (parts.length !== 3) return null
    const payload = parts[1].replace(/-/g, '+').replace(/_/g, '/')
    const decoded = decodeURIComponent(
      atob(payload)
        .split('')
        .map((c) => '%' + ('00' + c.charCodeAt(0).toString(16)).slice(-2))
        .join(''),
    )
    return JSON.parse(decoded) as Record<string, unknown>
  } catch {
    return null
  }
}

export async function apiFetch<T>(
  path: string,
  options: RequestInit = {},
  auth = false,
): Promise<T> {
  const headers = new Headers(options.headers)

  if (!headers.has('Content-Type') && options.body) {
    headers.set('Content-Type', 'application/json')
  }

  if (auth) {
    const token = getToken()
    if (token) {
      headers.set('Authorization', `Bearer ${token}`)
    }
  }

  const response = await fetch(`${API_URL}${path}`, {
    ...options,
    headers,
  })

  const data = await response.json().catch(() => null)

  if (!response.ok) {
    const message =
      (data && typeof data === 'object' && 'message' in data && String(data.message)) ||
      (data && typeof data === 'object' && 'detail' in data && String(data.detail)) ||
      'Wystąpił błąd. Spróbuj ponownie.'
    throw new Error(message)
  }

  return data as T
}
