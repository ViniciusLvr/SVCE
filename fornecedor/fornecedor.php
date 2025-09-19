<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../config/conexao.php';

function adicionarFornecedor($pdo, $nome, $cpf, $cnpj, $telefone)
{
    $sql = "INSERT INTO fornecedor (nome, cpf, cnpj, telefone) VALUES (:nome, :cpf, :cnpj, :telefone)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':nome' => $nome,
        ':cpf' => $cpf ?: null,
        ':cnpj' => $cnpj ?: null,
        ':telefone' => $telefone
    ]);
}



function excluirFornecedor($pdo, $id)
{
    try {
        $sql = "DELETE FROM fornecedor WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        return true;
    } catch (PDOException $e) {
        if ($e->getCode() == '23000' && strpos($e->getMessage(), 'a foreign key constraint fails') !== false) {
            echo "<div class='alert alert-danger'>Não é possível excluir o fornecedor pois ele está relacionado a outros registros (ex: produtos).</div>";
        } else {
            echo "<div class='alert alert-danger'>Erro ao excluir fornecedor: " . $e->getMessage() . "</div>";
        }
        return false;
    }
}

function atualizarFornecedor($pdo, $id, $nome, $cpf, $cnpj, $telefone)
{
    $sql = "UPDATE fornecedor SET nome = :nome, cpf = :cpf, cnpj = :cnpj, telefone = :telefone WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':id' => $id,
        ':nome' => $nome,
        ':cpf' => $cpf,
        ':cnpj' => $cnpj,
        ':telefone' => $telefone
    ]);
}


// Inserção
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nome'])) {
    $nome = trim($_POST['nome'] ?? '');
    $cpf = preg_replace('/\D/', '', $_POST['cpf'] ?? '');
    $cnpj = preg_replace('/\D/', '', $_POST['cnpj'] ?? '');
    $telefone = preg_replace('/\D/', '', $_POST['telefone'] ?? '');

    $docValido = (strlen($cpf) === 11 || strlen($cnpj) === 14);
    $telValido = (strlen($telefone) === 10 || strlen($telefone) === 11);

    if (!empty($nome) && $docValido && $telValido) {
        // ajuste a função para receber os campos separados
        adicionarFornecedor($pdo, $nome, $cpf, $cnpj, $telefone);
        header("Location: fornecedor.php");
        exit();
    } else {
        echo "<div class='alert alert-danger text-center'>⚠️ Preencha corretamente: Nome, CPF (11 dígitos) ou CNPJ (14 dígitos) e Telefone (10 ou 11 dígitos).</div>";
    }
}



// Exclusão
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['excluir_id'])) {
    $excluir_id = $_POST['excluir_id'];
    ob_start();
    $ok = excluirFornecedor($pdo, $excluir_id);
    $msg = ob_get_clean();
    if ($ok) {
        header("Location: fornecedor.php");
        exit();
    } else {
        echo $msg;
    }
}

// Edição
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['editar_id'])) {
    $id        = $_POST['editar_id'];
    $nome      = $_POST['nome'] ?? '';
    $documento = preg_replace('/\D/', '', $_POST['cpf_cnpj'] ?? '');
    $telefone  = preg_replace('/\D/', '', $_POST['telefone'] ?? '');
    $tipoDoc   = $_POST['tipoDocumento'] ?? '';

    $cpf = null;
    $cnpj = null;

    if ($tipoDoc === 'CPF' && strlen($documento) === 11) {
        $cpf = $documento;
    } elseif ($tipoDoc === 'CNPJ' && strlen($documento) === 14) {
        $cnpj = $documento;
    }

    if ($id && $nome && ($cpf || $cnpj) && $telefone) {
        atualizarFornecedor($pdo, $id, $nome, $cpf, $cnpj, $telefone);
        header("Location: fornecedor.php");
        exit();
    } else {
        echo "<div class='alert alert-danger'>Todos os campos são obrigatórios para editar.</div>";
    }
}

