
<?php
session_start();

if (!isset($_SESSION["id_usuario"])) {
    header("Location: login.html");
    exit;
}

$nome = $_SESSION["nome"] ?? "Cliente";
$email = $_SESSION["email"] ?? "";
$foto = $_SESSION["foto"] ?? "";

// Verifica se a foto existe
$fotoExiste = !empty($foto) && is_file(__DIR__ . "/" . $foto);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Minha conta - Sabor & Brasa</title>
    <link rel="stylesheet" href="usuario.css">
    <link rel="icon" type="image/x-icon" href="img/favicon.ico">
    <link href="https://fonts.googleapis.com/css2?family=Alfa+Slab+One&family=Anton+SC&family=Bungee&family=Chango&family=Climate+Crisis:YEAR@1979..2050&family=Comic+Neue:ital,wght@0,300;0,400;0,700;1,300;1,400;1,700&family=Edu+TAS+Beginner&family=Emilys+Candy&family=Fascinate+Inline&family=Kranky&family=Limelight&family=Sancreek&family=Shadows+Into+Light+Two&family=Spicy+Rice&family=Tapestry&display=swap" rel="stylesheet">
</head>

<body>

<header class="topo">
<header>
        <nav  class="nav">

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
                        <li><a href="sair.php">Sair da conta</a></li>

            </ul>
        </nav>
    </header>
</header>

<main class="perfil">

    <h1>Minha conta</h1>
    <p class="subtitulo">Seja bem-vindo ao Sabor & Brasa!</p>

    <section class="cartao-perfil">

        <?php if ($fotoExiste): ?>

            <img
                class="foto-perfil"
                src="<?= htmlspecialchars($foto) ?>"
                alt="Foto de perfil"
            >

        <?php else: ?>

            <div class="foto-padrao">👤</div>

        <?php endif; ?>

        <h2><?= htmlspecialchars($nome) ?></h2>

        <div class="informacoes">
            <p>
                <strong>Nome:</strong>
                <?= htmlspecialchars($nome) ?>
            </p>

            <p>
                <strong>E-mail:</strong>
                <?= htmlspecialchars($email) ?>
            </p>
        </div>

        <a href="index.php" class="botao">Voltar à loja</a>

    </section>

</main>

</body>
</html>
