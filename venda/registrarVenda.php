<?php
// Habilita exibição de erros
//ini_set('display_errors', 1);
//error_reporting(E_ALL);

require "../SVCE/config/conexao.php";
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="utf-8" />
    <title>Registrar Venda</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="container mt-5">
    <h1 class="mb-4">Registrar Venda</h1>

    <form action="registrar_itens.php" method="POST">
        <!-- Select de clientes -->
        <div class="mb-3">
            <label for="cliente" class="form-label">Selecionar Cliente</label>
            <select id="cliente" name="cliente_id" class="form-select" required>
                <option value="">Selecione um cliente</option>
                <?php
                try {
                    $stmt = $pdo->query("SELECT id, nome FROM clientes ORDER BY nome");
                    while ($cliente = $stmt->fetch()) {
                        echo "<option value='{$cliente['id']}'>" . htmlspecialchars($cliente['nome']) . "</option>";
                    }
                } catch (PDOException $e) {
                    echo "<option disabled>Erro ao carregar clientes</option>";
                }
                ?>
            </select>
        </div>

        <!-- Container de itens da venda -->
        <div id="itens-container">
            <div class="row mb-3 item-venda align-items-end">
                <div class="col-md-5">
                    <label class="form-label">Produto</label>
                    <select name="produto_id[]" class="form-select" required>
                        <?php
                        try {
                            $stmt = $pdo->query("SELECT id, nome FROM produtos ORDER BY nome");
                            while ($produto = $stmt->fetch()) {
                                echo "<option value='{$produto['id']}'>" . htmlspecialchars($produto['nome']) . "</option>";
                            }
                        } catch (PDOException $e) {
                            echo "<option disabled>Erro ao carregar produtos</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Quantidade</label>
                    <input type="number" name="quantidade[]" class="form-control quantidade" required value="1" onchange="calcularTotal()" />
                </div>
                <div class="col-md-3">
                    <label class="form-label">Preço Unitário (R$)</label>
		    <input type="number" name="preco_unitario[]" step="0.01" class="form-control preco" value="0" required readonly />

                </div>
                <div class="col-md-1">
                    <button type="button" class="btn btn-danger btn-sm mt-2" onclick="removerItem(this)">Remover</button>
                </div>
            </div>
        </div>

        <button type="button" class="btn btn-secondary mb-3" onclick="adicionarItem()">Adicionar Item</button>

        <h4 id="total-venda">Total: R$ 0,00</h4>

        <button type="submit" class="btn btn-primary">Registrar Venda</button>
        <a href="listar_vendas.php" class="btn btn-outline-secondary ms-2">Ver Vendas</a>
    </form>

    <script>
        function adicionarItem() {
            const container = document.getElementById('itens-container');
            const item = container.querySelector('.item-venda');
            const novoItem = item.cloneNode(true);
            novoItem.querySelectorAll('input').forEach(input => {
                if (input.name.includes('quantidade')) input.value = '1';
                else input.value = '0';
            });
            novoItem.querySelector('select').selectedIndex = 0;
            container.appendChild(novoItem);
            calcularTotal();
        }

        function removerItem(botao) {
            const container = document.getElementById('itens-container');
            if (container.children.length > 1) {
                botao.closest('.item-venda').remove();
                calcularTotal();
            } else {
                alert('Deve haver pelo menos um item na venda.');
            }
        }

        function calcularTotal() {
            let total = 0;
            const quantidades = document.querySelectorAll('.quantidade');
            const precos = document.querySelectorAll('.preco');
            for (let i = 0; i < quantidades.length; i++) {
                const qtd = parseFloat(quantidades[i].value) || 0;
                const preco = parseFloat(precos[i].value) || 0;
                total += qtd * preco;
            }
            document.getElementById('total-venda').innerText = 'Total: R$ ' + total.toFixed(2).replace('.', ',');
        }

        calcularTotal();
    </script>

    <script>
    	// Atualiza preço ao mudar produto
    	document.addEventListener('change', function (e) {
        	if (e.target.matches('select[name="produto_id[]"]')) {
            	const select = e.target;
            	const itemDiv = select.closest('.item-venda');
            	const precoInput = itemDiv.querySelector('input[name="preco_unitario[]"]');
            	const produtoId = select.value;

            	if (produtoId) {
                	fetch(`get_preco_produto.php?id=${produtoId}`)
                    	.then(res => res.json())
                    	.then(data => {
                        	if (data.preco !== undefined) {
                            	precoInput.value = parseFloat(data.preco).toFixed(2);
                            	calcularTotal();
                        	} else {
                            	precoInput.value = "0.00";
                        	}
                    	})
                    	.catch(err => {
                        	precoInput.value = "0.00";
                        	console.error("Erro ao buscar preço:", err);
                    	});
            	}
        	}
    	});
    </script>

<a href="../public/painel.php" class="btn btn-danger mt-4">Voltar ao painel</a>


</body>

</html>
