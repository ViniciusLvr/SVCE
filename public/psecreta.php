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
        // Aqui você pode liberar o acesso para redefinir a senha
        $mensagem = "<div class='alert alert-success'>Cor favorita confirmada! Usuário: " . htmlspecialchars($usuario['nome']) . ".</div>";

        // Exemplo: redirecionar para página de reset de senha
        // header('Location: redefinirSenha.php');
        // exit;

    } else {
        $mensagem = "<div class='alert alert-danger'>Cor favorita não confere.</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Recuperar Conta - Sistema de Vendas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <img src="../img/CompreFácil.png" alt="logo Compre fácil" style="height:180px; align:center; margin: 0 auto; display: block;">
    <h1 class="card-title text-center mb-4">Sistema de Vendas com Controle de Estoque</h1>

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

                <p class="mt-3 text-center"><a href="login.php">Voltar ao login</a></p>
            </div>
        </div>
    </div>
</body>
</html>