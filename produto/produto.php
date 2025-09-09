<?php
require_once '../config/conexao.php';
session_start();

// =================== FUNÇÕES ===================
function listarProdutos() {
    global $pdo;
    $sql = "SELECT p.*, c.nome AS categoria, f.nome AS fornecedor
            FROM produtos p
            JOIN categorias c ON p.categoria_id = c.id
            JOIN fornecedor f ON p.fornecedor_id = f.id
            ORDER BY p.id DESC";
    return $pdo->query($sql)->fetchAll();
}

function buscarProdutoPorNome($nome) {
    global $pdo;
    $sql = "SELECT * FROM produtos WHERE nome = :nome";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['nome' => $nome]);
    return $stmt->fetch();
}

function adicionarProduto($nome, $preco, $quantidade, $categoria_id, $fornecedor_id) {
    global $pdo;
    $sql = "INSERT INTO produtos (nome, preco, quantidade_estoque, categoria_id, fornecedor_id)
            VALUES (:nome, :preco, :quantidade, :categoria, :fornecedor)";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([
        'nome' => $nome,
        'preco' => $preco,
        'quantidade' => $quantidade,
        'categoria' => $categoria_id,
        'fornecedor' => $fornecedor_id
    ]);
}

function atualizarEstoque($id, $quantidade) {
    global $pdo;
    $sql = "UPDATE produtos SET quantidade_estoque = quantidade_estoque + :quantidade WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([
        'quantidade' => $quantidade,
        'id' => $id
    ]);
}

function editarProduto($id, $nome, $preco, $quantidade, $categoria_id, $fornecedor_id) {
    global $pdo;
    $sql = "UPDATE produtos 
            SET nome = :nome, preco = :preco, quantidade_estoque = :quantidade, 
                categoria_id = :categoria, fornecedor_id = :fornecedor 
            WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([
        'id' => $id,
        'nome' => $nome,
        'preco' => $preco,
        'quantidade' => $quantidade,
        'categoria' => $categoria_id,
        'fornecedor' => $fornecedor_id
    ]);
}

function deletarProduto($id) {
    global $pdo;
    $sql = "DELETE FROM produtos WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute(['id' => $id]);
}

// =================== AÇÕES ===================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Confirmação de produto duplicado -> soma estoque
    if (isset($_POST['confirmar_adicao']) && $_POST['confirmar_adicao'] === 'sim' && isset($_SESSION['produto_duplicado'])) {
        $produto = $_SESSION['produto_duplicado'];
        atualizarEstoque($produto['id'], $_SESSION['quantidade_temp']);
        unset($_SESSION['produto_duplicado'], $_SESSION['quantidade_temp']);
        header("Location: produto.php");
        exit();
    }

    // Adicionar produto
    if (isset($_POST['nome'], $_POST['preco'], $_POST['quantidade'], $_POST['categoria_id'], $_POST['fornecedor_id']) && !isset($_POST['editar_id'])) {
        $nome = $_POST['nome'];
        $preco = $_POST['preco'];
        $quantidade = $_POST['quantidade'];
        $categoria_id = $_POST['categoria_id'];
        $fornecedor_id = $_POST['fornecedor_id'];

        if (!empty($nome) && is_numeric($preco)) {
            $produtoExistente = buscarProdutoPorNome($nome);

            if ($produtoExistente) {
                $_SESSION['produto_duplicado'] = $produtoExistente;
                $_SESSION['quantidade_temp'] = $quantidade;
            } else {
                adicionarProduto($nome, $preco, $quantidade, $categoria_id, $fornecedor_id);
                header("Location: produto.php");
                exit();
            }
        }
    }

    // Editar produto
    if (isset($_POST['editar_id'])) {
        $id = $_POST['editar_id'];
        $nome = $_POST['nome'];
        $preco = $_POST['preco'];
        $quantidade = $_POST['quantidade'];
        $categoria_id = $_POST['categoria_id'];
        $fornecedor_id = $_POST['fornecedor_id'];

        editarProduto($id, $nome, $preco, $quantidade, $categoria_id, $fornecedor_id);
        header("Location: produto.php");
        exit();
    }

    // Deletar produto
    if (isset($_POST['deletar_id'])) {
        deletarProduto($_POST['deletar_id']);
        header("Location: produto.php");
        exit();
    }
}

