const { Sequelize } = require('sequelize');
require('dotenv').config();

const sequelize = new Sequelize(process.env.DB_NAME || 'svce', process.env.DB_USER || 'root', process.env.DB_PASSWORD || '', {
  host: process.env.DB_HOST || 'localhost',
  dialect: 'mysql',
  logging: false,
  define: {
    timestamps: false,
    underscored: false,
  },
});

module.exports = { sequelize };
