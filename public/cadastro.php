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
                try {
                    // Criptografar senha
                    $senha_hash = password_hash($senha, PASSWORD_DEFAULT);

                    // Inserir no banco
                    $stmt = $pdo->prepare("INSERT INTO usuarios (nome, email, senha, CPF, psecreta) 
                                           VALUES (:nome, :email, :senha, :CPF, :psecreta)");
                    $stmt->execute([
                        ':nome'     => $nome,
                        ':email'    => $email,
                        ':senha'    => $senha_hash,
                        ':CPF'      => $CPF,
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
