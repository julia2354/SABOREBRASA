<?php
session_start();
require_once "conexao.php";

mysqli_set_charset($conexao, "utf8mb4");

// 1) Só quem está logado pode finalizar
if (!isset($_SESSION["id_usuario"])) {
    header("Location: login.html");
    exit;
}

// 2) Só aceita POST (vem do botão "Finalizar pedido")
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: carrinho.html");
    exit;
}

// 3) Carrinho vazio? Volta para o carrinho
$carrinho = $_SESSION["carrinho"] ?? [];   // formato: id_produto => quantidade
if (empty($carrinho)) {
    header("Location: carrinho.html");
    exit;
}

// ID do cliente: vem da sessão criada no login/cadastro
$idCliente = (int) $_SESSION["id_usuario"];

// 4) Busca os preços no BANCO (nunca confiar no preço que vem do navegador)
$ids = array_map("intval", array_keys($carrinho));
$marcadores = implode(",", array_fill(0, count($ids), "?"));

$stmt = $conexao->prepare(
    "SELECT id_produto, nome, preco FROM produtos WHERE id_produto IN ($marcadores)"
);
$stmt->bind_param(str_repeat("i", count($ids)), ...$ids);
$stmt->execute();
$produtos = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

if (empty($produtos)) {
    die("Os produtos do carrinho não foram encontrados.");
}

// 5) Monta os itens e calcula o total
$itens = [];
$total = 0;

foreach ($produtos as $p) {
    $qtd = max(1, min(50, (int) $carrinho[$p["id_produto"]]));
    $preco = (float) $p["preco"];

    $itens[] = [
        "id_produto" => (int) $p["id_produto"],
        "nome"       => $p["nome"],
        "quantidade" => $qtd,
        "preco"      => $preco,
    ];

    $total += $preco * $qtd;
}

// 6) Grava tudo numa transação: ou salva o pedido inteiro, ou nada
$conexao->begin_transaction();

try {
    // Cria o pedido ligado ao cliente
    $stmt = $conexao->prepare("INSERT INTO pedido (id_cliente, total) VALUES (?, ?)");
    $stmt->bind_param("id", $idCliente, $total);
    $stmt->execute();
    $stmt->close();

    // ID do pedido que acabou de ser criado
    $idPedido = $conexao->insert_id;

    // Grava cada produto do carrinho ligado ao pedido
    $stmt = $conexao->prepare(
        "INSERT INTO item_pedido (id_pedido, id_produto, quantidade, preco_unitario)
         VALUES (?, ?, ?, ?)"
    );

    foreach ($itens as $item) {
        $stmt->bind_param("iiid", $idPedido, $item["id_produto"], $item["quantidade"], $item["preco"]);
        $stmt->execute();
    }
    $stmt->close();

    $conexao->commit();

} catch (Throwable $erro) {
    $conexao->rollback();
    die("Não foi possível finalizar o pedido. Tente novamente.");
}

// 7) Esvazia o carrinho
unset($_SESSION["carrinho"]);

function e($t) { return htmlspecialchars((string) $t, ENT_QUOTES, "UTF-8"); }
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pedido confirmado | Sabor &amp; Brasa</title>
    <link rel="stylesheet" href="style.css">
    <link rel="icon" type="image/x-icon" href="img/favicon.ico">
    <style>
        body { margin: 0; background: #8b5a2b; font-family: Georgia, "Times New Roman", serif; }
        .pedido-ok { max-width: 700px; margin: 40px auto; padding: 0 16px; }
        .pedido-caixa { background: #fff; border-radius: 10px; padding: 32px 24px; text-align: center; }
        .pedido-caixa h1 { color: #8b0000; margin: 0 0 8px; }
        .pedido-itens { width: 100%; border-collapse: collapse; margin: 24px 0; text-align: left; }
        .pedido-itens th, .pedido-itens td { padding: 10px 8px; border-bottom: 1px solid #eee; }
        .pedido-itens th { color: #8b0000; }
        .pedido-total { font-size: 22px; margin: 0 0 24px; }
        .pedido-btn { display: inline-block; padding: 12px 22px; background: #8b0000; color: #fff;
                      border-radius: 5px; text-decoration: none; font-weight: bold; }
        .pedido-btn:hover { background: #a10000; }
    </style>
</head>
<body>
    <header>
        <nav class="nav">
            <img src="img/Logo.png" alt="Logo da Hamburgueria Sabor & Brasa">

            <form action="pesquisar.php" method="get">
                <input type="search" id="pesquisa" class="buscar" name="q" placeholder=" 🔍︎  Pesquise no cardápio...">
                <button type="submit">Buscar</button>
            </form>

            <ul class="list">
                <li><a href="index.html">Home</a></li>
                <li><a href="index.html#Promoções">Promoções</a></li>
                <li><a href="cardápio.html">Cardápio</a></li>
                <li><a href="carrinho.html">Carrinho</a></li>
                <li><a href="usuario.php">Minha conta</a></li>
            </ul>
        </nav>
    </header>

    <main class="pedido-ok">
        <div class="pedido-caixa">
            <h1>Pedido confirmado! 🍔</h1>
            <p>Obrigado, <?= e($_SESSION["nome"]) ?>! Seu pedido nº <strong><?= (int) $idPedido ?></strong> foi recebido.</p>

            <table class="pedido-itens">
                <tr><th>Produto</th><th>Qtd.</th><th>Subtotal</th></tr>
                <?php foreach ($itens as $item): ?>
                    <tr>
                        <td><?= e($item["nome"]) ?></td>
                        <td><?= $item["quantidade"] ?></td>
                        <td>R$ <?= number_format($item["preco"] * $item["quantidade"], 2, ",", ".") ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>

            <p class="pedido-total">Total: <strong>R$ <?= number_format($total, 2, ",", ".") ?></strong></p>

            <a class="pedido-btn" href="cardápio.html">Voltar ao cardápio</a>
        </div>
    </main>

    <script>
    // Limpa a cópia do carrinho que o carrinho.js guarda no navegador
    try {
        Object.keys(localStorage)
            .filter(chave => chave.toLowerCase().includes("carrinho"))
            .forEach(chave => localStorage.removeItem(chave));
    } catch (e) {}
    </script>
</body>
</html>