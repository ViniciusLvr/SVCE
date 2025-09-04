<?php
require_once 'config/conexao.php';

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

                <?php foreach ($categorias as $categorias): ?>
<div class="modal fade" id="editarCliente<?= $categorias['id'] ?>" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="post">
        <div class="modal-header">
          <h5 class="modal-title">Editar Cliente</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="editar_id" value="<?= $cliente['id'] ?>">
          <div class="mb-3">
            <label class="form-label">Nome</label>
            <input type="text" name="nome" class="form-control" 
                   value="<?= htmlspecialchars($cliente['nome']) ?>" >
          </div>
          <div class="mb-3">
            <label class="form-label">CPF/CNPJ</label>
            <input type="text" name="cpf_cnpj" class="form-control" 
                   value="<?= htmlspecialchars($categorias['cpf_cnpj']) ?>" >
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Salvar Alterações</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        </div>
      </form>
    </div>
  </div>
</div>
<?php endforeach; ?>

        </div>

        <a href="../public/painel.php" class="btn btn-danger mt-4">Voltar ao painel</a>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
                </tbody>
            </table>
        </div>

        <a href="../public/painel.php" class="btn btn-danger mt-4">Voltar ao painel</a>
    </div>
</body>
</html>
