<?php
// Captura qualquer saída acidental (avisos, echo do conexao.php) para não quebrar o JSON
ob_start();

session_start();

header("Content-Type: application/json; charset=UTF-8");
header("Cache-Control: no-store");

const QUANTIDADE_MAXIMA = 50;

function responder($dados, $status = 200) {
    // Descarta qualquer coisa que tenha sido impressa antes do JSON
    if (ob_get_length()) {
        ob_clean();
    }
    http_response_code($status);
    echo json_encode($dados, JSON_UNESCAPED_UNICODE);
    exit;
}

function lerCorpo() {
    $dados = json_decode(file_get_contents("php://input"), true);
    // Corpo vazio ou JSON inválido vira array vazio (evita aviso de "array offset on null")
    return is_array($dados) ? $dados : [];
}

// Faz o mysqli lançar exceções, para capturarmos e responder em JSON
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    require_once "conexao.php";
} catch (Throwable $erro) {
    error_log("Carrinho - conexão: " . $erro->getMessage());
    responder(["erro" => "Não foi possível conectar ao banco de dados."], 500);
}

if (!isset($conexao) || !($conexao instanceof mysqli)) {
    responder(["erro" => "Conexão com o banco não configurada."], 500);
}

$conexao->set_charset("utf8mb4");

if (!isset($_SESSION["carrinho"]) || !is_array($_SESSION["carrinho"])) {
    $_SESSION["carrinho"] = [];
}

$metodo = $_SERVER["REQUEST_METHOD"];
$acao = $_GET["acao"] ?? "";

$acoesValidas = ["listar", "adicionar", "atualizar", "remover", "limpar"];

if (!in_array($acao, $acoesValidas, true)) {
    responder(["erro" => "Ação inválida."], 400);
}

// "listar" só lê dados; as demais alteram o carrinho e exigem POST
if ($acao === "listar" && $metodo !== "GET" && $metodo !== "POST") {
    responder(["erro" => "Método não permitido."], 405);
}

if ($acao !== "listar" && $metodo !== "POST") {
    header("Allow: POST");
    responder(["erro" => "Método não permitido."], 405);
}

function obterCarrinho($conexao) {

    $produtos = [];
    $total = 0.0;

    $ids = array_map("intval", array_keys($_SESSION["carrinho"]));
    $ids = array_values(array_filter($ids, fn($id) => $id > 0));

    if (count($ids) === 0) {
        return ["produtos" => [], "total" => 0];
    }

    // Uma única consulta para todos os produtos, em vez de uma por item
    $marcadores = implode(",", array_fill(0, count($ids), "?"));

    $stmt = $conexao->prepare(
        "SELECT id_produto AS id, nome, preco, imagem
         FROM produtos
         WHERE id_produto IN ($marcadores)"
    );

    $stmt->bind_param(str_repeat("i", count($ids)), ...$ids);
    $stmt->execute();

    $encontrados = [];
    $resultado = $stmt->get_result();

    while ($linha = $resultado->fetch_assoc()) {
        $encontrados[(int)$linha["id"]] = $linha;
    }

    $stmt->close();

    // Mantém a ordem em que os itens foram adicionados
    foreach ($_SESSION["carrinho"] as $id => $quantidade) {

        $id = (int)$id;

        if (!isset($encontrados[$id])) {
            // Produto saiu do banco: tira da sessão também
            unset($_SESSION["carrinho"][$id]);
            continue;
        }

        $produto = $encontrados[$id];

        $produto["id"] = $id;
        $produto["preco"] = (float)$produto["preco"];
        $produto["quantidade"] = (int)$quantidade;
        $produto["subtotal"] = round($produto["preco"] * $produto["quantidade"], 2);

        $total += $produto["subtotal"];

        $produtos[] = $produto;
    }

    return [
        "produtos" => $produtos,
        "total" => round($total, 2)
    ];
}

function produtoExiste($conexao, $id) {

    $stmt = $conexao->prepare(
        "SELECT 1 FROM produtos WHERE id_produto = ? LIMIT 1"
    );

    $stmt->bind_param("i", $id);
    $stmt->execute();

    $existe = $stmt->get_result()->num_rows > 0;

    $stmt->close();

    return $existe;
}

try {

    switch ($acao) {

        // LISTAR PRODUTOS
        case "listar":
            responder(obterCarrinho($conexao));

        // ADICIONAR PRODUTO
        case "adicionar":

            $dados = lerCorpo();

            $id = (int)($dados["id"] ?? 0);
            $quantidade = (int)($dados["quantidade"] ?? 1);

            if ($id <= 0 || $quantidade <= 0) {
                responder(["erro" => "Produto inválido."], 400);
            }

            if (!produtoExiste($conexao, $id)) {
                responder(["erro" => "Produto não encontrado."], 404);
            }

            $atual = (int)($_SESSION["carrinho"][$id] ?? 0);

            if ($atual + $quantidade > QUANTIDADE_MAXIMA) {
                responder(["erro" => "Quantidade máxima excedida (" . QUANTIDADE_MAXIMA . " por produto)."], 400);
            }

            $_SESSION["carrinho"][$id] = $atual + $quantidade;

            responder(obterCarrinho($conexao));

        // ATUALIZAR QUANTIDADE
        case "atualizar":

            $dados = lerCorpo();

            $id = (int)($dados["id"] ?? 0);
            $quantidade = (int)($dados["quantidade"] ?? 0);

            if ($id <= 0) {
                responder(["erro" => "Produto inválido."], 400);
            }

            if (!isset($_SESSION["carrinho"][$id])) {
                responder(["erro" => "Produto não encontrado no carrinho."], 404);
            }

            if ($quantidade > QUANTIDADE_MAXIMA) {
                responder(["erro" => "Quantidade máxima excedida (" . QUANTIDADE_MAXIMA . " por produto)."], 400);
            }

            if ($quantidade <= 0) {
                unset($_SESSION["carrinho"][$id]);
            } else {
                $_SESSION["carrinho"][$id] = $quantidade;
            }

            responder(obterCarrinho($conexao));

        // REMOVER PRODUTO
        case "remover":

            $dados = lerCorpo();

            $id = (int)($dados["id"] ?? 0);

            if ($id <= 0) {
                responder(["erro" => "Produto inválido."], 400);
            }

            unset($_SESSION["carrinho"][$id]);

            responder(obterCarrinho($conexao));

        // LIMPAR CARRINHO
        case "limpar":

            $_SESSION["carrinho"] = [];

            responder(obterCarrinho($conexao));
    }

} catch (Throwable $erro) {
    // Registra o detalhe no log do servidor e devolve uma mensagem segura ao navegador
    error_log("Carrinho - " . $acao . ": " . $erro->getMessage());
    responder(["erro" => "Erro interno no carrinho. Tente novamente."], 500);
}