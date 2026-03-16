const { DataTypes } = require('sequelize');
const { sequelize } = require('../db');

const Cliente = sequelize.define('Cliente', {
  id: {
    type: DataTypes.INTEGER,
    autoIncrement: true,
    primaryKey: true,
  },
  nome: {
    type: DataTypes.STRING(100),
    allowNull: false,
  },
  telefone: {
    type: DataTypes.STRING(20),
    allowNull: false,
  },
  cpf: {
    type: DataTypes.STRING(11),
    allowNull: true,
    unique: true,
    validate: {
      len: [11, 11],
    },
  },
  cnpj: {
    type: DataTypes.STRING(14),
    allowNull: true,
    unique: true,
    validate: {
      len: [14, 14],
    },
  },
  created_at: {
    type: DataTypes.DATE,
    defaultValue: DataTypes.NOW,
    allowNull: false,
  },
}, {
  tableName: 'clientes',
});

module.exports = { Cliente };
