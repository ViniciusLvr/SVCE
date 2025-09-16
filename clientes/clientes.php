<?php
require_once '../config/conexao.php';
require_once  '../config/auth.php';

function adicionarCliente($pdo, $nome, $telefone, $cpf, $cnpj, $tipoDocumento, $enderecoData)
{
    try {
        $pdo->beginTransaction();

        // Insere cliente
        if ($tipoDocumento == 'CPF') {
            $sql = "INSERT INTO clientes (nome, telefone, cpf) VALUES (:nome, :telefone, :cpf)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':nome' => $nome,
                ':telefone' => $telefone,
                ':cpf' => $cpf,
            ]);
        } else {
            $sql = "INSERT INTO clientes (nome, telefone, cnpj) VALUES (:nome, :telefone, :cnpj)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':nome' => $nome,
                ':telefone' => $telefone,
                ':cnpj' => $cnpj,
            ]);
        }

        $clienteId = $pdo->lastInsertId();

        // Insere endereço
        $sql = "INSERT INTO enderecos (cliente_id, cep, logradouro, numero, complemento, bairro, cidade, estado) 
                VALUES (:cliente_id, :cep, :logradouro, :numero, :complemento, :bairro, :cidade, :estado)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':cliente_id' => $clienteId,
            ':cep' => preg_replace('/\D/', '', $enderecoData['cep']),
            ':logradouro' => $enderecoData['logradouro'],
            ':numero' => $enderecoData['numero'],
            ':complemento' => $enderecoData['complemento'],
            ':bairro' => $enderecoData['bairro'],
            ':cidade' => $enderecoData['cidade'],
            ':estado' => $enderecoData['estado']
        ]);

        $pdo->commit();
        return true;

    } catch (PDOException $e) {
        $pdo->rollBack();
        echo "<div class='alert alert-danger'>Erro ao cadastrar cliente: " . $e->getMessage() . "</div>";
        return false;
    }
}


function excluirCliente($pdo, $id)
{
    try {
        $sql = "DELETE FROM clientes WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        return true;
    } catch (PDOException $e) {
        if ($e->getCode() == '23000' && strpos($e->getMessage(), 'a foreign key constraint fails') !== false) {
            echo "<div class='alert alert-danger'>Não é possível excluir o cliente pois ele está relacionado a outros registros (ex: vendas).</div>";
        } else {
            echo "<div class='alert alert-danger'>Erro ao excluir cliente: ".$e->getMessage()."</div>";
        }
        return false;
    }
}

