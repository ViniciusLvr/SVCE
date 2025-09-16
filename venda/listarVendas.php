<?php
require "../config/conexao.php";

// Paginação
$registros_por_pagina = 10;
$pagina_atual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
if ($pagina_atual < 1) $pagina_atual = 1;
$offset = ($pagina_atual - 1) * $registros_por_pagina;

// Contar total de vendas
$total_vendas = $pdo->query("SELECT COUNT(*) FROM vendas")->fetchColumn();
$total_paginas = ceil($total_vendas / $registros_por_pagina);

// Buscar vendas paginadas
$sql = "
    SELECT v.id, c.nome as cliente, v.data_venda
    FROM vendas v
    JOIN clientes c ON v.cliente_id = c.id
    ORDER BY v.data_venda DESC
    LIMIT :limit OFFSET :offset
";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':limit', (int)$registros_por_pagina, PDO::PARAM_INT);
$stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
$stmt->execute();
$vendas = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="utf-8" />
    <title>Lista de Vendas</title>
    <link rel="icon" href="../img/CompreFacil.png" type="image/png">
    <link rel="stylesheet" href="../assets/style/animated-gradient.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>

<body>
    <nav class="navbar" style="background: rgba(33, 37, 41, 0.85); mb-4;">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="../public/painel.php">
                <img src="../img/CompreFacil.png" alt="Logo do Sistema Compre Fácil" width="48" height="40" class="me-2"
                    style="object-fit:contain;">
                <span class="fw-bold text-white">Compre Fácil</span>
            </a>
            <a href="../public/painel.php" class="btn btn-danger mt-4">Voltar ao painel</a>
        </div>
    </nav>

    <div class="container bg-light p-4 rounded shadow-sm mb-5 mt-5">
        <div class="container mt-5">
            <h1 class="mb-4">Lista de Vendas</h1>
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead class="table-dark">
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
            </div>
            <a href="registrarVenda.php" class="btn btn-primary mt-4">Registrar Nova Venda</a>
            <a href="../public/painel.php" class="btn btn-danger mt-4 ms-2">Voltar ao painel</a>

            <!-- Paginação -->
            <nav>
                <ul class="pagination justify-content-center mt-4">
                    <li class="page-item <?= ($pagina_atual <= 1) ? 'disabled' : '' ?>">
                        <a class="page-link" href="?pagina=<?= $pagina_atual - 1 ?>">Anterior</a>
                    </li>
                    <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                    <li class="page-item <?= ($i == $pagina_atual) ? 'active' : '' ?>">
                        <a class="page-link" href="?pagina=<?= $i ?>"><?= $i ?></a>
                    </li>
                    <?php endfor; ?>
                    <li class="page-item <?= ($pagina_atual >= $total_paginas) ? 'disabled' : '' ?>">
                        <a class="page-link" href="?pagina=<?= $pagina_atual + 1 ?>">Próximo</a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>