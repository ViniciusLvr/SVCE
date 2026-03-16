# SVCE (React + TypeScript + Node + Docker)

Projeto migrado do sistema original PHP/MYSQL para arquitetura moderna:
- **Frontend**: React + TypeScript + Vite
- **Backend**: Node + Express + Sequelize + MySQL
- **Infra**: Docker Compose (MySQL, backend, frontend)

## Estrutura do repo
- `frontend/` - app React+TS
- `backend/` - API REST em Express
- `db/CF atualizado.sql` - script SQL com schema + dados originais
- `docker-compose.yml` - orquestração Docker
- `legacy-php/` - código PHP arquivado (não mais necessário)

---
## Pré-requisitos
- Docker Desktop instalado (Windows) ou Docker Engine (Linux/macOS)
- Opcional: Node.js se rodar local sem Docker

---
## ?? Rodar tudo com Docker (recomendado)
1. No diretório raiz do projeto:
   ```powershell
   docker compose up -d --build
   ```
2. Verificar containers:
   ```powershell
   docker compose ps
   ```
3. Importar o schema inicial (apenas na primeira vez):
   ```powershell
   type .\db\CF atualizado.sql | docker exec -i svce-db-1 sh -c "mysql -uroot -proot123 svce"
   ```
4. Acessar:
   - Frontend: `http://localhost:3000`
   - Backend: `http://localhost:4000/api/health`
   - Clientes API: `http://localhost:4000/api/clientes`

---
## Comandos úteis
- Subir dev: `docker compose up -d --build`
- Parar: `docker compose down`
- Ver logs backend: `docker compose logs -f backend`
- Ver logs frontend: `docker compose logs -f frontend`

---
## Desenvolvimento local sem Docker
### Backend
```powershell
cd backend
npm install
npm run dev
```
`.env` deve ter:
```
PORT=4000
DB_HOST=localhost
DB_USER=root
DB_PASSWORD=root123
DB_NAME=svce
JWT_SECRET=your_secret_here
```
### Frontend
```powershell
cd frontend
npm install
npm run dev -- --host 0.0.0.0 --port 3000
```

---
## Migração concluída
- O código PHP original foi arquivado no diretório `legacy-php/`.
- Funcionalidades principais implementadas (MVP):
  - CRUD clientes (`/api/clientes`)
  - Rotas de health `GET /api/health`
  - UI pages React (`/`, `/clientes`)

---
## Próximos passos (em ordem)
1. Autenticação JWT + role middleware (`dono`, `gerente`, `vendedor`).
2. CRUD produtos + categorias + fornecedores + usuários.
3. Endpoint de vendas (registro de itens + decremento de estoque transacional).
4. Frontend: telas de produto, venda, usuários e proteção de rotas.
5. Testes automáticos: Jest+Supertest (backend) e RTL (frontend).
6. CI/CD: GitHub Actions build/lint/test/deploy.

---
## Dica de comando rápido para migrations:
- Rebuild DB: `docker compose down -v && docker compose up -d --build`

---
## Dicas para portfolio
- Mostrar before/after: `legacy-php` vs `frontend/backend`.
- Colocar endpoints no README + Postman collection.
- Demonstrar ambiente Docker funcionando em 5 min.
