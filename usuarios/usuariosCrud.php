<?php
// Autenticação e conexão
require_once '../config/auth.php';
require_once '../config/conexao.php';

// Apenas o dono pode acessar
if ($_SESSION['cargo'] !== 'dono') {
    header("Location: ../public/painel.php");
    exit;
}

// CRUD de usuários
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_cargo'])) {
        $id = intval($_POST['id']);
        $cargo = $_POST['cargo'];

        $stmt = $pdo->prepare("UPDATE usuarios SET cargo = ? WHERE id = ?");
        $stmt->execute([$cargo, $id]);
    }

    if (isset($_POST['delete_user'])) {
        $id = intval($_POST['id']);

        $stmt = $pdo->prepare("DELETE FROM usuarios WHERE id = ?");
        $stmt->execute([$id]);
    }
}

// Buscar usuários
$stmt = $pdo->query("SELECT id, nome, email, cargo FROM usuarios ORDER BY id ASC");
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Gerenciar Usuários - Sistema de Vendas</title>
    <link rel="icon" href="../img/CompreFacil.png" type="image/png">
    <link rel="stylesheet" href="../assets/style/animated-gradient.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

    <nav class="navbar mb-4" style="background: rgba(33, 37, 41, 0.85);">
        <div class="container d-flex justify-content-between align-items-center">
            <a class="navbar-brand d-flex align-items-center" href="painel.php">
                <img src="../img/CompreFacil.png" alt="Logo do Sistema Compre Fácil" width="48" height="40" class="me-2"
                    style="object-fit:contain;">
                <span class="fw-bold text-white">Compre Fácil</span>
            </a>
            <a href="../public/painel.php" class="btn btn-danger">Voltar ao Painel</a>
        </div>
    </nav>

    <div class="container bg-light p-4 rounded shadow-sm">
        <h2 class="mb-4">Gerenciar Usuários</h2>

        <table class="table table-bordered table-hover align-middle">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Usuário</th>
                    <th>Email</th>
                    <th>Cargo</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($usuarios as $user): ?>
                <tr>
                    <td><?= htmlspecialchars($user['id']) ?></td>
                    <td><?= htmlspecialchars($user['nome']) ?></td>
                    <td><?= htmlspecialchars($user['email']) ?></td>
                    <td>
                        <form method="POST" class="d-flex">
                            <input type="hidden" name="id" value="<?= $user['id'] ?>">
                            <select name="cargo" class="form-select me-2">
                                <option value="dono" <?= $user['cargo'] === 'dono' ? 'selected' : '' ?>>Dono</option>
                                <option value="gerente" <?= $user['cargo'] === 'gerente' ? 'selected' : '' ?>>Gerente
                                </option>
                                <option value="vendedor" <?= $user['cargo'] === 'vendedor' ? 'selected' : '' ?>>Vendedor
                                </option>
                            </select>
                            <button type="submit" name="update_cargo" class="btn btn-success btn-sm">Salvar</button>
                        </form>
                    </td>
                    <td>
                        <?php if ($user['cargo'] !== 'dono'): ?>
                        <form method="POST" onsubmit="return confirm('Tem certeza que deseja excluir este usuário?');">
                            <input type="hidden" name="id" value="<?= $user['id'] ?>">
                            <button type="submit" name="delete_user" class="btn btn-danger btn-sm">Excluir</button>
                        </form>
                        <?php else: ?>
                        <span class="badge bg-secondary">Protegido</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

</body>

</html>