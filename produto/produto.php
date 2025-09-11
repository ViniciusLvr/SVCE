<?php
require_once '../config/conexao.php';
session_start();

// =================== CONFIG PAGINAÇÃO ===================
$registros_por_pagina = 10; // quantidade por página
$pagina_atual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
if ($pagina_atual < 1) $pagina_atual = 1;

$offset = ($pagina_atual - 1) * $registros_por_pagina;

// =================== FUNÇÕES ===================
function listarProdutos($limit, $offset)
{
    global $pdo;
    $sql = "SELECT p.*, c.nome AS categoria, f.nome AS fornecedor
            FROM produtos p
            JOIN categorias c ON p.categoria_id = c.id
            JOIN fornecedor f ON p.fornecedor_id = f.id
            ORDER BY p.id DESC
            LIMIT :limit OFFSET :offset";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll();
}

function contarProdutos()
{
    global $pdo;
    $sql = "SELECT COUNT(*) AS total FROM produtos";
    return $pdo->query($sql)->fetch()['total'];
}

function buscarProdutoPorNome($nome)
{
    global $pdo;
    $sql = "SELECT * FROM produtos WHERE nome = :nome";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['nome' => $nome]);
    return $stmt->fetch();
}

function buscarProdutoPorNomeEFornecedor($nome, $fornecedor_id)
{
    global $pdo;
    $sql = "SELECT * FROM produtos WHERE nome = :nome AND fornecedor_id = :fornecedor_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['nome' => $nome, 'fornecedor_id' => $fornecedor_id]);
    return $stmt->fetch();
}

function adicionarProduto($nome, $preco, $quantidade, $categoria_id, $fornecedor_id)
{
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

function atualizarEstoque($id, $quantidade)
{
    global $pdo;
    $sql = "UPDATE produtos SET quantidade_estoque = quantidade_estoque + :quantidade WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([
        'quantidade' => $quantidade,
        'id' => $id
    ]);
}

