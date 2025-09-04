<?php
require_once '../SVCE/config/conexao.php';

function adicionarCategoria($pdo, $nome) {
    $sql = "INSERT INTO categorias (nome) VALUES (:nome)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':nome' => $nome]);
}

function excluirCategoria($pdo, $id) {
    $sql = "DELETE FROM categorias WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $id]);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['nome'])) {
    $nome = $_POST['nome'] ?? '';
    if ($nome) {
        adicionarCategoria($pdo, $nome);
        header("Location: categoria.php");
        exit();
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['excluir_id'])) {
    $excluir_id = $_POST['excluir_id'];
    excluirCategoria($pdo, $excluir_id);
    header("Location: categoria.php");
    exit();
}

$stmt = $pdo->query("SELECT * FROM categorias ORDER BY created_at DESC");
$categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Categorias</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1 class="mb-4">Cadastro de Categorias</h1>

        <form method="post" class="border p-4 rounded shadow-sm bg-light mb-5">
            <div class="mb-3">
                <input type="text" name="nome" class="form-control" placeholder="Nome da Categoria" required>
            </div>
            <button type="submit" class="btn btn-success">Cadastrar</button>
        </form>

        <h2 class="mb-3">Categorias Cadastradas</h2>
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Cadastro</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($categorias as $categoria): ?>
                        <tr>
                            <td><?= htmlspecialchars($categoria['id']) ?></td>
                            <td><?= htmlspecialchars($categoria['nome']) ?></td>
                            <td><?= htmlspecialchars($categoria['created_at']) ?></td>
                            <td>
                                <form method="post" onsubmit="return confirm('Tem certeza que deseja excluir esta categoria?');">
                                    <input type="hidden" name="excluir_id" value="<?= htmlspecialchars($categoria['id']) ?>">
                                    <button type="submit" class="btn btn-sm btn-danger">Excluir</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <a href="../public/painel.php" class="btn btn-danger mt-4">Voltar ao painel</a>
    </div>
</body>
</html>
