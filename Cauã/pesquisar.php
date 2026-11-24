
<?php
include "conexao.php";

$pesquisa = $_GET['busca'] ?? '';

$sql = "SELECT * FROM produtos
        WHERE disponivel = 1
        AND nome LIKE ?";

$conn = new mysqli($servidor, $usuario, $senha_bd, $banco);
$stmt = $conn->prepare($sql);


$termo = "%" . $pesquisa . "%";
$stmt->bind_param("s", $termo);

$stmt->execute();

$resultado = $stmt->get_result();

while ($produto = $resultado->fetch_assoc()) {
    echo "<div class='produto'>";

    echo "<img src='uploads/" .
         htmlspecialchars($produto['imagem']) . "'>";

    echo "<h3>" .
         htmlspecialchars($produto['nome']) . "</h3>";

    echo "<p>" .
         htmlspecialchars($produto['descricao']) . "</p>";

    echo "<strong>R$ " .
         number_format($produto['preco'], 2, ',', '.') .
         "</strong>";

    echo "</div>";
}
?>
