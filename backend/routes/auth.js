const express = require('express');
const bcrypt = require('bcryptjs');
const jwt = require('jsonwebtoken');
const { Usuario } = require('../models/Usuario');
const { authMiddleware } = require('../middleware/auth');

const router = express.Router();

router.post('/register', async (req, res) => {
  try {
    const { nome, email, senha, CPF, cargo } = req.body;
    if (!nome || !email || !senha || !CPF) {
      return res.status(400).json({ message: 'nome, email, senha e CPF obrigatórios' });
    }

    const existing = await Usuario.findOne({ where: { email } });
    if (existing) return res.status(400).json({ message: 'Email já cadastrado' });

    const existingCpf = await Usuario.findOne({ where: { cpf: CPF } });
    if (existingCpf) return res.status(400).json({ message: 'CPF já cadastrado' });

    const hashed = await bcrypt.hash(senha, 10);
    const user = await Usuario.create({ nome, email, senha: hashed, cpf: CPF, cargo: cargo || 'vendedor' });

    return res.status(201).json({ id: user.id, nome: user.nome, email: user.email, cargo: user.cargo });
  } catch (error) {
    console.error('Erro auth register:', error);
    return res.status(500).json({ message: 'Erro ao registrar usuário' });
  }
});

router.post('/login', async (req, res) => {
  try {
    const { email, senha } = req.body;
    if (!email || !senha) {
      return res.status(400).json({ message: 'email e senha obrigatórios' });
    }

    const user = await Usuario.findOne({ where: { email } });
    if (!user) return res.status(401).json({ message: 'Credenciais inválidas' });

    const isValid = await bcrypt.compare(senha, user.senha);
    if (!isValid) return res.status(401).json({ message: 'Credenciais inválidas' });

    const token = jwt.sign(
      { id: user.id, nome: user.nome, email: user.email, cargo: user.cargo },
      process.env.JWT_SECRET || 'secret_key',
      { expiresIn: '6h' }
    );

    return res.json({ token, user: { id: user.id, nome: user.nome, email: user.email, cargo: user.cargo } });
  } catch (error) {
    console.error('Erro auth login:', error);
    return res.status(500).json({ message: 'Erro ao efetuar login' });
  }
});

router.get('/me', authMiddleware, async (req, res) => {
  try {
    const user = await Usuario.findByPk(req.user.id, { attributes: ['id', 'nome', 'email', 'cargo', 'CPF'] });
    if (!user) return res.status(404).json({ message: 'Usuário não encontrado' });
    res.json(user);
  } catch (error) {
    console.error('Erro auth me:', error);
    res.status(500).json({ message: 'Erro ao buscar usuário' });
  }
});

module.exports = router;
