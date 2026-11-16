
<?php
session_start();
echo "Caminho da foto: " . $_SESSION['foto'];

// CONEXÃO COM O BANCO
$servidor = "localhost";
$usuario = "root";
$senha_bd = "";
$banco = "saborebrasa";

$conexao = mysqli_connect($servidor, $usuario, $senha_bd, $banco);

if (!$conexao) {
    die("Erro de conexão: " . mysqli_connect_error());
}

// RECEBER OS DADOS
$nome = $_POST['nome'];
$email = $_POST['email'];
$telefone = $_POST['telefone'];
$senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);

// FOTO DE PERFIL
$foto = "";

if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {

    $arquivo = $_FILES['foto']['tmp_name'];

    if (getimagesize($arquivo) === false) {
        die("O arquivo enviado não é uma imagem.");
    }

    $pasta = "uploads/";

    if (!is_dir($pasta)) {
        mkdir($pasta, 0755, true);
    }

    $extensao = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));

    if (!in_array($extensao, ['jpg', 'jpeg', 'png', 'webp'])) {
        die("Formato de imagem inválido.");
    }

    $nomeFoto = uniqid() . "." . $extensao;
    $foto = $pasta . $nomeFoto;

    if (!move_uploaded_file($arquivo, $foto)) {
        die("Erro ao enviar a foto.");
    }
}

// CADASTRAR NO BANCO
$sql = "INSERT INTO cliente (nome, email, telefone, senha, foto)
        VALUES (?, ?, ?, ?, ?)";

$stmt = mysqli_prepare($conexao, $sql);

mysqli_stmt_bind_param(
    $stmt,
    "sssss",
    $nome,
    $email,
    $telefone,
    $senha,
    $foto
);

if (mysqli_stmt_execute($stmt)) {

    // GUARDAR DADOS NA SESSÃO
    $_SESSION['id_usuario'] = mysqli_insert_id($conexao);
    $_SESSION['nome'] = $nome;
    $_SESSION['email'] = $email;
    $_SESSION['telefone'] = $telefone;
    $_SESSION['foto'] = $foto;

    // IR PARA A PÁGINA DO USUÁRIO
    header("Location: usuario.php");
    exit();

} else {
    echo "Erro ao cadastrar: " . mysqli_stmt_error($stmt);
}

mysqli_stmt_close($stmt);
mysqli_close($conexao);
?>
