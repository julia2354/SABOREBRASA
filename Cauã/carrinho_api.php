
<?php

session_start();

require_once "conexao.php";

header("Content-Type: application/json; charset=UTF-8");
if (!isset($_SESSION["carrinho"])) {
    $_SESSION["carrinho"] = [];
}

$acao = $_GET["acao"] ?? "";

function responder($dados) {
    echo json_encode($dados, JSON_UNESCAPED_UNICODE);
    exit;
}


function obterCarrinho($conexao) {

    $resultado = [];
    $total = 0;

    foreach ($_SESSION["carrinho"] as $id => $quantidade) {

        $id = (int)$id;

        $stmt = $conexao->prepare(
            "SELECT id_produto AS id, nome, preco, imagem
             FROM produtos
             WHERE id_produto = ?"
        );

        $stmt->bind_param("i", $id);
        $stmt->execute();

        $produto = $stmt->get_result()->fetch_assoc();

        $stmt->close();

        if ($produto) {

            $produto["preco"] = (float)$produto["preco"];

            $produto["quantidade"] = (int)$quantidade;

            $produto["subtotal"] =
                $produto["preco"] * $produto["quantidade"];

            $total += $produto["subtotal"];

            $resultado[] = $produto;
        }
    }

    return [
        "produtos" => $resultado,
        "total" => $total
    ];
}

// LISTAR PRODUTOS
if ($acao === "listar") {

    responder(obterCarrinho($conexao));

}

// ADICIONAR PRODUTO
if ($acao === "adicionar") {

    $dados = json_decode(file_get_contents("php://input"), true);

    $id = (int)($dados["id"] ?? 0);
    $quantidade = (int)($dados["quantidade"] ?? 1);

    if ($id <= 0 || $quantidade <= 0) {
        http_response_code(400);
        responder(["erro" => "Produto inválido."]);
    }

    $stmt = $conexao->prepare(
        "SELECT id_produto FROM produtos WHERE id_produto = ?"
    );

    $stmt->bind_param("i", $id);
    $stmt->execute();

    $existe = $stmt->get_result()->num_rows > 0;

    $stmt->close();

    if (!$existe) {
        http_response_code(404);
        responder(["erro" => "Produto não encontrado."]);
    }

    $atual = (int)($_SESSION["carrinho"][$id] ?? 0);

    if ($atual + $quantidade > 50) {
        http_response_code(400);
        responder(["erro" => "Quantidade máxima excedida."]);
    }

    $_SESSION["carrinho"][$id] = $atual + $quantidade;

    responder(obterCarrinho($conexao));
}

// ATUALIZAR QUANTIDADE
if ($acao === "atualizar") {

    $dados = json_decode(file_get_contents("php://input"), true);

    $id = (int)($dados["id"] ?? 0);
    $quantidade = (int)($dados["quantidade"] ?? 0);

    if (!isset($_SESSION["carrinho"][$id])) {
        http_response_code(404);
        responder(["erro" => "Produto não encontrado no carrinho."]);
    }

    if ($quantidade <= 0) {

        unset($_SESSION["carrinho"][$id]);

    } elseif ($quantidade <= 50) {

        $_SESSION["carrinho"][$id] = $quantidade;

    } else {

        http_response_code(400);
        responder(["erro" => "Quantidade máxima excedida."]);

    }

    responder(obterCarrinho($conexao));
}

// REMOVER PRODUTO
if ($acao === "remover") {

    $dados = json_decode(file_get_contents("php://input"), true);

    $id = (int)($dados["id"] ?? 0);

    unset($_SESSION["carrinho"][$id]);

    responder(obterCarrinho($conexao));
}

// LIMPAR CARRINHO
if ($acao === "limpar") {

    $_SESSION["carrinho"] = [];

    responder(obterCarrinho($conexao));
}

http_response_code(400);

responder(["erro" => "Ação inválida."]);

?>
