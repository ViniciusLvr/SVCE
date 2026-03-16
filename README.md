# SVCE (React + TypeScript + Node + Docker)

Projeto migrado do sistema original PHP/MYSQL para arquitetura moderna:
- **Frontend**: React + TypeScript + Vite
- **Backend**: Node + Express + Sequelize + MySQL
- **Infra**: Docker Compose (MySQL, backend, frontend)

## Estrutura do repo
- `frontend/` - app React+TS
- `backend/` - API REST em Express
- `db/CF atualizado.sql` - script SQL com schema + dados originais
- `docker-compose.yml` - orquestra��o Docker
- `legacy-php/` - c�digo PHP arquivado (n�o mais necess�rio)

---
## Pr�-requisitos
- Docker Desktop instalado (Windows) ou Docker Engine (Linux/macOS)
- Opcional: Node.js se rodar local sem Docker

---
## ?? Rodar tudo com Docker (recomendado)
1. No diret�rio raiz do projeto:
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

> O backend cria um usuário admin padrão na primeira execução:
> - email: `admin@svce.com`
> - senha: `admin123`
> - cargo: `dono`
>
> A autenticação usa JWT; endpoints:
> - `POST /api/auth/login` (email/senha)
> - `POST /api/auth/register`
> - `GET /api/auth/me` (token Bearer)

---
## Comandos �teis
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
## Migra��o conclu�da
- O c�digo PHP original foi arquivado no diret�rio `legacy-php/`.
- Funcionalidades principais implementadas (MVP):
  - CRUD clientes (`/api/clientes`)
  - Rotas de health `GET /api/health`
  - UI pages React (`/`, `/clientes`)

---
## Pr�ximos passos (em ordem)
1. Autentica��o JWT + role middleware (`dono`, `gerente`, `vendedor`).
2. CRUD produtos + categorias + fornecedores + usu�rios.
3. Endpoint de vendas (registro de itens + decremento de estoque transacional).
4. Frontend: telas de produto, venda, usu�rios e prote��o de rotas.
5. Testes autom�ticos: Jest+Supertest (backend) e RTL (frontend).
6. CI/CD: GitHub Actions build/lint/test/deploy.

---
## Dica de comando r�pido para migrations:
- Rebuild DB: `docker compose down -v && docker compose up -d --build`

---
## Dicas para portfolio
- Mostrar before/after: `legacy-php` vs `frontend/backend`.
- Colocar endpoints no README + Postman collection.
- Demonstrar ambiente Docker funcionando em 5 min.
