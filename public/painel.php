<?php
session_start();
if (!isset($_SESSION['acesso_permitido']) || $_SESSION['acesso_permitido'] !== true) {
    header("Location: login.php");
    exit();
}

require_once '../config/auth.php';
$cargo = getCargo();
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8" />
    <title>Painel - Sistema de Vendas</title>
    <link rel="icon" href="../img/CompreFacil.png" type="image/png">
    <link rel="stylesheet" href="../assets/style/animated-gradient.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
</head>

<body>

    <nav class="navbar mb-4" style="background: rgba(33, 37, 41, 0.85);">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="#">
                <img src="../img/CompreFacil.png" alt="Logo do Sistema Compre Fácil" width="48" height="40" class="me-2"
                    style="object-fit:contain;">
                <span class="fw-bold text-white">Compre Fácil</span>
            </a>

            <div class="d-flex align-items-center gap-3">
                <!-- Botão unificado de perfil + cargo -->
                <a href="../usuarios/perfil.php" class="btn btn-info text-white d-flex align-items-center gap-2">
                    <i class="bi bi-person-circle"></i>
                    <span><?php echo ucfirst($cargo); ?></span>
                </a>

                <!-- Botão sair -->
                <a href="logout.php" class="btn btn-danger">Sair</a>
            </div>

        </div>
    </nav>

    <div class="container mt-5 bg-light p-4 rounded shadow-sm">
        <div class="row g-3">

            <?php if (in_array($cargo, ['gerente', 'dono'])): ?>
            <div class="col-md-4">
                <a href="../categoria.php" class="btn btn-primary w-100 py-5 d-flex flex-column align-items-center">
                    <i class="bi bi-tags fs-1 mb-2"></i>
                    Gerenciar Categorias
                </a>
            </div>
            <div class="col-md-4">
                <a href="../fornecedor/fornecedor.php"
                    class="btn btn-primary w-100 py-5 d-flex flex-column align-items-center">
                    <i class="bi bi-truck fs-1 mb-2"></i>
                    Gerenciar Fornecedores
                </a>
            </div>
            <div class="col-md-4">
                <a href="../produto/produto.php"
                    class="btn btn-primary w-100 py-5 d-flex flex-column align-items-center">
                    <i class="bi bi-box-seam fs-1 mb-2"></i>
                    Gerenciar Produtos
                </a>
            </div>
            <?php endif; ?>

            <?php if (in_array($cargo, ['vendedor', 'gerente', 'dono'])): ?>
            <div class="col-md-4">
                <a href="../clientes/clientes.php"
                    class="btn btn-primary w-100 py-5 d-flex flex-column align-items-center">
                    <i class="bi bi-people fs-1 mb-2"></i>
                    Gerenciar Clientes
                </a>
            </div>
            <div class="col-md-4">
                <a href="../venda/registrarVenda.php"
                    class="btn btn-primary w-100 py-5 d-flex flex-column align-items-center">
                    <i class="bi bi-cart-plus fs-1 mb-2"></i>
                    Registrar Venda
                </a>
            </div>
            <div class="col-md-4">
                <a href="../venda/listarVendas.php"
                    class="btn btn-primary w-100 py-5 d-flex flex-column align-items-center">
                    <i class="bi bi-clock-history fs-1 mb-2"></i>
                    Histórico de Vendas
                </a>
            </div>
            <?php endif; ?>

            <?php if ($cargo === 'dono'): ?>
            <div class="col-md-12">
                <a href="../usuarios/usuariosCrud.php"
                    class="btn btn-warning w-100 py-5 d-flex flex-column align-items-center">
                    <i class="bi bi-person-gear fs-1 mb-2"></i>
                    Gerenciar Usuários
                </a>
            </div>
            <?php endif; ?>

        </div>
    </div>

    <?php if (isset($_GET['erro']) && $_GET['erro'] === 'acesso_negado'): ?>
    <div class="container mt-3">
        <div class="alert alert-danger">Você não tem permissão para acessar essa página.</div>
    </div>
    <?php endif; ?>

</body>

</html>