function atualizarCliente($pdo, $id, $nome, $telefone, $endereco, $cpf, $cnpj, $tipoDocumento)
{
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

// Verifica envio do formulário de cadastro
if ($_SERVER["REQUEST_METHOD"] == "POST" && !isset($_POST['editar_id']) && !isset($_POST['excluir_id'])) {
    $tipoDocumento = $_POST['tipoDocumento'] ?? '';
    $nome = $_POST['nome'] ?? '';
    $telefone = preg_replace('/\D/', '', $_POST['telefone'] ?? '');

    $cpf = preg_replace('/\D/', '', $_POST['cpf'] ?? '');
    $cnpj = preg_replace('/\D/', '', $_POST['cnpj'] ?? '');

    $documentoValido = false;
    $valorDocumento = '';
    if ($tipoDocumento === 'CPF') {
        $valorDocumento = $cpf;
        $documentoValido = strlen($cpf) === 11;
    } else if ($tipoDocumento === 'CNPJ') {
        $valorDocumento = $cnpj;
        $documentoValido = strlen($cnpj) === 14;
    }

    $endereco = [
        'cep' => $_POST['cep'] ?? '',
        'logradouro' => $_POST['logradouro'] ?? '',
        'numero' => $_POST['numero'] ?? '',
        'complemento' => $_POST['complemento'] ?? '',
        'bairro' => $_POST['bairro'] ?? '',
        'cidade' => $_POST['cidade'] ?? '',
        'estado' => $_POST['estado'] ?? ''
    ];

    if ($nome && $telefone && $documentoValido && $tipoDocumento && $endereco['cep']) {
        if (adicionarCliente($pdo, $nome, $telefone, $cpf, $cnpj, $tipoDocumento, $endereco)) {
            header("Location: clientes.php");
            exit();
        }
    } else {
        echo "<div class='alert alert-danger'>Todos os campos são obrigatórios.</div>";
    }
}

// Verifica envio do formulário de edição
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['editar_id']) && !isset($_POST['excluir_id'])) {
    $id = $_POST['editar_id'];

    // Busca os dados atuais do cliente
    $stmt = $pdo->prepare("SELECT * FROM clientes WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $clienteExistente = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$clienteExistente) {
        echo "<div class='alert alert-danger'>Cliente não encontrado.</div>";
        exit();
    }

    // Se novos dados foram enviados, usa eles; senão, usa os antigos
    $nome = trim($_POST['nome'] ?? '') ?: $clienteExistente['nome'];
    $telefone = trim($_POST['telefone'] ?? '') ?: $clienteExistente['telefone'];
    $endereco = trim($_POST['endereco'] ?? '') ?: $clienteExistente['endereco'];
    $cpf_cnpj = trim($_POST['cpf_cnpj'] ?? '');

    // Descobre se é CPF ou CNPJ com base no que já está salvo
    $tipoDocumento = !empty($clienteExistente['cpf']) ? 'CPF' : 'CNPJ';

    if ($tipoDocumento === 'CPF') {
        $cpf = $cpf_cnpj ?: $clienteExistente['cpf'];
        $cnpj = '';
    } else {
        $cnpj = $cpf_cnpj ?: $clienteExistente['cnpj'];
        $cpf = '';
    }

    atualizarCliente($pdo, $id, $nome, $telefone, $endereco, $cpf, $cnpj, $tipoDocumento);
    header("Location: clientes.php");
    exit();
}



// Verifica envio do formulário de exclusão
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['excluir_id'])) {
    $excluir_id = $_POST['excluir_id'];
    ob_start();
    $ok = excluirCliente($pdo, $excluir_id);
    $msg = ob_get_clean();
    if ($ok) {
        header("Location: clientes.php");
        exit();
    } else {
        echo $msg;
    }
}

// Busca clientes
$stmt = $pdo->query("SELECT * FROM clientes ORDER BY created_at DESC");
$clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>


<!-- HTML -->

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title>Clientes</title>
    <link rel="icon" href="../img/CompreFacil.png" type="image/png">
    <link rel="stylesheet" href="../assets/style/animated-gradient.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- jQuery e jQuery Mask -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
</head>

