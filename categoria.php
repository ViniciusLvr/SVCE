<?php
require_once 'config/conexao.php';

function adicionarCategoria($pdo, $nome)
{
    $sql = "INSERT INTO categorias (nome) VALUES (:nome)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':nome' => $nome]);
}

function excluirCategoria($pdo, $id)
{
    try {
        $sql = "DELETE FROM categorias WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        return true;
    } catch (PDOException $e) {
        if ($e->getCode() == '23000' && strpos($e->getMessage(), 'a foreign key constraint fails') !== false) {
            echo "<div class='alert alert-danger'>Não é possível excluir a categoria pois ela está relacionada a outros registros (ex: produtos).</div>";
        } else {
            echo "<div class='alert alert-danger'>Erro ao excluir categoria: ".$e->getMessage()."</div>";
        }
        return false;
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['nome'])) {
    $nome = trim($_POST['nome'] ?? '');
    if ($nome) {
        // Verifica se já existe categoria com esse nome (ignorando maiúsculas/minúsculas e espaços)
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM categorias WHERE LOWER(TRIM(nome)) = LOWER(TRIM(:nome))");
        $stmt->execute([':nome' => $nome]);
        $existe = $stmt->fetchColumn();
        if ($existe) {
            echo "<div class='alert alert-danger alert-dismissible fade show' role='alert'>
                    Já existe uma categoria com esse nome.
                    <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Fechar'></button>
                  </div>";
        } else {
            adicionarCategoria($pdo, $nome);
            header("Location: categoria.php");
            exit();
        }
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['excluir_id'])) {
    $excluir_id = $_POST['excluir_id'];
    ob_start();
    $ok = excluirCategoria($pdo, $excluir_id);
    $msg = ob_get_clean();
    if ($ok) {
        header("Location: categoria.php");
        exit();
    } else {
        echo $msg;
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['editar_id'])) {
    $editar_id = $_POST['editar_id'];
    $novo_nome = trim($_POST['novo_nome'] ?? '');
    if ($novo_nome) {
        // Verifica se já existe categoria com esse nome (ignorando maiúsculas/minúsculas e espaços), exceto a própria
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM categorias WHERE LOWER(TRIM(nome)) = LOWER(TRIM(:nome)) AND id != :id");
        $stmt->execute([':nome' => $novo_nome, ':id' => $editar_id]);
        $existe = $stmt->fetchColumn();
        if ($existe) {
            echo "<div class='alert alert-danger'>Já existe uma categoria com esse nome.</div>";
        } else {
            $sql = "UPDATE categorias SET nome = :nome WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':nome' => $novo_nome, ':id' => $editar_id]);
            header("Location: categoria.php");
            exit();
        }
    }
}

$stmt = $pdo->query("SELECT * FROM categorias ORDER BY created_at DESC");
$categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title>Categorias</title>
    <link rel="icon" href="img/CompreFacil.png" type="image/png">
    <link rel="stylesheet" href="assets/style/animated-gradient.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

    <nav class="navbar" style="background: rgba(33, 37, 41, 0.85); mb-4;">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="#">
                <img src="img/CompreFacil.png" alt="Logo do Sistema Compre Fácil" width="48" height="40" class="me-2" style="object-fit:contain;">
                <span class="fw-bold text-white">Compre Fácil</span>
            </a>

            <a href="public/painel.php" class="btn btn-danger mt-4">Voltar ao painel</a>
        </div>
    </nav>

    <div class="container bg-light p-4 rounded shadow-sm mb-5 mt-5">

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
                                    <form method="post" class="d-inline" onsubmit="return confirm('Tem certeza que deseja excluir esta categoria?');">
                                        <input type="hidden" name="excluir_id" value="<?= htmlspecialchars($categoria['id']) ?>">
                                        <button type="submit" class="btn btn-sm btn-danger">Excluir</button>
                                    </form>
                                    <!-- Botão para abrir o modal de edição -->
                                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#editarCategoria<?= $categoria['id'] ?>">
                                        Editar
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Modais de edição -->
            <?php foreach ($categorias as $categoria): ?>
                <div class="modal fade" id="editarCategoria<?= $categoria['id'] ?>" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form method="post">
                                <div class="modal-header">
                                    <h5 class="modal-title">Editar Categoria</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <input type="hidden" name="editar_id" value="<?= $categoria['id'] ?>">
                                    <div class="mb-3">
                                        <label class="form-label">Nome da Categoria</label>
                                        <input type="text" name="novo_nome" class="form-control" value="<?= htmlspecialchars($categoria['nome']) ?>" required>
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
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>