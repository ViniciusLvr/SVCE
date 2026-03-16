const express = require('express');
const cors = require('cors');
require('dotenv').config();

const { sequelize } = require('./db');
const { Cliente } = require('./models/Cliente');

const app = express();
const port = process.env.PORT || 4000;

app.use(cors());
app.use(express.json());

app.get('/api', (req, res) => {
  res.json({ status: 'OK', message: 'SVCE API running', version: '0.1.0' });
});

app.get('/api/health', (req, res) => {
  res.json({ uptime: process.uptime(), status: 'healthy' });
});

// Clientes endpoints
app.get('/api/clientes', async (req, res) => {
  try {
    const clientes = await Cliente.findAll({ order: [['id', 'ASC']] });
    res.json(clientes);
  } catch (error) {
    console.error('Erro buscar clientes:', error);
    res.status(500).json({ message: 'Erro ao listar clientes' });
  }
});

app.post('/api/clientes', async (req, res) => {
  try {
    const { nome, telefone, cpf, cnpj } = req.body;
    if (!nome || !telefone) {
      return res.status(400).json({ message: 'nome e telefone são obrigatórios' });
    }

    const cliente = await Cliente.create({ nome, telefone, cpf, cnpj });
    res.status(201).json(cliente);
  } catch (error) {
    console.error('Erro criar cliente:', error.errors || error);

    if (error.name === 'SequelizeUniqueConstraintError') {
      return res.status(400).json({ message: 'CPF ou CNPJ já cadastrado' });
    }

    res.status(500).json({ message: 'Erro ao criar cliente' });
  }
});

app.get('/api/clientes/:id', async (req, res) => {
  try {
    const cliente = await Cliente.findByPk(req.params.id);
    if (!cliente) return res.status(404).json({ message: 'Cliente não encontrado' });
    res.json(cliente);
  } catch (error) {
    console.error('Erro consultar cliente:', error);
    res.status(500).json({ message: 'Erro ao buscar cliente' });
  }
});

app.put('/api/clientes/:id', async (req, res) => {
  try {
    const cliente = await Cliente.findByPk(req.params.id);
    if (!cliente) return res.status(404).json({ message: 'Cliente não encontrado' });

    const { nome, telefone, cpf, cnpj } = req.body;
    await cliente.update({ nome, telefone, cpf, cnpj });
    res.json(cliente);
  } catch (error) {
    console.error('Erro atualizar cliente:', error);
    if (error.name === 'SequelizeUniqueConstraintError') {
      return res.status(400).json({ message: 'CPF ou CNPJ já cadastrado' });
    }
    res.status(500).json({ message: 'Erro ao atualizar cliente' });
  }
});

app.delete('/api/clientes/:id', async (req, res) => {
  try {
    const cliente = await Cliente.findByPk(req.params.id);
    if (!cliente) return res.status(404).json({ message: 'Cliente não encontrado' });

    await cliente.destroy();
    res.json({ message: 'Cliente excluído' });
  } catch (error) {
    console.error('Erro excluir cliente:', error);
    res.status(500).json({ message: 'Erro ao excluir cliente' });
  }
});

async function startup() {
  try {
    await sequelize.authenticate();
    console.log('Conectado ao banco de dados MySQL');

    await sequelize.sync();
    console.log('Sincronização de models concluída');

    app.listen(port, () => {
      console.log(`Backend started at http://localhost:${port}`);
    });
  } catch (err) {
    console.error('Erro ao iniciar backend:', err);
    process.exit(1);
  }
}

startup();

