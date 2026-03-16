<?php
session_start();
require_once '../config/conexao.php';

$mensagem = '';

// Verifica se CPF está salvo na sessão
if (!isset($_SESSION['CPF'])) {
    header('Location: recuperarSenha.php');
    exit;
}

$cpf = $_SESSION['CPF'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['psecreta'])) {
    $corFavorita = trim($_POST['psecreta']);

    // Buscar usuário pelo CPF e verificar se a cor favorita bate
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE CPF = :CPF AND TRIM(LOWER(psecreta)) = TRIM(LOWER(:cor)) LIMIT 1");
    $stmt->execute([':CPF' => $cpf, ':cor' => $corFavorita]);
    $usuario = $stmt->fetch();

    if ($usuario) {
        // Exibe modal para redefinir senha
        $showModal = true;
        $mensagem = "<div class='alert alert-success'>Cor favorita confirmada! Usuário: " . htmlspecialchars($usuario['nome']) . ".</div>";
    } else {
        $mensagem = "<div class='alert alert-danger'>Cor favorita não confere.</div>";
    }
}

// Lógica para atualizar a senha
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nova_senha'])) {
    $novaSenha = $_POST['nova_senha'];
    $confirmarSenha = $_POST['confirmar_senha'];
    if ($novaSenha === $confirmarSenha && strlen($novaSenha) >= 6) {
        $hash = password_hash($novaSenha, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE usuarios SET senha = :senha WHERE CPF = :CPF");
        $stmt->execute([':senha' => $hash, ':CPF' => $cpf]);
        $mensagem = "<div class='alert alert-success'>Senha redefinida com sucesso! <a href='login.php'>Clique aqui para entrar</a></div>";
        $showModal = false;
    } else {
        $mensagem = "<div class='alert alert-danger'>As senhas não conferem ou são muito curtas (mínimo 6 caracteres).</div>";
        $showModal = true;
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Recuperar Conta - Sistema de Vendas</title>
    <link rel="icon" href="../img/CompreFacil.png" type="image/png">
    <link rel="stylesheet" href="../assets/style/animated-gradient.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <nav class="navbar" style="background: rgba(33, 37, 41, 0.85);">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="login.php">
                <img src="../img/CompreFacil.png" alt="Logo do Sistema Compre Fácil" width="48" height="40" class="me-2" style="object-fit:contain;">
                <span class="fw-bold text-white">Compre Fácil</span>
            </a>
        </div>
    </nav>

    <div class="container mt-3" style="max-width: 400px;">
        <div class="card shadow">
            <div class="card-body">
                <h4 class="card-title mb-4 text-center">Verificação de Segurança</h4>

                <?= $mensagem ?>

                <form method="POST">
                    <div class="mb-3">
                        <label for="psecreta" class="form-label">Qual sua cor favorita?</label>
                        <input type="text" name="psecreta" id="psecreta" class="form-control" placeholder="Digite sua cor" required>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">Verificar</button>
                    </div>
                </form>

                <!-- Modal de redefinição de senha -->
                <?php if (!empty($showModal)): ?>
                    <div class="modal fade show" id="modalRedefinirSenha" tabindex="-1" style="display:block; background:rgba(0,0,0,0.5);" aria-modal="true" role="dialog">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form method="POST">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Redefinir Senha</h5>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label for="nova_senha" class="form-label">Nova Senha</label>
                                            <input type="password" name="nova_senha" id="nova_senha" class="form-control" minlength="6" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="confirmar_senha" class="form-label">Confirmar Nova Senha</label>
                                            <input type="password" name="confirmar_senha" id="confirmar_senha" class="form-control" minlength="6" required>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="submit" class="btn btn-success">Salvar Nova Senha</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <script>
                        // Foca no campo de senha ao abrir o modal
                        document.getElementById('nova_senha').focus();
                    </script>
                <?php endif; ?>

                <p class="mt-3 text-center"><a href="login.php">Voltar ao login</a></p>
            </div>
        </div>
    </div>
</body>

</html>