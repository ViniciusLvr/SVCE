<?php
require "config/conexao.php";
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="utf-8" />
    <title>Lista de Vendas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>

<body class="container mt-5">
    <h1 class="mb-4">Lista de Vendas</h1>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID Venda</th>
                <th>Cliente</th>
                <th>Data</th>
                <th>Itens</th>
                <th>Total (R$)</th>
            </tr>
        </thead>
        <tbody>
            <?php
            try {
                $sql = "
                    SELECT v.id, c.nome as cliente, v.data_venda
                    FROM vendas v
                    JOIN clientes c ON v.cliente_id = c.id
                    ORDER BY v.data_venda DESC
                ";
                $stmt = $pdo->query($sql);
                $vendas = $stmt->fetchAll();

                foreach ($vendas as $venda) {
                    $venda_id = $venda['id'];

                    // Buscar itens da venda
                    $stmt_itens = $pdo->prepare("
                        SELECT p.nome, iv.quantidade, iv.preco_unitario
                        FROM itens_venda iv
                        JOIN produtos p ON iv.produto_id = p.id
                        WHERE iv.venda_id = ?
                    ");
                    $stmt_itens->execute([$venda_id]);
                    $itens = $stmt_itens->fetchAll();

                    $total = 0;
                    $itens_list = [];
                    foreach ($itens as $item) {
                        $itens_list[] = "{$item['nome']} (x{$item['quantidade']})";
                        $total += $item['quantidade'] * $item['preco_unitario'];
                    }

                    echo "<tr>
                        <td>{$venda['id']}</td>
                        <td>" . htmlspecialchars($venda['cliente']) . "</td>
                        <td>{$venda['data_venda']}</td>
                        <td>" . implode(', ', $itens_list) . "</td>
                        <td>R$ " . number_format($total, 2, ',', '.') . "</td>
                    </tr>";
                }
            } catch (PDOException $e) {
                echo "<tr><td colspan='5'>Erro ao carregar vendas: " . $e->getMessage() . "</td></tr>";
            }
            ?>
        </tbody>
    </table>

    <a href="registrar_venda.php" class="btn btn-primary mt-4">Registrar Nova Venda</a>
    <a href="../public/painel.php" class="btn btn-danger mt-4 ms-2">Voltar ao painel</a>
</body>

</html>
