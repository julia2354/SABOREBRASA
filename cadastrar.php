

<?php
session_start();

$servidor = "localhost";
$usuario = "root";
$senha_bd = "";
$banco = "saborebrasa";

$conexao = mysqli_connect($servidor, $usuario, $senha_bd, $banco);

if (!$conexao) {
    die("Erro de conexão: " . mysqli_connect_error());
}

mysqli_set_charset($conexao, "utf8mb4");

// Receber dados do formulário
$nome = $_POST['nome'] ?? '';
$email = $_POST['email'] ?? '';
$telefone = $_POST['telefone'] ?? '';
$senha = $_POST['senha'] ?? '';

if (empty($nome) || empty($email) || empty($telefone) || empty($senha)) {
    die("Preencha todos os campos obrigatórios.");
}

// Criptografar a senha apenas uma vez
$senhaCriptografada = password_hash($senha, PASSWORD_DEFAULT);

// Foto do usuário
$foto = "";

if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {

    $arquivo = $_FILES['foto']['tmp_name'];

    if (getimagesize($arquivo) === false) {
        die("O arquivo enviado não é uma imagem.");
    }

    $extensao = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));

    if (!in_array($extensao, ['jpg', 'jpeg', 'png', 'webp'])) {
        die("Formato de imagem inválido.");
    }

    $pasta = __DIR__ . "/uploads/";

    if (!is_dir($pasta)) {
        mkdir($pasta, 0755, true);
    }

    $nomeFoto = uniqid() . "." . $extensao;

    if (!move_uploaded_file($arquivo, $pasta . $nomeFoto)) {
        die("Erro ao enviar a foto.");
    }

    $foto = "uploads/" . $nomeFoto;
}

// Inserir usuário no banco
$sql = "INSERT INTO cliente (nome, email, telefone, senha, foto)
        VALUES (?, ?, ?, ?, ?)";

$stmt = mysqli_prepare($conexao, $sql);

if (!$stmt) {
    die("Erro na preparação: " . mysqli_error($conexao));
}

mysqli_stmt_bind_param(
    $stmt,
    "sssss",
    $nome,
    $email,
    $telefone,
    $senhaCriptografada,
    $foto
);

if (mysqli_stmt_execute($stmt)) {

    // Guardar informações na sessão
    $_SESSION['id_usuario'] = mysqli_insert_id($conexao);
    $_SESSION['nome'] = $nome;
    $_SESSION['email'] = $email;
    $_SESSION['telefone'] = $telefone;
    $_SESSION['foto'] = $foto;

    // Ir para a página do usuário
    header("Location: usuario.php");
    exit();

} else {
    echo "Erro ao cadastrar: " . mysqli_stmt_error($stmt);
}

mysqli_stmt_close($stmt);
mysqli_close($conexao);
?>
