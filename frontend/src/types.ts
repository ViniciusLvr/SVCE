export interface Cliente {
  id: number;
  nome: string;
  telefone: string;
  cpf?: string | null;
  cnpj?: string | null;
  created_at: string;
}
