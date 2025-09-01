<?php
require "../SVCE/config/conexao.php";

header('Content-Type: application/json');

if (isset($_GET['id'])) {
    $id = (int) $_GET['id'];

    try {
        $stmt = $pdo->prepare("SELECT preco FROM produtos WHERE id = ?");
        $stmt->execute([$id]);

        $produto = $stmt->fetch();

        if ($produto) {
            echo json_encode(['preco' => $produto['preco']]);
        } else {
            echo json_encode(['erro' => 'Produto não encontrado']);
        }
    } catch (PDOException $e) {
        echo json_encode(['erro' => 'Erro no banco de dados']);
    }
} else {
    echo json_encode(['erro' => 'ID do produto não informado']);
}
