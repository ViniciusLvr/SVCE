import { useEffect, useState } from 'react'
import api from '../services/api'
import type { Cliente } from '../types'
import type { FormEvent } from 'react'

function ClientesPage() {
  const [clientes, setClientes] = useState<Cliente[]>([])
  const [nome, setNome] = useState('')
  const [telefone, setTelefone] = useState('')
  const [cpf, setCpf] = useState('')
  const [cnpj, setCnpj] = useState('')
  const [error, setError] = useState('')

  async function loadClientes() {
    try {
      const response = await api.get<Cliente[]>('/clientes')
      setClientes(response.data)
      setError('')
    } catch (err) {
      console.error(err)
      setError('Erro ao carregar clientes')
    }
  }

  useEffect(() => {
    async function init() {
      await loadClientes()
    }
    init()
  }, [])

  async function handleCreate(e: FormEvent) {
    e.preventDefault()

    if (!nome || !telefone) {
      setError('Nome e telefone são obrigatórios')
      return
    }

    try {
      await api.post('/clientes', { nome, telefone, cpf: cpf || null, cnpj: cnpj || null })
      setNome('')
      setTelefone('')
      setCpf('')
      setCnpj('')
      await loadClientes()
      setError('')
    } catch (err) {
      type ApiError = { response?: { data?: { message?: string } } }
      const errorMessage =
        typeof err === 'object' && err !== null
          ? (err as ApiError).response?.data?.message
          : undefined

      console.error(err)
      setError(errorMessage || 'Erro ao criar cliente')
    }
  }

  return (
    <div className="page-container">
      <h1>Clientes</h1>
      {error && <p className="error">{error}</p>}
      <form onSubmit={handleCreate} className="clientes-form">
        <input value={nome} onChange={(e) => setNome(e.target.value)} placeholder="Nome" />
        <input value={telefone} onChange={(e) => setTelefone(e.target.value)} placeholder="Telefone" />
        <input value={cpf} onChange={(e) => setCpf(e.target.value)} placeholder="CPF (11 dígitos)" />
        <input value={cnpj} onChange={(e) => setCnpj(e.target.value)} placeholder="CNPJ (14 dígitos)" />
        <button type="submit">Adicionar cliente</button>
      </form>

      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Nome</th>
            <th>Telefone</th>
            <th>CPF</th>
            <th>CNPJ</th>
            <th>Criado</th>
          </tr>
        </thead>
        <tbody>
          {clientes.map((cliente) => (
            <tr key={cliente.id}>
              <td>{cliente.id}</td>
              <td>{cliente.nome}</td>
              <td>{cliente.telefone}</td>
              <td>{cliente.cpf ?? '-'}</td>
              <td>{cliente.cnpj ?? '-'}</td>
              <td>{new Date(cliente.created_at).toLocaleString()}</td>
            </tr>
          ))}
        </tbody>
      </table>
    </div>
  )
}

export default ClientesPage
