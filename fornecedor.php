<?php
require_once 'config/conexao.php';

function adicionarFornecedor($pdo, $nome, $cnpj, $telefone) {
    $sql = "INSERT INTO fornecedor (nome, cnpj, telefone) VALUES (:nome, :cnpj, :telefone)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':nome' => $nome,
        ':cnpj' => $cnpj,
        ':telefone' => $telefone
    ]);
}

function excluirFornecedor($pdo, $id) {
    $sql = "DELETE FROM fornecedor WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $id]);
}

// Inserção
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nome'])) {
    $nome = $_POST['nome'] ?? '';
    $cnpj = preg_replace('/\D/', '', $_POST['cnpj'] ?? '');
    $telefone = preg_replace('/\D/', '', $_POST['telefone'] ?? '';

    if (!empty($nome) && strlen($cnpj) === 14 && (strlen($telefone) === 10 || srtlen($telefone) === 11)) {
        adicionarFornecedor($pdo, $nome, $cnpj, $telefone);
        header("Location: fornecedor.php");
        exit();
    } else {
        echo "<div class='alert alert-danger text-center'>! Prencha corretamente os campos!</div>;
        }
}

// Exclusão
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['excluir_id'])) {
    $excluir_id = $_POST['excluir_id'];
    excluirFornecedor($pdo, $excluir_id);
    header("Location: fornecedor.php");
    exit();
}

// Listagem
$stmt = $pdo->query("SELECT * FROM fornecedor ORDER BY created_at DESC");
$fornecedores = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title>Fornecedores</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <h1 class="mb-4">Cadastro de Fornecedores</h1>

        <form method="post" class="border p-4 rounded shadow-sm bg-light mb-5">
            <div class="row mb-3">
                <div class="col">
                    <input type="text" name="nome" class="form-control" placeholder="Nome" required>
                </div>
                <div class="col">
                    <input type="text" name="cnpj" class="form-control" placeholder="CNPJ" 
                    pattern="\d{14}" title="Digite apenas números (14 digitos)" required>
                </div>
                <div class="col">
                    <input type="text" name="telefone" class="form-control" placeholder="Telefone" 
                    pattern="\d{10,11}" title="Digite apenas números (10 ou 11 dígitos)" required>
                </div>
            </div>
            <button type="submit" class="btn btn-success">Cadastrar</button>
        </form>

        <h2 class="mb-3">Fornecedores Cadastrados</h2>
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>CNPJ</th>
                        <th>Telefone</th>
                        <th>Cadastro</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($fornecedores as $f): ?>
                    <tr>
                        <td><?= htmlspecialchars($f['id']) ?></td>
                        <td><?= htmlspecialchars($f['nome']) ?></td>
                        <td><?= htmlspecialchars($f['cnpj']) ?></td>
                        <td><?= htmlspecialchars($f['telefone']) ?></td>
                        <td><?= htmlspecialchars($f['created_at']) ?></td>
                        <td>
                            <form method="post"
                                onsubmit="return confirm('Tem certeza que deseja excluir este fornecedor?');">
                                <input type="hidden" name="excluir_id" value="<?= htmlspecialchars($f['id']) ?>">
                                <button type="submit" class="btn btn-sm btn-danger">Excluir</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <a href="public/painel.php" class="btn btn-danger mt-4">Voltar ao painel</a>
    </div>
</body>

</html>
