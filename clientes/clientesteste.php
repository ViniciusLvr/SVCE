<?php
require_once '../config/conexao.php';
require_once  '../config/auth.php';

// Funções para manipulação de clientes no banco
function adicionarCliente($pdo, $nome, $telefone, $endereco, $cpf, $cnpj, $tipoDocumento) {
    if ($tipoDocumento == 'CPF') {
        $sql = "INSERT INTO clientes (nome, telefone, endereco, cpf) VALUES (:nome, :telefone, :endereco, :cpf)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':nome' => $nome,
            ':telefone' => $telefone,
            ':endereco' => $endereco,
            ':cpf' => $cpf,
        ]);
    } elseif ($tipoDocumento == 'CNPJ') {
        $sql = "INSERT INTO clientes (nome, telefone, endereco, cnpj) VALUES (:nome, :telefone, :endereco, :cnpj)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':nome' => $nome,
            ':telefone' => $telefone,
            ':endereco' => $endereco,
            ':cnpj' => $cnpj,
        ]);
    }
}

function excluirCliente($pdo, $id) {
    $sql = "DELETE FROM clientes WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $id]);
}

function atualizarCliente($pdo, $id, $nome, $telefone, $endereco, $cpf, $cnpj, $tipoDocumento) {
    if ($tipoDocumento == 'CPF') {
        $sql = "UPDATE clientes 
                SET nome = :nome, telefone = :telefone, endereco = :endereco, cpf = :cpf
                WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':id' => $id,
            ':nome' => $nome,
            ':telefone' => $telefone,
            ':endereco' => $endereco,
            ':cpf' => $cpf,
        ]);
    } elseif ($tipoDocumento == 'CNPJ') {
        $sql = "UPDATE clientes 
                SET nome = :nome, telefone = :telefone, endereco = :endereco, cnpj = :cnpj
                WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':id' => $id,
            ':nome' => $nome,
            ':telefone' => $telefone,
            ':endereco' => $endereco,
            ':cnpj' => $cnpj,
        ]);
    }
}

// Função de validação do CNPJ
function validar_cnpj($cnpj) {
    $cnpj = preg_replace('/[^0-9]/', '', $cnpj);
    if (strlen($cnpj) != 14) return false;
    if (preg_match('/(\d)\1{13}/', $cnpj)) return false;

    $soma = 0;
    $multiplicador1 = [5,4,3,2,9,8,7,6,5,4,3,2];
    for ($i = 0; $i < 12; $i++) {
        $soma += $cnpj[$i] * $multiplicador1[$i];
    }
    $resto = $soma % 11;
    $digito1 = ($resto < 2) ? 0 : 11 - $resto;
    if ($cnpj[12] != $digito1) return false;

    $soma = 0;
    $multiplicador2 = [6,5,4,3,2,9,8,7,6,5,4,3,2];
    for ($i = 0; $i < 13; $i++) {
        $soma += $cnpj[$i] * $multiplicador2[$i];
    }
    $resto = $soma % 11;
    $digito2 = ($resto < 2) ? 0 : 11 - $resto;
    return $cnpj[13] == $digito2;
}

// Captura dados do formulário
$cpf = '';
$cnpj = '';
$tipoDocumento = $_POST['tipoDocumento'] ?? '';
$documento = preg_replace('/[^0-9]/', '', $_POST['cpf_cnpj'] ?? '');

// Valida o CPF ou CNPJ dependendo da seleção
if ($tipoDocumento === 'CPF') {
    if (strlen($documento) !== 11) {
        echo "<div class='alert alert-danger'>CPF inválido.</div>";
        exit;
    }
    $cpf = $documento;
} elseif ($tipoDocumento === 'CNPJ') {
    if (!validar_cnpj($documento)) {
        echo "<div class='alert alert-danger'>CNPJ inválido.</div>";
        exit;
    }
    $cnpj = $documento;
}

