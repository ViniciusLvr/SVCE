import { useEffect, useState } from 'react'
import { BrowserRouter, Link, Route, Routes } from 'react-router-dom'
import ClientesPage from './pages/ClientesPage'
import LoginPage from './pages/LoginPage'
import PrivateRoute from './components/PrivateRoute'
import { AuthProvider, useAuth } from './context/AuthContext'
import './App.css'

type HealthData = {
  uptime: number
  status: string
}

function Home() {
  const [health, setHealth] = useState<HealthData | null>(null)
  const [error, setError] = useState<string | null>(null)

  useEffect(() => {
    fetch('http://localhost:4000/api/health')
      .then((res) => {
        if (!res.ok) throw new Error('API não respondeu com sucesso')
        return res.json()
      })
      .then((data) => setHealth(data))
      .catch((err) => setError(err.message))
  }, [])

  return (
    <main className="app-container">
      <h1>SVCE (React + TypeScript)</h1>
      {!health && !error && <p>Conectando ao backend...</p>}
      {error && (
        <p style={{ color: 'red' }}>
          Erro ao conectar ao backend: {error}
        </p>
      )}
      {health && (
        <div>
          <p>Status: {health.status}</p>
          <p>Uptime: {health.uptime.toFixed(2)}s</p>
        </div>
      )}
      <section>
        <h2>Funcionalidades</h2>
        <ul>
          <li>CRUD de clientes</li>
          <li>CRUD de produtos</li>
          <li>Registro de vendas</li>
          <li>Autenticação (JWT)</li>
        </ul>
      </section>
      <Link to="/clientes" className="btn">
        Ir para clientes
      </Link>
    </main>
  )
}

function AppRouter() {
  const { user, logout } = useAuth()

  return (
    <BrowserRouter>
      <div className="nav">
        <Link to="/">Home</Link>
        <Link to="/clientes">Clientes</Link>
        {!user ? <Link to="/login">Login</Link> : <button onClick={logout}>Logout</button>}
      </div>
      <Routes>
        <Route path="/" element={<Home />} />
        <Route path="/login" element={<LoginPage />} />
        <Route
          path="/clientes"
          element={
            <PrivateRoute>
              <ClientesPage />
            </PrivateRoute>
          }
        />
      </Routes>
    </BrowserRouter>
  )
}

export default function App() {
  return (
    <AuthProvider>
      <AppRouter />
    </AuthProvider>
  )
}