// Listagem
$registros_por_pagina = 10;
$pagina_atual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
if ($pagina_atual < 1) $pagina_atual = 1;
$offset = ($pagina_atual - 1) * $registros_por_pagina;

// Contar total de fornecedores
$total_fornecedores = $pdo->query("SELECT COUNT(*) FROM fornecedor")->fetchColumn();
$total_paginas = ceil($total_fornecedores / $registros_por_pagina);

// Busca fornecedores paginados
$stmt = $pdo->prepare("SELECT * FROM fornecedor ORDER BY created_at DESC LIMIT :limit OFFSET :offset");
$stmt->bindValue(':limit', (int)$registros_por_pagina, PDO::PARAM_INT);
$stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
$stmt->execute();
$fornecedores = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title>Fornecedores</title>
    <link rel="icon" href="../img/CompreFacil.png" type="image/png">
    <link rel="stylesheet" href="../assets/style/animated-gradient.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <nav class="navbar" style="background: rgba(33, 37, 41, 0.85); margin-bottom: 1.5rem;">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="../public/painel.php">
                <img src="../img/CompreFacil.png" alt="Logo do Sistema Compre Fácil" width="48" height="40" class="me-2"
                    style="object-fit:contain;">
                <span class="fw-bold text-white">Compre Fácil</span>
            </a>
            <a href="../public/painel.php" class="btn btn-danger">Voltar ao painel</a>
        </div>
    </nav>

    <div class="container bg-light p-4 rounded shadow-sm mb-5 mt-5">
        <h1 class="mb-4">Cadastro de Fornecedores</h1>

        <form method="post" class="border p-4 rounded shadow-sm bg-light mb-5">
            <div class="row g-3 align-items-end">
                <div class="col-md-3">
                    <input type="text" name="nome" class="form-control" placeholder="Nome" required>
                </div>
                <div class="col-md-2">
                    <select class="form-select" id="tipoDocumento" name="tipoDocumento">
                        <option value="CPF" selected>CPF</option>
                        <option value="CNPJ">CNPJ</option>
                    </select>
                </div>
                <div class="col-md-2" id="campoCPF">
                    <input type="text" class="form-control cpf" name="cpf" placeholder="CPF"
                        pattern="\d{3}\.\d{3}\.\d{3}-\d{2}">
                </div>
                <div class="col-md-2" id="campoCNPJ" style="display:none;">
                    <input type="text" class="form-control cnpj" name="cnpj" placeholder="CNPJ"
                        pattern="\d{2}\.\d{3}\.\d{3}/\d{4}-\d{2}">
                </div>
                <div class="col-md-3">
                    <input type="text" name="telefone" class="form-control telefone" placeholder="Telefone"
                        pattern="\(\d{2}\)\s\d{4,5}-\d{4}" title="Digite apenas números (10 ou 11 dígitos)" required>
                </div>
                <div class="col-md-12 mt-3">
                    <button type="submit" class="btn btn-success">Cadastrar</button>
                </div>
            </div>
        </form>

        <h2 class="mb-3">Fornecedores Cadastrados</h2>
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>CPF/CNPJ</th>
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
                        <td><?= htmlspecialchars($f['cpf'] ?: $f['cnpj']) ?></td>
                        <td><?= htmlspecialchars($f['telefone']) ?></td>
                        <td><?= htmlspecialchars($f['created_at']) ?></td>
                        <td>
                            <div class="d-flex gap-2">
                                <form method="post" style="display:inline;"
                                    onsubmit="return confirm('Tem certeza que deseja excluir este fornecedor?');">
                                    <input type="hidden" name="excluir_id" value="<?= htmlspecialchars($f['id']) ?>">
                                    <button type="submit" class="btn btn-sm btn-danger">Excluir</button>
                                </form>
                                <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                    data-bs-target="#editarFornecedor<?= $f['id'] ?>">
                                    Editar
                                </button>
                            </div>
                        </td>
                    </tr>

                    <!-- Modal para Edição -->
                    <div class="modal fade" id="editarFornecedor<?= $f['id'] ?>" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form method="post">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Editar Fornecedor</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <input type="hidden" name="editar_id" value="<?= $f['id'] ?>">
                                        <div class="mb-3">
                                            <label class="form-label">Nome</label>
                                            <input type="text" name="nome" class="form-control"
                                                value="<?= htmlspecialchars($f['nome']) ?>" required>
                                        </div>
                                        <?php
                                            $cpf = $f['cpf'] ?? '';
                                            $cnpj = $f['cnpj'] ?? '';

                                            if (!empty($cpf) && strlen($cpf) === 11) {
                                                $tipoDocumento = 'CPF';
                                                $documento = $cpf;
                                            } elseif (!empty($cnpj)) {
                                                $tipoDocumento = 'CNPJ';
                                                $documento = $cnpj;
                                            } else {
                                                $tipoDocumento = 'Não informado';
                                                $documento = '';
                                            }
                                            ?>

                                        <label class="form-label">CPF/CNPJ</label>
                                        <div class="row g-2 align-items-center mb-3">
                                            <div class="col-auto">
                                                <select class="form-select" name="tipoDocumento">
                                                    <option value="CPF"
                                                        <?= $tipoDocumento === 'CPF' ? 'selected' : '' ?>>CPF</option>
                                                    <option value="CNPJ"
                                                        <?= $tipoDocumento === 'CNPJ' ? 'selected' : '' ?>>CNPJ</option>
                                                </select>
                                            </div>
                                            <div class="col">
                                                <input type="text" name="cpf_cnpj" class="form-control"
                                                    value="<?= htmlspecialchars($documento) ?>" required>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Telefone</label>
                                            <input type="text" name="telefone" class="form-control telefone"
                                                value="<?= htmlspecialchars($f['telefone']) ?>" required>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                                        <button type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">Cancelar</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <!-- Paginação -->
            <nav>
                <ul class="pagination justify-content-center mt-4">
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
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const tipoDocumento = document.getElementById('tipoDocumento');
        const campoCPF = document.getElementById('campoCPF');
        const campoCNPJ = document.getElementById('campoCNPJ');
        const inputCPF = campoCPF.querySelector('input');
        const inputCNPJ = campoCNPJ.querySelector('input');

        function alternarCampos() {
            if (tipoDocumento.value === 'CPF') {
                campoCPF.style.display = '';
                campoCNPJ.style.display = 'none';
                inputCPF.required = true;
                inputCNPJ.required = false;
                inputCNPJ.value = '';
            } else {
                campoCPF.style.display = 'none';
                campoCNPJ.style.display = '';
                inputCPF.required = false;
                inputCNPJ.required = true;
                inputCPF.value = '';
            }
        }

        tipoDocumento.addEventListener('change', alternarCampos);
        alternarCampos();
    });
    </script>
    <script>
    $(document).ready(function() {
        // Máscara para telefone (celular ou fixo)
        $('.telefone').mask(function(val) {
            return val.replace(/\D/g, '').length === 11 ? '(00) 00000-0000' : '(00) 0000-00009';
        }, {
            onKeyPress: function(val, e, field, options) {
                field.mask(val.replace(/\D/g, '').length === 11 ? '(00) 00000-0000' :
                    '(00) 0000-00009', options);
            }
        });

        // Máscara dinâmica para CPF/CNPJ no cadastro
        $('#tipoDocumento').on('change', function() {
            if ($(this).val() === 'CPF') {
                $('.cpf').mask('000.000.000-00');
                $('.cpf').val('');
                $('.cnpj').val('');
            } else {
                $('.cnpj').mask('00.000.000/0000-00');
                $('.cnpj').val('');
                $('.cpf').val('');
            }
        }).trigger('change');

        // Máscara dinâmica nos modais de edição
        $('select[name="tipoDocumento"]').on('change', function() {
            var $input = $(this).closest('.modal-body').find('input[name="cpf_cnpj"]');
            if ($(this).val() === 'CPF') {
                $input.mask('000.000.000-00');
            } else {
                $input.mask('00.000.000/0000-00');
            }
        }).trigger('change');
    });
    </script>
</body>

</html>