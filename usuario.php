<?php
session_start();


if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit();
}

$nomeFoto = basename($_SESSION['foto']);
$caminhoFoto = "uploads/" . $nomeFoto;

echo "Nome da foto: " . htmlspecialchars($nomeFoto);
echo "<br>";

echo "Caminho: " . htmlspecialchars($caminhoFoto);
echo "<br>";

if (file_exists(__DIR__ . "/uploads/" . $nomeFoto)) {
    echo "A imagem foi encontrada!";
} else {
    echo "A imagem NÃO foi encontrada!";
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Minha conta - Sabor & Brasa</title>
</head>

<body>

    <h1>Bem-vindo ao Sabor & Brasa!</h1>

    <img src="<?php echo htmlspecialchars($caminhoFoto); ?>"
     width="120"
     height="120"
     alt="Foto de perfil">

    <h2>
        Olá, <?php echo htmlspecialchars($_SESSION['nome']); ?>!
    </h2>

    <p>
        E-mail: <?php echo htmlspecialchars($_SESSION['email']); ?>
    </p>

</body>
</html>
