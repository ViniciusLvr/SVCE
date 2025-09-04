<?php
session_start();
require_once '../config/conexao.php';

$mensagem = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cpf'])) {
    $cpf = trim($_POST['cpf']);

    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE cpf = :cpf LIMIT 1");
    $stmt->execute([':cpf' => $cpf]);
    $usuario = $stmt->fetch();

    if ($usuario) {
        $mensagem = "<div class='alert alert-success'>Usuário encontrado: " . htmlspecialchars($usuario['nome']) . ".</div>";
        

        // Aqui você pode implementar a lógica de reset de senha
    } else {
        $mensagem = "<div class='alert alert-danger'>CPF não encontrado no sistema.</div>";
    }
}



?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Recuperar Senha - Sistema de Vendas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <h1 class="card-title text-center">
        <img src="img/logoSVCE.png" alt="logoSVCE" style="height:60px; margin-right:10px;">
        Sistema de Vendas com Controle de Estoque
    </h1>

    <div class="container mt-5" style="max-width: 400px;">
        <div class="card shadow">
            <div class="card-body">
                <h4 class="card-title mb-4 text-center">Recuperar Senha</h4>

                <?= $mensagem ?>

                <form method="POST">
                    <div class="mb-3">
                        <input type="text" name="CPF" class="form-control" placeholder="Digite seu CPF" required>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">Recuperar senha</button>
                    </div>
                </form>

                <p class="mt-3 text-center"><a href="login.php">Voltar ao login</a></p>
            </div>
        </div>
    </div>
</body>
</html>
