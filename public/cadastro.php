<?php
require_once '../config/conexao.php';

$erro = '';
$sucesso = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = trim($_POST['nome']);
    $email = trim($_POST['email']);
    $senha = $_POST['senha'];
    $CPF = trim($_POST['CPF']);
    $psecreta = trim($_POST['psecreta']);

    if ($nome && $email && $senha && $CPF && $psecreta) {
        if (strlen($senha) < 8 || !preg_match('/[\W]/', $senha)) {
            $erro = "A senha deve ter no mínimo 8 caracteres e incluir pelo menos 1 caractere especial.";
        } else {
            // Verifica duplicados
            $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = :email OR CPF = :CPF");
            $stmt->execute([
                ':email' => $email,
                ':CPF'   => $CPF
            ]);

            if ($stmt->rowCount() > 0) {
                $erro = "Já existe um usuário cadastrado com este e-mail ou CPF.";
            } else {
                // Criptografar a senha
                $senha_hash = password_hash($senha, PASSWORD_DEFAULT);

                // // Inserir no banco
                // $stmt = $pdo->prepare("INSERT INTO usuarios (nome, email, senha, CPF, psecreta) VALUES (:nome, :email, :senha, :CPF, :psecreta)");
                // $stmt->execute([
                //     ':nome' => $nome,
                //     ':email' => $email,
                //     ':senha' => $senha_hash,
                //     ':CPF' => $CPF,
                //     ':psecreta' => $psecreta
                // ]);

                // $sucesso = "Cadastro realizado com sucesso! <a href='login.php' class='alert-link'>Clique aqui para entrar</a>";
                try {
                    $stmt = $pdo->prepare("INSERT INTO usuarios (nome, email, senha, CPF, psecreta) VALUES (:nome, :email, :senha, :CPF, :psecreta)");
                    $stmt->execute([
                        ':nome' => $nome,
                        ':email' => $email,
                        ':senha' => $senha_hash,
                        ':CPF' => $CPF,
                        ':psecreta' => $psecreta
                    ]);

                    $sucesso = "Cadastro realizado com sucesso! <a href='login.php' class='alert-link'>Clique aqui para entrar</a>";
                } catch (PDOException $e) {
                    if ($e->getCode() == 23000) { 
                        $erro = "Usuário já cadastrado (e-mail ou CPF).";
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
    <img src="../img/CompreFácil.png" alt="logo Compre fácil" style="height:200px; align:center; margin: 0 auto; display: block;">
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
                <label for="password" class="form-label">Senha<label>
                <input type="password" class="form-control" name="senha" id="senha" required>
            </div>
            <div class="mb-3">
                <label for="CPF" class="form-label cpf">CPF</label>
                <input type="CPF" class="form-control CPF" name="CPF" id="CPF" required>
            </div>
            <div class="mb-3">
                <label for="psecreta" class="form-label">Qual sua cor preferida?</label>
                <input type="psecreta" class="form-control" name="psecreta" id="psecreta" required>
            </div>
            <button type="submit" class="btn btn-primary">Cadastrar</button>

            <button type="submit" class="btn btn-danger mt-4" ><a href="login.php">Voltar para o login</a></button>
            

        </form>
    </div>
    <script src="../assets/js/masks.js"></script>
</body>

</html>
