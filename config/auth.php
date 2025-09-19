<?php
// Caminho: config/auth.php

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if (!isset($_SESSION['usuario_logado'])) {
    header("Location: ../public/login.php");
    exit();
}

function getCargo()
{
    return $_SESSION['cargo'] ?? 'vendedor';
}

//verifica se o usuário tem permissão para acessar a página

function verificarPermissao($cargosPermitidos)
{
    $cargo = getCargo();
    if (!in_array($cargo, $cargosPermitidos)) {
        header("Location: ../public/painel.php?erro=acesso_negado");
        exit();
    }
}
