<?php
require_once '../config/conexao.php';

$erro = '';
$sucesso = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = trim($_POST['nome']);
    $email = trim($_POST['email']);
    $senha = $_POST['senha'];

    if ($nome && $email && $senha) {
        // Verifica se o e-mail já está cadastrado
        if (strlen($senha) < 8 || !preg_match('/[\W]/', $senha)) {
        $erro = "A senha deve ter no mínimo 8 caracteres e incluir pelo menos 1 caractere especial.";
        }else{
            // Verifica se o e-mail já está cadastrado
            $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = :email");
            $stmt->execute([
                           ':email' => $email,
                           ':nome' => $nome
                           ]);

            if ($stmt->rowCount() > 0) {
                $erro = "Usuário já cadastrado (e-mail ou nome em uso).";
            } else {
                // Criptografar a senha
                $senha_hash = password_hash($senha, PASSWORD_DEFAULT);

                // Inserir no banco
                $stmt = $pdo->prepare("INSERT INTO usuarios (nome, email, senha) VALUES (:nome, :email, :senha)");
                $stmt->execute([
                    ':nome' => $nome,
                    ':email' => $email,
                    ':senha' => $senha_hash
                ]);

                $sucesso = "Cadastro realizado com sucesso! <a href='login.php' class='alert-link'>Clique aqui para entrar</a>";
                try {
                    $stmt = $pdo->prepare("INSERT INTO usuarios (nome, email, senha) VALUES (:nome, :email, :senha)");
                    $stmt->execute([
                        ':nome' => $nome,
                        ':email' => $email,
                        ':senha' => $senha_hash
                    ]);

                    $sucesso = "Cadastro realizado com sucesso! <a href='login.php' class='alert-link'>Clique aqui para entrar</a>";
                } catch (PDOException $e) {
                    if ($e->getCode() == 23000) { 
                        $erro = "E-mail já cadastrado.";
                    } else {
                        $erro = "Erro inesperado: " . $e->getMessage();
                    }
                }

            }
        }
    } else {
        $erro = "Preencha todos os campos.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title>Cadastro de Usuário</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <h1 class="mb-4">Cadastro de Usuário</h1>

        <?php if ($erro): ?>
        <div class="alert alert-danger" role="alert">
            <?= $erro ?>
        </div>
        <?php elseif ($sucesso): ?>
        <div class="alert alert-success" role="alert">
            <?= $sucesso ?>
        </div>
        <?php endif; ?>

        <form method="post" class="border p-4 rounded shadow-sm bg-light">
            <div class="mb-3">
                <label for="nome" class="form-label">Nome completo</label>
                <input type="text" class="form-control" name="nome" id="nome" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">E-mail</label>
                <input type="email" class="form-control" name="email" id="email" required>
            </div>
            <div class="mb-3">
                <label for="senha" class="form-label">Senha</label>
                <input type="password" class="form-control" name="senha" id="senha" required>
            </div>
            <button type="submit" class="btn btn-primary">Cadastrar</button>
        </form>

        <p class="mt-3">
            <a href="login.php">Voltar para o login</a>
        </p>
    </div>
</body>

</html>
