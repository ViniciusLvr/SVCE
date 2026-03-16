import { createContext, useContext, useEffect, useState } from 'react'
import type { ReactNode } from 'react'
import api from '../services/api'

type User = {
  id: number
  nome: string
  email: string
  cargo: string
}

type AuthContextType = {
  user: User | null
  token: string | null
  login: (email: string, senha: string) => Promise<void>
  logout: () => void
  loadUser: () => Promise<void>
}

const AuthContext = createContext<AuthContextType | undefined>(undefined)

export const AuthProvider = ({ children }: { children: ReactNode }) => {
  const [token, setToken] = useState<string | null>(localStorage.getItem('svce-token'))
  const [user, setUser] = useState<User | null>(null)

  useEffect(() => {
    if (token) {
      api.defaults.headers.common.Authorization = `Bearer ${token}`
      loadUser().catch(() => logout())
    }
  }, [token])

  async function loadUser() {
    if (!token) return
    try {
      const response = await api.get<User>('/auth/me')
      setUser(response.data)
    } catch (error) {
      console.error('Erro carregando user', error)
      logout()
    }
  }

  async function login(email: string, senha: string) {
    const response = await api.post('/auth/login', { email, senha })
    const { token: receivedToken, user: loggedUser } = response.data
    setToken(receivedToken)
    localStorage.setItem('svce-token', receivedToken)
    api.defaults.headers.common.Authorization = `Bearer ${receivedToken}`
    setUser(loggedUser)
  }

  function logout() {
    setToken(null)
    setUser(null)
    localStorage.removeItem('svce-token')
    delete api.defaults.headers.common.Authorization
  }

  return <AuthContext.Provider value={{ user, token, login, logout, loadUser }}>{children}</AuthContext.Provider>
}

export function useAuth() {
  const context = useContext(AuthContext)
  if (!context) throw new Error('useAuth must be used within AuthProvider')
  return context
}
