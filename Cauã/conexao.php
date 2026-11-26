
<?php

$servidor = "localhost";
$usuario = "root";
$senha_bd = "";
$banco = "saborebrasa";

$conexao = mysqli_connect(
    $servidor,
    $usuario,
    $senha_bd,
    $banco
);

if (!$conexao) {
    die("Erro de conexão: " . mysqli_connect_error());
}

mysqli_set_charset($conexao, "utf8mb4");

?>