// Verifica envio do formulário de cadastro ou edição
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['editar_id']) && !isset($_POST['excluir_id'])) {
        $id = $_POST['editar_id'];
        $nome = $_POST['nome'] ?? '';
        $telefone = $_POST['telefone'] ?? '';
        $endereco = $_POST['endereco'] ?? '';
        $cpf = $_POST['cpf_cnpj'] ?? '';
        $cnpj = $_POST['cpf_cnpj'] ?? '';
        $tipoDocumento = $_POST['tipoDocumento'] ?? '';

        if ($id && $nome && $telefone && $endereco && $tipoDocumento && ($cpf || $cnpj)) {
            atualizarCliente($pdo, $id, $nome, $telefone, $endereco, $cpf, $cnpj, $tipoDocumento);
            header("Location: clientes.php");
            exit();
        } else {
            echo "<div class='alert alert-danger'>Todos os campos são obrigatórios para editar.</div>";
        }
    } elseif (isset($_POST['excluir_id'])) {
        $excluir_id = $_POST['excluir_id'];
        excluirCliente($pdo, $excluir_id);
        header("Location: clientes.php");
        exit();
    }
}

// Busca os clientes cadastrados
$stmt = $pdo->query("SELECT * FROM clientes ORDER BY created_at DESC");
$clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title>Cadastro de Clientes</title>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- jQuery Mask Plugin -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <h1 class="mb-4">Cadastro de Clientes</h1>

        <form method="post" class="border p-4 rounded shadow-sm bg-light mb-5">
            <div class="row mb-3">
                <div class="col">
                    <input type="text" name="nome" class="form-control" placeholder="Nome" required>
                </div>
                <div class="col">
                    <select class="form-select" id="tipoDocumento" name="tipoDocumento">
                        <option value="CPF" selected>CPF</option>
                        <option value="CNPJ">CNPJ</option>
                    </select>
                </div>
                <div class="col" id="campoCPF">
                    <input type="text" name="cpf_cnpj" class="form-control cpf" placeholder="CPF" required>
                </div>
                <div class="col" id="campoCNPJ" style="display:none;">
                    <input type="text" name="cpf_cnpj" class="form-control cnpj" placeholder="CNPJ" required>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col">
                    <input type="text" name="telefone" class="form-control telefone" placeholder="Telefone" required>
                </div>
                <div class="col">
                    <input type="text" name="endereco" class="form-control" placeholder="Endereço" required>
                </div>
            </div>
            <button type="submit" class="btn btn-success">Cadastrar</button>
        </form>

        <h2 class="mb-3">Clientes Cadastrados</h2>
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>CPF/CNPJ</th>
                        <th>Telefone</th>
                        <th>Endereço</th>
                        <th>Cadastro</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($clientes as $cliente): ?>
                    <tr>
                        <td><?= htmlspecialchars($cliente['id']) ?></td>
                        <td><?= htmlspecialchars($cliente['nome']) ?></td>
                        <td>
                            <?php 
                                if (!empty($cliente['cpf'])) {
                                    echo "CPF: " . htmlspecialchars($cliente['cpf']);
                                } elseif (!empty($cliente['cnpj'])) {
                                    echo "CNPJ: " . htmlspecialchars($cliente['cnpj']);
                                } else {
                                    echo "N/A";
                                }
                            ?>
                        </td>
                        <td><?= htmlspecialchars($cliente['telefone']) ?></td>
                        <td><?= htmlspecialchars($cliente['endereco']) ?></td>
                        <td><?= htmlspecialchars($cliente['created_at']) ?></td>
                        <td>
                            <!-- Botão Editar (abre modal) -->
                            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#editarCliente<?= $cliente['id'] ?>">Editar</button>

                            <!-- Botão Excluir -->
                            <form method="post" onsubmit="return confirm('Tem certeza que deseja excluir este cliente?');">
                                <input type="hidden" name="excluir_id" value="<?= htmlspecialchars($cliente['id']) ?>">
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/cliente-mask.js"></script>
</body>

</html>
