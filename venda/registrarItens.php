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
    $pdo->beginTransaction();

    $total_venda = 0;

    // Preparar statements com antecedência
    $stmt_estoque = $pdo->prepare("SELECT quantidade_estoque FROM produtos WHERE id = ?");
    $stmt_atualiza_estoque = $pdo->prepare("UPDATE produtos SET quantidade_estoque = quantidade_estoque - ? WHERE id = ?");
    $stmt_item = $pdo->prepare("INSERT INTO itens_venda (venda_id, produto_id, quantidade, preco_unitario) VALUES (?, ?, ?, ?)");

    // Verificar estoque e calcular total
    for ($i = 0; $i < count($produto_ids); $i++) {
        $produto_id = (int)$produto_ids[$i];
        $quantidade = (int)$quantidades[$i];
        $preco = (float)$precos[$i];

        // Verifica estoque
        $stmt_estoque->execute([$produto_id]);
        $produto = $stmt_estoque->fetch();

        if (!$produto) {
            throw new Exception("Produto ID $produto_id não encontrado.");
        }

        if ($produto['quantidade_estoque'] < $quantidade) {
            throw new Exception("Estoque insuficiente para o produto ID $produto_id. Disponível: {$produto['quantidade_estoque']}, solicitado: $quantidade.");
        }

        $total_venda += $quantidade * $preco;
    }

    // Inserir venda
    $stmt_venda = $pdo->prepare("INSERT INTO vendas (cliente_id, total, data_venda) VALUES (?, ?, NOW())");
    $stmt_venda->execute([$cliente_id, $total_venda]);
    $venda_id = $pdo->lastInsertId();

    if (!$venda_id) {
        throw new Exception("Erro ao inserir venda.");
    }

    // Inserir itens e atualizar estoque
    for ($i = 0; $i < count($produto_ids); $i++) {
        $produto_id = (int)$produto_ids[$i];
        $quantidade = (int)$quantidades[$i];
        $preco = (float)$precos[$i];

        $stmt_item->execute([$venda_id, $produto_id, $quantidade, $preco]);
        $stmt_atualiza_estoque->execute([$quantidade, $produto_id]);
    }

    $pdo->commit();

    header("Location: listarVendas.php?sucesso=1");
    exit;

} catch (Exception $e) {
    $pdo->rollBack();
    die("Erro ao registrar venda: " . $e->getMessage());
}
