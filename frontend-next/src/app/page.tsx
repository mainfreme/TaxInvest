async function getHealth() {
  const apiUrl =
    process.env.API_URL_INTERNAL ??
    process.env.NEXT_PUBLIC_API_URL ??
    'http://localhost:8080'

  try {
    const response = await fetch(`${apiUrl}/api/health`, { cache: 'no-store' })
    if (!response.ok) {
      return 'Backend niedostępny'
    }

    const data = (await response.json()) as { status: string; php: string }
    return `Backend: ${data.status} (PHP ${data.php})`
  } catch {
    return 'Backend niedostępny'
  }
}

export default async function HomePage() {
  const health = await getHealth()

  return (
    <main style={{ fontFamily: 'system-ui, sans-serif', maxWidth: 640, margin: '4rem auto', padding: '0 1rem' }}>
      <h1>TaxInvest</h1>
      <p>Frontend Next.js (React)</p>
      <p style={{ color: '#2563eb' }}>{health}</p>
    </main>
  )
}