function editarProduto($id, $nome, $preco, $quantidade, $categoria_id, $fornecedor_id)
{
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

function deletarProduto($id)
{
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

        // CONVERTE O PREÇO PARA FORMATO PADRÃO (1234.56)
        $preco = str_replace('.', '', $preco);
        $preco = str_replace(',', '.', $preco);

        if (!empty($nome) && is_numeric($preco)) {
            $produtoExistente = buscarProdutoPorNomeEFornecedor($nome, $fornecedor_id);

            if ($produtoExistente) {
                $_SESSION['produto_duplicado'] = $produtoExistente;
                $_SESSION['quantidade_temp'] = $quantidade;
                $_SESSION['tentativa_produto'] = compact('nome', 'preco', 'quantidade', 'categoria_id', 'fornecedor_id');
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

        // CONVERTE O PREÇO PARA FORMATO PADRÃO (1234.56)
        $preco = str_replace('.', '', $preco);
        $preco = str_replace(',', '.', $preco);

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

// Listar produtos com estoque crítico
function produtosEstoqueCritico($limite = 5)
{
    global $pdo;
    $sql = "SELECT * FROM produtos WHERE quantidade_estoque <= :limite ORDER BY quantidade_estoque ASC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['limite' => $limite]);
    return $stmt->fetchAll();
}

// =================== CARREGAR DADOS ===================
$total_registros = contarProdutos();
$total_paginas = ceil($total_registros / $registros_por_pagina);

$produtos = listarProdutos($registros_por_pagina, $offset);
$categorias = $pdo->query("SELECT * FROM categorias")->fetchAll();
$fornecedores = $pdo->query("SELECT * FROM fornecedor")->fetchAll();
$estoqueCritico = produtosEstoqueCritico(5); // 5 é o limite mínimo do estoque
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <title>Produtos</title>
    <meta charset="utf-8">
    <link rel="icon" href="../img/CompreFacil.png" type="image/png">
    <link rel="stylesheet" href="../assets/style/animated-gradient.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Adicione as bibliotecas de máscara -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
</head>

<body>
    <nav class="navbar" style="background: rgba(33, 37, 41, 0.85); mb-4;">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="../public/painel.php">
                <img src="../img/CompreFacil.png" alt="Logo do Sistema Compre Fácil" width="48" height="40" class="me-2" style="object-fit:contain;">
                <span class="fw-bold text-white">Compre Fácil</span>
            </a>
            <a href="../public/painel.php" class="btn btn-danger mt-4">Voltar ao painel</a>
        </div>
    </nav>

    <div class="container bg-light p-4 rounded shadow-sm mb-5 mt-5">
        <div class="container mt-5">
            <h1 class="mb-4">Cadastro de Produtos</h1>

            <!-- Alertas de estoque crítico -->
            <?php if (!empty($estoqueCritico)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong>Atenção!</strong> Alguns produtos estão com estoque crítico:
                    <ul class="mb-0">
                        <?php foreach ($estoqueCritico as $ec): ?>
                            <li><?= htmlspecialchars($ec['nome']) ?> - Estoque: <?= $ec['quantidade_estoque'] ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['produto_duplicado'])): ?>
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    O produto <strong><?= htmlspecialchars($_SESSION['produto_duplicado']['nome']) ?></strong> já existe para este fornecedor.<br>
                    Deseja somar <strong><?= $_SESSION['quantidade_temp'] ?></strong> ao estoque atual
                    (<?= $_SESSION['produto_duplicado']['quantidade_estoque'] ?>)?
                    <div class="mt-3 d-flex flex-wrap align-items-center gap-2">
                        <!-- Botão para somar ao estoque -->
                        <form method="POST" class="d-inline">
                            <input type="hidden" name="confirmar_adicao" value="sim">
                            <button type="submit" class="btn btn-success">Sim, somar</button>
                        </form>
                        <!-- Select e botão para cadastrar para outro fornecedor -->
                        <form method="POST" class="d-flex align-items-center gap-2">
                            <button type="submit" class="btn btn-primary">Cadastrar para outro fornecedor:</button>
                            <input type="hidden" name="nome" value="<?= htmlspecialchars($_SESSION['tentativa_produto']['nome']) ?>">
                            <input type="hidden" name="preco" value="<?= htmlspecialchars($_SESSION['tentativa_produto']['preco']) ?>">
                            <input type="hidden" name="quantidade" value="<?= htmlspecialchars($_SESSION['tentativa_produto']['quantidade']) ?>">
                            <input type="hidden" name="categoria_id" value="<?= htmlspecialchars($_SESSION['tentativa_produto']['categoria_id']) ?>">
                            <select name="fornecedor_id" class="form-select w-auto" required>
                                <?php foreach ($fornecedores as $forn): ?>
                                    <option value="<?= $forn['id'] ?>" <?= $forn['id'] == $_SESSION['tentativa_produto']['fornecedor_id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($forn['nome']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </form>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"
                        onclick="window.location='produto.php'"></button>
                </div>
            <?php endif; ?>

            <!-- Formulário de adicionar -->
            <form method="POST" class="border p-4 rounded shadow-sm bg-light mb-5">
                <div class="row g-3">
                    <div class="col-md-3">
                        <input type="text" name="nome" class="form-control" placeholder="Nome do Produto" required>
                    </div>
                    <div class="col-md-2">
                        <input type="text" name="preco" class="form-control preco" placeholder="Preço" required>
                    </div>
                    <div class="col-md-2">
                        <input type="text" name="quantidade" class="form-control quantidade" placeholder="Estoque" required>
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
                <button type="submit" class="btn btn-success mt-3">Adicionar Produto</button>
            </form>

            <h2 class="mb-3">Produtos Cadastrados</h2>
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead class="table-dark">
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
                                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#editarProduto<?= $p['id'] ?>">
                                        Editar
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Paginação -->
            <nav>
                <ul class="pagination justify-content-center">
                    <li class="page-item <?= ($pagina_atual <= 1) ? 'disabled' : '' ?>">
                        <a class="page-link" href="?pagina=<?= $pagina_atual - 1 ?>">Anterior</a>
                    </li>
                    <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                        <li class="page-item <?= ($i == $pagina_atual) ? 'active' : '' ?>">
                            <a class="page-link" href="?pagina=<?= $i ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>
                    <li class="page-item <?= ($pagina_atual >= $total_paginas) ? 'disabled' : '' ?>">
                        <a class="page-link" href="?pagina=<?= $pagina_atual + 1 ?>">Próximo</a>
                    </li>
                </ul>
            </nav>

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
                                    <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            // Máscara para preço: 99999.99 ou 9.999,99 (ajuste conforme seu padrão)
            $('.preco').mask('#.##0,00', {
                reverse: true
            });
            // Máscara para quantidade: apenas números inteiros
            $('.quantidade').mask('000000', {
                reverse: true
            });
        });
    </script>
</body>

</html>