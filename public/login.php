<?php
session_start();
require_once __DIR__ . "/../config/conexao.php";

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $senha = $_POST['senha'];

    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = :email LIMIT 1");
    $stmt->execute([':email' => $email]);
    $usuario = $stmt->fetch();

    if ($usuario && password_verify($senha, $usuario['senha'])) {
        $_SESSION['usuario_logado'] = $usuario['nome']; 
        header('Location: painel.php');
        exit();
    } else {
        $erro = "Usuário ou senha inválidos.";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Login - Sistema de Vendas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <img src="img/logoSVCE.png" alt="logoSVCE" style="height:60px; margin-right:10px;">
    <h1 class="card-title text-center">        
        Sistema de Vendas com Controle de Estoque
    </h1>

    <div class="container mt-5" style="max-width: 400px;">
        <div class="card shadow">
            <div class="card-body">
                <h4 class="card-title mb-4 text-center">Login</h4>

                <?php if (!empty($erro)): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($erro) ?></div>
                <?php endif; ?>

                <form method="POST">
                    <div class="mb-3">
                        <input type="email" name="email" class="form-control" placeholder="E-mail" required>
                    </div>
                    <div class="mb-3">
                        <input type="password" name="senha" class="form-control" placeholder="Senha" required>
                    </div>
                    <div class="d-grid mb-3">
                        <button type="submit" class="btn btn-primary">Entrar</button>
                    </div>
                </form>

                <p class="text-center mb-1">
                    <a href="recuperarSenha.php">Esqueci minha senha</a>
                </p>
                <p class="text-center">
                    <a href="cadastro.php">Não tem conta? Cadastre-se</a>
                </p>
            </div>
        </div>
    </div>
</body>
</html>