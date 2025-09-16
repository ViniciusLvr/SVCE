<?php
require_once '../config/conexao.php';

$erro = '';
$sucesso = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = trim($_POST['nome']);
    $email = trim($_POST['email']);
    $senha = $_POST['senha'];
    $CPF = preg_replace('/\D/', '', $_POST['CPF']);
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
    <link rel="icon" href="../img/CompreFacil.png" type="image/png">
    <link rel="stylesheet" href="../assets/style/animated-gradient.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <nav class="navbar" style="background: rgba(33, 37, 41, 0.85);">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="login.php">
                <img src="../img/CompreFacil.png" alt="Logo do Sistema Compre Fácil" width="48" height="40" class="me-2" style="object-fit:contain;">
                <span class="fw-bold text-white">Compre Fácil</span>
            </a>
            <a href="login.php" class="btn btn-danger">Voltar para o Login</a>
        </div>
    </nav>
    <div class="container">
        <div class="container mt-5">



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
                <h1 class="mb-4">Cadastro de Usuário</h1>
                <div class="mb-3">
                    <label for="nome" class="form-label">Nome completo</label>
                    <input type="text" class="form-control" name="nome" id="nome" required>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">E-mail</label>
                    <input type="email" class="form-control" name="email" id="email" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Senha</label>
                    <input type="password" class="form-control" name="senha" id="senha" required>
                </div>
                <div class="mb-3">
                    <label for="CPF" class="form-label">CPF</label>
                    <input type="text" class="form-control cpf" name="CPF" id="CPF"
                           pattern="\d{3}\.\d{3}\.\d{3}-\d{2}" placeholder="000.000.000-00"
                           title="Digite o CPF no formato 000.000.000-00" required>
                </div>
                <div class="mb-3">
                    <label for="psecreta" class="form-label">Qual sua cor preferida?</label>
                    <input type="psecreta" class="form-control" name="psecreta" id="psecreta" required>
                </div>
                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-success">Cadastrar</button>
                </div>
            </form>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
    <script src="../assets/js/masks.js"></script>
    <script>
$(document).ready(function(){
    $('#CPF').mask('000.000.000-00');
});
</script>
</body>

</html>