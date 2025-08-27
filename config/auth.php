<?php
// Caminho: config/auth.php

session_start();

if (!isset($_SESSION['usuario_logado'])) {
    header("Location: ../public/login.php");
    exit();
}
?>

