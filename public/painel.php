<?php
// Inclui o arquivo de autenticação (ajustando caminho relativo)
require_once __DIR__ . "/../config/auth.php";
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8" />
    <title>Painel - Sistema de Vendas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>

<body>
    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Sistema de Vendas - Painel Administrativo</h2>
            <a href="logout.php" class="btn btn-danger">Sair</a>
        </div>

        <div class="row g-3">
            <div class="col-md-4">
                <a href="../categoria.php" class="btn btn-primary w-100">Gerenciar Categorias</a>
            </div>
            <div class="col-md-4">
                <a href="../fornecedor.php" class="btn btn-primary w-100">Gerenciar Fornecedores</a>
            </div>
            <div class="col-md-4">
                <a href="../produto.php" class="btn btn-primary w-100">Gerenciar Produtos</a>
            </div>
            <div class="col-md-4">
                <a href="../clientes.php" class="btn btn-primary w-100">Gerenciar Clientes</a>
            </div>
            <div class="col-md-4">
                <a href="../registrar_venda.php" class="btn btn-primary w-100">Registrar Venda</a>
            </div>
            <div class="col-md-4">
                <a href="../listar_vendas.php" class="btn btn-primary w-100">Historico de Vendas</a>
            </div>
        </div>
    </div>
</body>

</html>
