<?php
session_start();
require_once "conexao.php"; // define $conexao (mysqli)

mysqli_set_charset($conexao, "utf8mb4");

// Aceita ?q=, ?busca= ou ?pesquisa= (GET ou POST)
$termo = "";
foreach (["q", "busca", "pesquisa"] as $campo) {
    if (isset($_REQUEST[$campo]) && trim($_REQUEST[$campo]) !== "") {
        $termo = trim($_REQUEST[$campo]);
        break;
    }
}
$termo = mb_substr($termo, 0, 100);

$resultados = [];
if ($termo !== "") {
    $like = "%" . addcslashes($termo, "%_\\") . "%";

    $stmt = $conexao->prepare(
        "SELECT id_produto, nome, preco, imagem
           FROM produtos
          WHERE nome LIKE ?
          ORDER BY nome
          LIMIT 50"
    );
    $stmt->bind_param("s", $like);
    $stmt->execute();
    $resultados = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
}

// Modo JSON (busca ao vivo via fetch): pesquisar.php?q=x&formato=json
if (($_GET["formato"] ?? "") === "json") {
    header("Content-Type: application/json; charset=utf-8");
    echo json_encode(["termo" => $termo, "produtos" => $resultados], JSON_UNESCAPED_UNICODE);
    exit;
}

function e($texto) {
    return htmlspecialchars((string)$texto, ENT_QUOTES, "UTF-8");
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesquisar | Sabor &amp; Brasa</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Ultra&display=swap" rel="stylesheet">

    <!-- Mesmo CSS das outras páginas (cabeçalho e menu) -->
    <link rel="stylesheet" href="style.css">

    <!-- Estilos só desta página -->
    <style>
        body {
            margin: 0;
            background: #8b5a2b;
            font-family: Georgia, "Times New Roman", serif;
        }

        .pesquisa {
            max-width: 1100px;
            margin: 0 auto;
            padding: 40px 16px 60px;
        }

        .pesquisa-titulo {
            font-family: "Ultra", Georgia, serif;
            font-weight: 400;
            color: #fff;
            text-align: center;
            font-size: 40px;
            margin: 0 0 32px;
            text-shadow: 0 2px 4px rgba(0, 0, 0, .3);
        }

        .pesquisa-caixa {
            background: #fff;
            border-radius: 10px;
            padding: 36px 24px;
            text-align: center;
            max-width: 900px;
            margin: 0 auto;
        }
        .pesquisa-caixa h2 { color: #8b0000; margin: 0 0 10px; font-size: 22px; }
        .pesquisa-caixa p { margin: 0 0 20px; }

        .pesquisa-contagem {
            color: #fff;
            text-align: center;
            font-size: 18px;
            margin: 0 0 24px;
        }

        .pesquisa-resultados {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
            gap: 24px;
        }

        .pesquisa-produto {
            background: #fff;
            border-radius: 10px;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            box-shadow: 0 4px 12px rgba(0, 0, 0, .2);
            transition: transform .15s;
        }
        .pesquisa-produto:hover { transform: translateY(-4px); }
        .pesquisa-produto img { width: 100%; height: 180px; object-fit: cover; display: block; }

        .pesquisa-info {
            padding: 16px;
            display: flex;
            flex-direction: column;
            gap: 8px;
            flex: 1;
            text-align: center;
        }
        .pesquisa-info h3 { margin: 0; font-size: 20px; color: #8b0000; }
        .pesquisa-preco { font-size: 18px; font-weight: bold; }

        .pesquisa-btn {
            display: inline-block;
            padding: 11px 20px;
            border: 0;
            border-radius: 5px;
            background: #8b0000;
            color: #fff;
            font-family: Georgia, "Times New Roman", serif;
            font-size: 15px;
            font-weight: bold;
            text-decoration: none;
            cursor: pointer;
            transition: .2s;
        }
        .pesquisa-btn:hover { background: #a10000; }
        .pesquisa-produto .pesquisa-btn { margin-top: auto; }
        .pesquisa-btn.ok { background: #2e8b57; }
    </style>
</head>
<body>
    <header>
        <nav class="nav">

            <img src="img/Logo.png" alt="Logo Sabor & Brasa">

            <form action="pesquisar.php" method="get">
                <input
                    type="search"
                    id="pesquisa"
                    class="buscar"
                    name="q"
                    placeholder="🔍︎ Pesquise no cardápio..."
                    value="<?= e($termo) ?>"
                >

                <button type="submit">Buscar</button>
            </form>

            <ul class="list">
                <li><a href="index.html">Home</a></li>
                <li><a href="index.html#Promoções">Promoções</a></li>
                <li><a href="cardápio.html">Cardápio</a></li>
                <li><a href="cadastro.html">Cadastrar</a></li>
                <li><a href="login.html">Login</a></li>
            </ul>

        </nav>
    </header>

    <main class="pesquisa">
        <h1 class="pesquisa-titulo">🔍 Pesquisar 🍔</h1>

        <?php if ($termo === ""): ?>
            <div class="pesquisa-caixa">
                <h2>O que você está com vontade de comer?</h2>
                <p>Digite o nome de um produto na barra de pesquisa lá em cima.</p>
                <a class="pesquisa-btn" href="cardápio.html">Ver cardápio</a>
            </div>

        <?php elseif (empty($resultados)): ?>
            <div class="pesquisa-caixa">
                <h2>Nenhum produto encontrado para "<?= e($termo) ?>" 😕</h2>
                <p>Tente outra palavra, como "bacon", "cheddar" ou "combo".</p>
                <a class="pesquisa-btn" href="cardápio.html">Ver cardápio</a>
            </div>

        <?php else: ?>
            <p class="pesquisa-contagem"><?= count($resultados) ?> resultado(s) para "<strong><?= e($termo) ?></strong>"</p>
            <div class="pesquisa-resultados">
                <?php foreach ($resultados as $p): ?>
                    <div class="pesquisa-produto">
                        <img src="<?= e($p["imagem"]) ?>" alt="<?= e($p["nome"]) ?>">
                        <div class="pesquisa-info">
                            <h3><?= e($p["nome"]) ?></h3>
                            <span class="pesquisa-preco">R$ <?= number_format((float)$p["preco"], 2, ",", ".") ?></span>
                            <button type="button" class="pesquisa-btn btn-add" data-id="<?= (int)$p["id_produto"] ?>">Adicionar ao carrinho</button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>

    <script>
    document.querySelectorAll(".btn-add").forEach(function (botao) {
        botao.addEventListener("click", async function () {
            const id = botao.dataset.id;

            if (typeof window.adicionarAoCarrinho === "function") {
                window.adicionarAoCarrinho(id, 1);
                return;
            }

            const dados = new FormData();
            dados.append("acao", "adicionar");
            dados.append("id", id);
            dados.append("quantidade", 1);

            try {
                const resp = await fetch("carrinho_api.php", { method: "POST", body: dados });
                if (!resp.ok) throw new Error();
                botao.textContent = "Adicionado ✓";
                botao.classList.add("ok");
                setTimeout(() => {
                    botao.textContent = "Adicionar ao carrinho";
                    botao.classList.remove("ok");
                }, 1500);
            } catch (err) {
                alert("Não foi possível adicionar ao carrinho.");
            }
        });
    });
    </script>
</body>
</html>