<body>
    <nav class="navbar" style="background: rgba(33, 37, 41, 0.85); mb-4;">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="../public/painel.php">
                <img src="../img/CompreFacil.png" alt="Logo do Sistema Compre Fácil" width="48" height="40" class="me-2"
                    style="object-fit:contain;">
                <span class="fw-bold text-white">Compre Fácil</span>
            </a>
            <a href="../public/painel.php" class="btn btn-danger mt-4">Voltar ao painel</a>
        </div>
    </nav>

    <div class="container bg-light p-4 rounded shadow-sm mb-5 mt-5">
        <div class="container mt-5">
            <h1 class="mb-4">Cadastro de Clientes</h1>

            <form method="post" class="border p-4 rounded shadow-sm bg-light mb-5">
                <div class="row mb-3">
                    <div class="col">
                        <input type="text" name="nome" class="form-control" placeholder="Nome" pattern="[A-Za-zÀ-ÿ\s]+"
                            required>
                    </div>
                    <div class="col">
                        <select class="form-select" id="tipoDocumento" name="tipoDocumento">
                            <option value="CPF" selected>CPF</option>
                            <option value="CNPJ">CNPJ</option>
                        </select>
                    </div>
                    <div class="col" id="campoCPF">
                        <input type="text" name="cpf" class="form-control cpf" placeholder="CPF"
                            pattern="\d{3}\.\d{3}\.\d{3}-\d{2}" title="Formato: 000.000.000-00" required>
                    </div>
                    <div class="col" id="campoCNPJ" style="display:none;">
                        <input type="text" name="cnpj" class="form-control cnpj" placeholder="CNPJ"
                            pattern="\d{2}\.\d{3}\.\d{3}/\d{4}-\d{2}" title="Formato: 00.000.000/0000-00" disabled>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col">
                        <input type="text" name="telefone" class="form-control telefone" placeholder="Telefone"
                            pattern="\(\d{2}\)\s\d{4,5}-\d{4}" title="Formato: (99) 99999-9999 ou (99) 9999-9999"
                            required>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <input type="text" id="cep" name="cep" class="form-control" placeholder="CEP" required>
                        </div>
                        <div class="col-md-6">
                            <input type="text" id="logradouro" name="logradouro" class="form-control" placeholder="Rua"
                                required>
                        </div>
                        <div class="col-md-3">
                            <input type="text" id="numero" name="numero" class="form-control" placeholder="Número"
                                required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <input type="text" id="bairro" name="bairro" class="form-control" placeholder="Bairro"
                                required>
                        </div>
                        <div class="col-md-5">
                            <input type="text" id="cidade" name="cidade" class="form-control" placeholder="Cidade"
                                required>
                        </div>
                        <div class="col-md-2">
                            <input type="text" id="estado" name="estado" class="form-control" placeholder="UF" required>
                        </div>
                        <div class="col-md-2">
                            <input type="text" id="complemento" name="complemento" class="form-control"
                                placeholder="Complemento">
                        </div>
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
                                <!-- Botão Excluir -->
                                <form method="post" class="d-inline"
                                    onsubmit="return confirm('Tem certeza que deseja excluir este cliente?');">
                                    <input type="hidden" name="excluir_id"
                                        value="<?= htmlspecialchars($cliente['id']) ?>">
                                    <button type="submit" class="btn btn-sm btn-danger">Excluir</button>

                                    <!-- Botão Editar (abre modal) -->
                                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                        data-bs-target="#editarCliente<?= $cliente['id'] ?>">
                                        Editar
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <!-- Modais de edição -->
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
                                        <input type="text" name="nome" class="form-control" pattern="[A-Za-zÀ-ÿ\s]+"
                                            value="<?= htmlspecialchars($cliente['nome']) ?>">
                                    </div>
                                    <?php
                                            $tipoDoc = !empty($cliente['cpf']) ? 'CPF' : 'CNPJ';
                                            $cpf = $cliente['cpf'] ?? '';
                                            $cnpj = $cliente['cnpj'] ?? '';
                                        ?>
                                    <div class="mb-3">
                                        <label class="form-label">Tipo de Documento</label>
                                        <select class="form-select mb-2 tipoDocumentoEditar" name="tipoDocumento">
                                            <option value="CPF" <?= $tipoDoc === 'CPF' ? 'selected' : '' ?>>CPF</option>
                                            <option value="CNPJ" <?= $tipoDoc === 'CNPJ' ? 'selected' : '' ?>>CNPJ
                                            </option>
                                        </select>
                                        <div id="campoCPFEditar<?= $cliente['id'] ?>" class="mb-2"
                                            style="display:<?= $tipoDoc === 'CPF' ? 'block' : 'none' ?>;">
                                            <input type="text" name="cpf" class="form-control cpfEditar"
                                                placeholder="CPF" value="<?= htmlspecialchars($cpf) ?>">
                                        </div>
                                        <div id="campoCNPJEditar<?= $cliente['id'] ?>" class="mb-2"
                                            style="display:<?= $tipoDoc === 'CNPJ' ? 'block' : 'none' ?>;">
                                            <input type="text" name="cnpj" class="form-control cnpjEditar"
                                                placeholder="CNPJ" value="<?= htmlspecialchars($cnpj) ?>">
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Telefone</label>
                                        <input type="text" name="telefone" class="form-control"
                                            value="<?= htmlspecialchars($cliente['telefone']) ?>">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Endereço</label>
                                        <input type="text" name="endereco" class="form-control"
                                            value="<?= htmlspecialchars($cliente['endereco']); ?>">
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

            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/cliente-mask.js"></script>
    <script src="../assets/js/masks.js"></script>
    <script>
    // Alterna campos CPF/CNPJ no formulário de cadastro
    $(document).ready(function() {
        function alternarCamposDocumento() {
            if ($('#tipoDocumento').val() === 'CPF') {
                $('#campoCPF').show().find('input').prop('required', true).prop('disabled', false);
                $('#campoCNPJ').hide().find('input').prop('required', false).prop('disabled', true).val('');
            } else {
                $('#campoCPF').hide().find('input').prop('required', false).prop('disabled', true).val('');
                $('#campoCNPJ').show().find('input').prop('required', true).prop('disabled', false);
            }
        }
        $('#tipoDocumento').change(alternarCamposDocumento);
        alternarCamposDocumento();

        // Máscara para telefone (fixo e celular)
        $('.telefone').mask(function(val) {
            return val.replace(/\D/g, '').length === 11 ? '(00) 00000-0000' : '(00) 0000-00009';
        }, {
            onKeyPress: function(val, e, field, options) {
                var masks = ['(00) 0000-00009', '(00) 00000-0000'];
                var mask = (val.replace(/\D/g, '').length === 11) ? masks[1] : masks[0];
                $('.telefone').mask(mask, options);
            }
        });

        // --- MODAL DE EDIÇÃO CLIENTE ---
        $('.tipoDocumentoEditar').each(function() {
            var select = $(this);
            var modalId = select.closest('.modal').attr('id').replace('editarCliente', '');
            var campoCPF = $('#campoCPFEditar' + modalId);
            var campoCNPJ = $('#campoCNPJEditar' + modalId);
            var inputCPF = campoCPF.find('input');
            var inputCNPJ = campoCNPJ.find('input');

            function alternarCamposModal() {
                if (select.val() === 'CPF') {
                    campoCPF.show();
                    campoCNPJ.hide();
                    inputCPF.prop('required', true).prop('disabled', false);
                    inputCNPJ.prop('required', false).prop('disabled', true).val('');
                    inputCPF.mask('000.000.000-00');
                } else {
                    campoCPF.hide();
                    campoCNPJ.show();
                    inputCPF.prop('required', false).prop('disabled', true).val('');
                    inputCNPJ.prop('required', true).prop('disabled', false);
                    inputCNPJ.mask('00.000.000/0000-00');
                }
            }
            select.change(alternarCamposModal);
            alternarCamposModal();
        });
    });
    </script>

    <!-- Script para API ViaCEP -->

    <script>
    $(document).ready(function() {
        $('#cep').mask('00000-000');

        $('#cep').on('blur', function() {
            let cep = $(this).val().replace(/\D/g, '');

            if (cep.length !== 8) {
                alert('CEP inválido');
                return;
            }

            $.getJSON(`https://viacep.com.br/ws/${cep}/json/`, function(data) {
                if (!data.erro) {
                    $('#logradouro').val(data.logradouro);
                    $('#bairro').val(data.bairro);
                    $('#cidade').val(data.localidade);
                    $('#estado').val(data.uf);
                    $('#complemento').val(data.complemento);
                } else {
                    alert("CEP não encontrado.");
                }
            }).fail(function() {
                alert("Erro ao consultar o CEP.");
            });
        });
    });
    </script>

</body>

</html>