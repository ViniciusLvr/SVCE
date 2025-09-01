# 📦 SVCE - Sistema de Vendas e Controle de Estoque

O **SVCE** é um sistema simples de gerenciamento de vendas e controle de estoque, desenvolvido em **PHP** com **MySQL**.  
Ele permite cadastrar produtos, fornecedores e clientes, além de registrar vendas e acompanhar o estoque em tempo real.

---

## 🚀 Funcionalidades

- 🔑 **Autenticação de usuários** (login/logout)
- 👤 **Cadastro e listagem de clientes**
- 🏢 **Cadastro e listagem de fornecedores**
- 📦 **Cadastro e controle de produtos**
  - Prevenção de duplicidade
  - Atualização de estoque automático
- 💰 **Gestão de vendas**
  - Registro de vendas
  - Registro de itens da venda
  - Detalhamento e listagem de vendas
- 📊 **Controle de estoque integrado**

---

## 📂 Estrutura do Projeto

SVCE-main/
│── categoria.php # Gerenciamento de categorias
│── clientes/ # Módulo de clientes
│ └── clientes.php
│── fornecedor/ # Módulo de fornecedores
│ └── fornecedor.php
│── produto/ # Módulo de produtos
│ ├── produto.php
│ └── get_preco_produto.php
│── venda/ # Módulo de vendas
│ ├── registrarVenda.php
│ ├── registrarItens.php
│ ├── listarVendas.php
│ └── detalhesVenda.php
│── public/ # Telas públicas
│ ├── login.php
│ ├── logout.php
│ ├── cadastro.php
│ └── painel.php
│── config/ # Configuração do sistema
│ ├── conexao.php
│ └── auth.php
└── README.md


---

## 🛠️ Tecnologias Utilizadas

- **PHP 8+**
- **MySQL/MariaDB**
- **HTML5, CSS3**
- **JavaScript (básico)**
- **Servidor Apache ou Nginx**

---

## ⚙️ Instalação e Uso

1. Clone este repositório:

   git clone https://github.com/ViniciusLvr/SVCE.git

   
2. Importe o banco de dados (arquivo .sql será fornecido quando o projeto estiver 100%).

3. Configure a conexão no arquivo:

    config/conexao.php

4. Inicie o servidor local (Apache/Nginx) e acesse:

    http://localhost/SVCE/public/login.php


👨‍💻 Autores
Desenvolvido por Vinicius Santos de Oliveira, Walter e Winicius

📜 Licença
Este projeto é distribuído sob a licença MIT.
Sinta-se livre para usar, modificar e compartilhar.