// =================== CARREGAR DADOS ===================
$produtos = listarProdutos();
$categorias = $pdo->query("SELECT * FROM categorias")->fetchAll();
$fornecedores = $pdo->query("SELECT * FROM fornecedor")->fetchAll();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <title>Produtos</title>
    <meta charset="utf-8">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">
    <h1 class="mb-4">Gerenciar Produtos</h1>

    <?php if (isset($_SESSION['produto_duplicado'])): ?>
    <div class="alert alert-warning">
        O produto <strong><?= htmlspecialchars($_SESSION['produto_duplicado']['nome']) ?></strong> já existe.
        Deseja somar <strong><?= $_SESSION['quantidade_temp'] ?></strong> ao estoque atual
        (<?= $_SESSION['produto_duplicado']['quantidade_estoque'] ?>)?
        <form method="POST" class="mt-3">
            <input type="hidden" name="confirmar_adicao" value="sim">
            <button type="submit" class="btn btn-success">Sim, somar</button>
            <a href="produto.php" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
    <?php endif; ?>

    <!-- Formulário de adicionar -->
    <form method="POST" class="mb-4">
        <div class="row g-3">
            <div class="col-md-3">
                <input type="text" name="nome" class="form-control" placeholder="Nome do Produto" required>
            </div>
            <div class="col-md-2">
                <input type="number" step="0.01" name="preco" class="form-control" placeholder="Preço" required>
            </div>
            <div class="col-md-2">
                <input type="number" name="quantidade" class="form-control" placeholder="Estoque" required>
            </div>
            <div class="col-md-2">
                <select name="categoria_id" class="form-control" required>
                    <option value="">Categoria</option>
                    <?php foreach ($categorias as $cat): ?>
                    <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['nome']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <select name="fornecedor_id" class="form-control" required>
                    <option value="">Fornecedor</option>
                    <?php foreach ($fornecedores as $forn): ?>
                    <option value="<?= $forn['id'] ?>"><?= htmlspecialchars($forn['nome']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <button type="submit" class="btn btn-primary mt-3">Adicionar Produto</button>
    </form>

    <!-- Tabela de produtos -->
    <h2 class="mb-3">Lista de Produtos</h2>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Produto</th>
                <th>Preço</th>
                <th>Estoque</th>
                <th>Categoria</th>
                <th>Fornecedor</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($produtos as $p): ?>
            <tr>
                <td><?= htmlspecialchars($p['nome']) ?></td>
                <td>R$ <?= number_format($p['preco'], 2, ',', '.') ?></td>
                <td><?= $p['quantidade_estoque'] ?></td>
                <td><?= htmlspecialchars($p['categoria']) ?></td>
                <td><?= htmlspecialchars($p['fornecedor']) ?></td>
                <td>
                    <!-- Excluir -->
                    <form method="POST" class="d-inline" onsubmit="return confirm('Tem certeza que deseja excluir este produto?');">
                        <input type="hidden" name="deletar_id" value="<?= $p['id'] ?>">
                        <button type="submit" class="btn btn-sm btn-danger">Excluir</button>
                    </form>

                    <!-- Editar -->
                    <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editarProduto<?= $p['id'] ?>">
                        Editar
                    </button>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Modais de edição -->
    <?php foreach ($produtos as $p): ?>
    <div class="modal fade" id="editarProduto<?= $p['id'] ?>" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <form method="POST">
            <div class="modal-header">
              <h5 class="modal-title">Editar Produto - <?= htmlspecialchars($p['nome']) ?></h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="editar_id" value="<?= $p['id'] ?>">

                <div class="mb-3">
                    <label>Nome</label>
                    <input type="text" name="nome" class="form-control" value="<?= htmlspecialchars($p['nome']) ?>" required>
                </div>
                <div class="mb-3">
                    <label>Preço</label>
                    <input type="number" step="0.01" name="preco" class="form-control" value="<?= $p['preco'] ?>" required>
                </div>
                <div class="mb-3">
                    <label>Estoque</label>
                    <input type="number" name="quantidade" class="form-control" value="<?= $p['quantidade_estoque'] ?>" required>
                </div>
                <div class="mb-3">
                    <label>Categoria</label>
                    <select name="categoria_id" class="form-control" required>
                        <?php foreach ($categorias as $cat): ?>
                            <option value="<?= $cat['id'] ?>" <?= $cat['id'] == $p['categoria_id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($cat['nome']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label>Fornecedor</label>
                    <select name="fornecedor_id" class="form-control" required>
                        <?php foreach ($fornecedores as $forn): ?>
                            <option value="<?= $forn['id'] ?>" <?= $forn['id'] == $p['fornecedor_id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($forn['nome']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
              <button type="submit" class="btn btn-success">Salvar Alterações</button>
            </div>
          </form>
        </div>
      </div>
    </div>
    <?php endforeach; ?>

    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <a href="../public/painel.php" class="btn btn-danger">Voltar ao painel</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
