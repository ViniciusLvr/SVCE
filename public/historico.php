<?php
session_start();

// Proteção com sessão
if (!isset($_SESSION['acesso_permitido'])) {
  header("Location: login.php");
  exit();
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>Histórico</title>
</head>
<body>

  <h1>Página de Histórico</h1>
  
  <script>
    // Proteção adicional via sessionStorage no navegador
    if (!sessionStorage.getItem("acesso_permitido")) {
      window.location.href = "login.html"; // ou .php, depende do seu projeto
    }

    // Impede voltar para a página anterior (ex: login)
    history.pushState(null, "", window.location.href);
    window.onpopstate = function () {
      history.pushState(null, "", window.location.href);
    };

    // Se o usuário veio diretamente da página de login, redireciona
    const previousPage = document.referrer;
    if (previousPage.includes("login.html") || previousPage.includes("login.php")) {
      window.location.replace("dashboard.html"); // ou outra página que faça sentido
    }
  </script>

</body>
</html>
