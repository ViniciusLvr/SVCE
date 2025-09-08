<?php
require_once '../config/conexao.php';
require_once  '../config/auth.php';

function adicionarCliente($pdo, $nome, $cpf_cnpj, $telefone, $endereco) {
    $sql = "INSERT INTO clientes (nome, cpf_cnpj, telefone, endereco) VALUES (:nome, :cpf_cnpj, :telefone, :endereco)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':nome' => $nome,
        ':cpf_cnpj' => $cpf_cnpj,
        ':telefone' => $telefone,
        ':endereco' => $endereco
    ]);
}

function excluirCliente($pdo, $id) {
    $sql = "DELETE FROM clientes WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $id]);
}

function atualizarCliente($pdo, $id, $nome, $cpf_cnpj, $telefone, $endereco) {
    $sql = "UPDATE clientes 
            SET nome = :nome, cpf_cnpj = :cpf_cnpj, telefone = :telefone, endereco = :endereco 
            WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':id' => $id,
        ':nome' => $nome,
        ':cpf_cnpj' => $cpf_cnpj,
        ':telefone' => $telefone,
        ':endereco' => $endereco
    ]);
}

// Verifica envio do formulário de cadastro
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['editar_id']) && !isset($_POST['excluir_id'])) {
    $id = $_POST['editar_id'];
    $nome = $_POST['nome'] ?? '';
    $cpf_cnpj = $_POST['cpf_cnpj'] ?? '';
    $telefone = $_POST['telefone'] ?? '';
    $endereco = $_POST['endereco'] ?? '';

    if ($id && $nome && $cpf_cnpj && $telefone && $endereco) {
        atualizarCliente($pdo, $id, $nome, $cpf_cnpj, $telefone, $endereco);
        header("Location: clientes.php");
        exit();
    } else {
        echo "<div class='alert alert-danger'>Todos os campos são obrigatórios para editar.</div>";
    }
}

// Verifica envio do formulário de exclusão
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['excluir_id'])) {
    $excluir_id = $_POST['excluir_id'];
    excluirCliente($pdo, $excluir_id);
    header("Location: clientes.php");
    exit();
}

// Busca clientes
$stmt = $pdo->query("SELECT * FROM clientes ORDER BY created_at DESC");
$clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title>Clientes</title>
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
            <input type="text" name="cpf_cnpj" class="form-control cpf" placeholder="CPF" pattern="\d{11}" title="Digite o CPF (11 dígitos)" required>
        </div>
        <div class="col" id="campoCNPJ" style="display:none;">
            <input type="text" name="cpf_cnpj" class="form-control cnpj" placeholder="CNPJ" pattern="\d{14}" title="Digite o CNPJ (14 dígitos)">
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
                        <td><?= htmlspecialchars($cliente['cpf_cnpj']) ?></td>
                        <td><?= htmlspecialchars($cliente['telefone']) ?></td>
                        <td><?= htmlspecialchars($cliente['endereco']) ?></td>
                        <td><?= htmlspecialchars($cliente['created_at']) ?></td>
                        <td>
                            <!-- Botão Editar (abre modal) -->
                            <button type="button" class="btn btn-sm btn-primary"
                                    data-bs-toggle="modal"
                                    data-bs-target="#editarCliente<?= $cliente['id'] ?>">
                                Editar
                            </button>

                            <!-- Botão Excluir -->
                            <form method="post"
                                onsubmit="return confirm('Tem certeza que deseja excluir este cliente?');">
                                <input type="hidden" name="excluir_id" value="<?= htmlspecialchars($cliente['id']) ?>">
                                <button type="submit" class="btn btn-sm btn-danger">Excluir</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <?php foreach ($clientes as $cliente): ?>
<div class="modal fade" id="editarCliente<?= $cliente['id'] ?>" tabindex="-1" aria-hidden="true">
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
                   value="<?= htmlspecialchars($cliente['cpf_cnpj']) ?>" >
          </div>
          <div class="mb-3">
            <label class="form-label">Telefone</label>
            <input type="text" name="telefone" class="form-control" 
                   value="<?= htmlspecialchars($cliente['telefone']) ?>" >
          </div>
          <div class="mb-3">
            <label class="form-label">Endereço</label>
            <input type="text" name="endereco" class="form-control" 
                   value="<?= htmlspecialchars($cliente['endereco']); ?>" >
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
    <script src="../assets/js/cliente-mask.js"></script>
    <script src="../assets/js/masks.js"></script>
</body>

</html>
