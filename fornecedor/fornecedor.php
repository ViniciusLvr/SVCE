<?php
require_once '../config/conexao.php';

function adicionarFornecedor($pdo, $nome, $cpf, $cnpj, $telefone) {
    $sql = "INSERT INTO fornecedor (nome, cpf, cnpj, telefone) VALUES (:nome, :cpf, :cnpj, :telefone)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':nome' => $nome,
        ':cpf' => $cpf ?: null,
        ':cnpj' => $cnpj ?: null,
        ':telefone' => $telefone
    ]);
}



function excluirFornecedor($pdo, $id) {
    $sql = "DELETE FROM fornecedor WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $id]);
}

function atualizarFornecedor($pdo, $id, $nome, $cpf, $cnpj, $telefone) {
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
    excluirFornecedor($pdo, $excluir_id);
    header("Location: fornecedor.php");
    exit();
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

<nav class="navbar" style="background: rgba(33, 37, 41, 0.85); mb-4;">
  <div class="container">
    <a class="navbar-brand d-flex align-items-center" href="#">
      <img src="../img/CompreFacil.png" alt="Logo do Sistema Compre Fácil" width="48" height="40" class="me-2" style="object-fit:contain;">
      <span class="fw-bold text-white">Compre Fácil</span>
    </a>

     <a href="../public/painel.php" class="btn btn-danger mt-4">Voltar ao painel</a>
  </div>
</nav>

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

        <!-- CPF -->
        <div class="col" id="campoCPF">
            <input type="text" class="form-control" name="cpf" placeholder="CPF" pattern="\d{11}" title="Digite o CPF (11 dígitos)">
        </div>

        <!-- CNPJ -->
        <div class="col" id="campoCNPJ" style="display:none;">
            <input type="text" class="form-control" name="cnpj" placeholder="CNPJ" pattern="\d{14}" title="Digite o CNPJ (14 dígitos)">
        </div>

        <div class="col">
            <input type="text" name="telefone" class="form-control" placeholder="Telefone" pattern="\d{10,11}" title="Digite apenas números (10 ou 11 dígitos)" required>
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
                <!-- Botão Editar (abre modal) -->
                <button type="button" class="btn btn-sm btn-primary"
                        data-bs-toggle="modal"
                        data-bs-target="#editarFornecedor<?= $f['id'] ?>">
                    Editar
                </button>
                <!-- Botão Excluir -->
                <form method="post" style="display:inline;" onsubmit="return confirm('Tem certeza que deseja excluir este fornecedor?');">
                    <input type="hidden" name="excluir_id" value="<?= htmlspecialchars($f['id']) ?>">
                    <button type="submit" class="btn btn-sm btn-danger">Excluir</button>
                </form>
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
            <input type="text" name="nome" class="form-control" value="<?= htmlspecialchars($f['nome']) ?>" required>
          </div>
<?php
// Defina o tipo de documento (CPF ou CNPJ) e o valor do campo
$tipoDocumento = strlen($f['cpf']) === 11 ? 'CPF' : 'CNPJ';
$documento = $f['cpf'] ?: $f['cnpj'];
?>

    <label class="form-label">CPF/CNPJ</label>
    <!-- Campo select para escolher entre CPF ou CNPJ -->
    <select class="form-select" name="tipoDocumento">
        <option value="CPF" <?= $tipoDocumento === 'CPF' ? 'selected' : '' ?>>CPF</option>
        <option value="CNPJ" <?= $tipoDocumento === 'CNPJ' ? 'selected' : '' ?>>CNPJ</option>
    </select>
    
    <div class="mb-3">
    <!-- Aqui estamos colocando o valor correto de CPF ou CNPJ no input -->
    <input type="text" name="cpf_cnpj" class="form-control" value="<?= htmlspecialchars($documento) ?>" required>
</div>



          <div class="mb-3">
            <label class="form-label">Telefone</label>
            <input type="text" name="telefone" class="form-control" value="<?= htmlspecialchars($f['telefone']) ?>" required>
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
    </tbody>
</table>

    <script src="../assets/js/fornecedor-mask.js"></script>
    <script src="../assets/js/masks.js"></script>
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


</body>

</html>
