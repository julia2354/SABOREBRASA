
<?php
session_start();
require_once "conexao.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: login.html");
    exit;
}

$nome = trim($_POST["nome"] ?? "");
$senha = $_POST["senha"] ?? "";

$sql = "SELECT id_cliente, nome, email, telefone, senha, foto
        FROM cliente
        WHERE nome = ?
        LIMIT 1";

$stmt = $conexao->prepare($sql);
$stmt->bind_param("s", $nome);
$stmt->execute();

$resultado = $stmt->get_result();

if ($resultado->num_rows === 1) {

    $cliente = $resultado->fetch_assoc();

    if (password_verify($senha, $cliente["senha"])) {

        session_regenerate_id(true);

        $_SESSION["id_usuario"] = $cliente["id_cliente"];
        $_SESSION["nome"] = $cliente["nome"];
        $_SESSION["email"] = $cliente["email"];
        $_SESSION["telefone"] = $cliente["telefone"];
        $_SESSION["foto"] = $cliente["foto"];

        header("Location: usuario.php");
        exit;

    } else {
        echo "Senha incorreta!";
    }

} else {
    echo "Nome de usuário não encontrado!";
}

$stmt->close();
?>
