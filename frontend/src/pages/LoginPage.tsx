import { useState } from 'react'
import type { FormEvent } from 'react'
import { useNavigate } from 'react-router-dom'
import { useAuth } from '../context/AuthContext'

function LoginPage() {
  const [email, setEmail] = useState('')
  const [senha, setSenha] = useState('')
  const [error, setError] = useState('')
  const { login } = useAuth()
  const navigate = useNavigate()

  async function handleSubmit(e: FormEvent) {
    e.preventDefault()
    setError('')

    if (!email || !senha) {
      setError('Informe email e senha')
      return
    }

    try {
      await login(email, senha)
      navigate('/clientes')
    } catch (err: any) {
      setError(err?.response?.data?.message || 'Falha no login')
    }
  }

  return (
    <div className="page-container">
      <h1>Login</h1>
      {error && <p className="error">{error}</p>}
      <form onSubmit={handleSubmit} className="clientes-form">
        <input type="email" value={email} onChange={(e) => setEmail(e.target.value)} placeholder="Email" />
        <input type="password" value={senha} onChange={(e) => setSenha(e.target.value)} placeholder="Senha" />
        <button type="submit">Entrar</button>
      </form>
    </div>
  )
}

export default LoginPage
