<?php
require "../config/conexao.php";

if (!isset($_POST['cliente_id'], $_POST['produto_id'], $_POST['quantidade'], $_POST['preco_unitario'])) {
    die("Dados incompletos.");
}

$cliente_id = (int)$_POST['cliente_id'];
$produto_ids = $_POST['produto_id'];
$quantidades = $_POST['quantidade'];
$precos = $_POST['preco_unitario'];

if (count($produto_ids) === 0) {
    die("Nenhum produto selecionado.");
}

try {
    // Iniciar transação
    $pdo->beginTransaction();

    // Inserir venda
    $stmt = $pdo->prepare("INSERT INTO vendas (cliente_id, data_venda) VALUES (?, NOW())");
    $stmt->execute([$cliente_id]);
    $venda_id = $pdo->lastInsertId();

    if (!$venda_id) {
        throw new Exception("Erro ao inserir venda.");
    }

    // Inserir itens da venda
    $stmt_item = $pdo->prepare("INSERT INTO itens_venda (venda_id, produto_id, quantidade, preco_unitario) VALUES (?, ?, ?, ?)");

    for ($i = 0; $i < count($produto_ids); $i++) {
        $produto_id = (int)$produto_ids[$i];
        $quantidade = (int)$quantidades[$i];
        $preco = (float)$precos[$i];

        $stmt_item->execute([$venda_id, $produto_id, $quantidade, $preco]);
    }

    // Confirmar transação
    $pdo->commit();

    header("Location: listar_vendas.php");
    exit;

} catch (Exception $e) {
    $pdo->rollBack();
    die("Erro ao registrar venda: " . $e->getMessage());
}
?>

