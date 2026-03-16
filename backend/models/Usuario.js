const { DataTypes } = require('sequelize');
const { sequelize } = require('../db');

const Usuario = sequelize.define('Usuario', {
  id: {
    type: DataTypes.INTEGER,
    autoIncrement: true,
    primaryKey: true,
  },
  nome: {
    type: DataTypes.STRING(100),
    allowNull: false,
  },
  email: {
    type: DataTypes.STRING(100),
    allowNull: false,
    unique: true,
    validate: {
      isEmail: true,
    },
  },
  senha: {
    type: DataTypes.STRING(255),
    allowNull: false,
  },
  cpf: {
    type: DataTypes.STRING(14),
    allowNull: false,
    unique: true,
    field: 'CPF',
  },
  psecreta: {
    type: DataTypes.STRING(255),
    allowNull: false,
    defaultValue: 'NA',
  },
  cargo: {
    type: DataTypes.ENUM('vendedor', 'gerente', 'dono'),
    allowNull: false,
    defaultValue: 'vendedor',
  },
  created_at: {
    type: DataTypes.DATE,
    defaultValue: DataTypes.NOW,
    allowNull: false,
    field: 'created_at',
  },
}, {
  tableName: 'usuarios',
});

module.exports = { Usuario };
