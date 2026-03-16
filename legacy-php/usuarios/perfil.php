<?php
require_once '../config/auth.php';
require_once '../config/conexao.php';

// Redireciona para a página de login se o usuário não estiver autenticado
if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../public/login.php');
    exit;
}

// Dados do usuário logado
$idLogado = $_SESSION['usuario_id'];
$stmt = $pdo->prepare("SELECT id, nome, email, cargo, CPF, psecreta, created_at FROM usuarios WHERE id = ?");
$stmt->execute([$idLogado]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

// Se for dono, puxa todos usuários
$usuarios = [];
if ($_SESSION['cargo'] === 'dono') {
    $stmtAll = $pdo->query("SELECT id, nome, email, cargo FROM usuarios ORDER BY id ASC");
    $usuarios = $stmtAll->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Meu Perfil - Sistema de Vendas</title>
    <link rel="icon" href="../img/CompreFacil.png" type="image/png">
    <link rel="stylesheet" href="../assets/style/animated-gradient.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

    <nav class="navbar mb-4" style="background: rgba(33, 37, 41, 0.85);">
        <div class="container d-flex justify-content-between align-items-center">
            <a class="navbar-brand d-flex align-items-center" href="../public/painel.php">
                <img src="../img/CompreFacil.png" alt="Logo do Sistema Compre Fácil" width="48" height="40" class="me-2"
                    style="object-fit:contain;">
                <span class="fw-bold text-white">Compre Fácil</span>
            </a>
            <a href="../public/painel.php" class="btn btn-danger">Voltar ao Painel</a>
        </div>
    </nav>

    <div class="container bg-light p-4 rounded shadow-sm">
        <h2 class="mb-4">Meu Perfil</h2>

        <div class="card mb-4">
            <div class="card-body">
                <p><strong>Nome:</strong> <?= htmlspecialchars($usuario['nome']) ?></p>
                <p><strong>Email:</strong> <?= htmlspecialchars($usuario['email']) ?></p>
                <p><strong>Cargo:</strong> <?= htmlspecialchars($usuario['cargo']) ?></p>
                <p><strong>CPF:</strong> <?= htmlspecialchars($usuario['CPF']) ?></p>
                <p><strong>Cor Favorita:</strong> <?= htmlspecialchars($usuario['psecreta']) ?></p>
                <p><strong>Data de Criação:</strong> <?= htmlspecialchars($usuario['created_at']) ?></p>
            </div>
        </div>

        <?php if ($_SESSION['cargo'] === 'dono'): ?>
        <h3 class="mb-3">Todos os Usuários</h3>
        <table class="table table-bordered table-hover align-middle">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Usuário</th>
                    <th>Email</th>
                    <th>Cargo</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($usuarios as $user): ?>
                <tr>
                    <td><?= htmlspecialchars($user['id']) ?></td>
                    <td><?= htmlspecialchars($user['nome']) ?></td>
                    <td><?= htmlspecialchars($user['email']) ?></td>
                    <td><?= htmlspecialchars($user['cargo']) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <a href="usuariosCrud.php" class="btn btn-primary">Gerenciar Usuários</a>
        <?php endif; ?>
    </div>

</body>

</html>