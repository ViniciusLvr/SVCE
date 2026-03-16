<?php
include "../config/conexao.php";

$id_venda = (int)$_GET['id'];

$venda_query = mysqli_query($conexao, "
  SELECT v.id, v.data_venda, c.nome AS cliente
  FROM vendas v
  LEFT JOIN clientes c ON v.cliente_id = c.id
  WHERE v.id = $id_venda
");
$venda = mysqli_fetch_assoc($venda_query);

$itens_query = mysqli_query($conexao, "
  SELECT p.nome AS produto, i.quantidade, i.preco_unitario,
         (i.quantidade * i.preco_unitario) AS total
  FROM itens_venda i
  JOIN produtos p ON p.id = i.produto_id
  WHERE i.venda_id = $id_venda
");
?>

<div class="container mt-4">
    <h2>Detalhes da Venda #<?= $venda['id'] ?></h2>
    <p><strong>Data:</strong> <?= $venda['data_venda'] ?> | <strong>Cliente:</strong>
        <?= htmlspecialchars($venda['cliente']) ?></p>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Produto</th>
                <th>Quantidade</th>
                <th>Preço Unitário (R$)</th>
                <th>Total (R$)</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $total_venda = 0;
            while ($item = mysqli_fetch_assoc($itens_query)) {
                $total_venda += $item['total'];
            ?>
            <tr>
                <td><?= htmlspecialchars($item['produto']) ?></td>
                <td><?= $item['quantidade'] ?></td>
                <td><?= number_format($item['preco_unitario'], 2, ',', '.') ?></td>
                <td><?= number_format($item['total'], 2, ',', '.') ?></td>
            </tr>
            <?php } ?>
        </tbody>
        <tfoot>
            <tr>
                <th colspan="3" class="text-end">Total da Venda</th>
                <th><?= number_format($total_venda, 2, ',', '.') ?></th>
            </tr>
        </tfoot>
    </table>

    <a href="listar_vendas.php" class="btn btn-secondary">Voltar</a>
</div>
