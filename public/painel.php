<?php
// Inclui o arquivo de autenticação (ajustando caminho relativo)
require_once '../config/auth.php';
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8" />
    <title>Painel - Sistema de Vendas</title>
    <link rel="icon" href="../img/CompreFacil.png" type="image/png">
    <link rel="stylesheet" href="../assets/style/animated-gradient.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

    <nav class="navbar" style="background: rgba(33, 37, 41, 0.85); mb-4;">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="#">
                <img src="../img/CompreFacil.png" alt="Logo do Sistema Compre Fácil" width="48" height="40" class="me-2" style="object-fit:contain;">
                <span class="fw-bold text-white">Compre Fácil</span>
            </a>

            <a href="logout.php" class="btn btn-danger">Sair</a>
        </div>
    </nav>

    <div class="container mt-5 bg-light p-4 rounded shadow-sm">
        <div class="row g-3">
            <div class="col-md-4">
                <a href="../categoria.php" class="btn btn-primary w-100 py-5">Gerenciar Categorias</a>
            </div>
            <div class="col-md-4">
                <a href="../fornecedor/fornecedor.php" class="btn btn-primary w-100 py-5">Gerenciar Fornecedores</a>
            </div>
            <div class="col-md-4">
                <a href="../produto/produto.php" class="btn btn-primary w-100 py-5">Gerenciar Produtos</a>
            </div>
            <div class="col-md-4">
                <a href="../clientes/clientes.php" class="btn btn-primary w-100 py-5">Gerenciar Clientes</a>
            </div>
            <div class="col-md-4">
                <a href="../venda/registrarVenda.php" class="btn btn-primary w-100 py-5">Registrar Venda</a>
            </div>
            <div class="col-md-4">
                <a href="../venda/listarVendas.php" class="btn btn-primary w-100 py-5">Historico de Vendas</a>
            </div>
        </div>
    </div>
    </div>
</body>

</html>