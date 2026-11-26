
<?php
session_start();
require_once "conexao.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: cadastro.html");
    exit;
}

$nome = trim($_POST["nome"] ?? "");
$email = trim($_POST["email"] ?? "");
$telefone = trim($_POST["telefone"] ?? "");
$senha = $_POST["senha"] ?? "";

if (
    $nome === "" ||
    !filter_var($email, FILTER_VALIDATE_EMAIL) ||
    strlen($senha) < 6
) {
    die("Preencha os dados corretamente. A senha deve ter pelo menos 6 caracteres.");
}

// Verifica se o e-mail já está cadastrado
$stmt = $conexao->prepare(
    "SELECT id_cliente FROM cliente WHERE email = ?"
);

$stmt->bind_param("s", $email);
$stmt->execute();

$resultado = $stmt->get_result();

if ($resultado->num_rows > 0) {
    die("Este e-mail já está cadastrado.");
}

$stmt->close();

// Foto padrão
$foto = "img/usuario/perfil-padrao.png";

// Verifica se o cliente enviou uma foto
if (isset($_FILES["foto"]) && $_FILES["foto"]["error"] !== UPLOAD_ERR_NO_FILE) {

    if ($_FILES["foto"]["error"] !== UPLOAD_ERR_OK) {
        die("Não foi possível enviar a foto.");
    }

    if ($_FILES["foto"]["size"] > 3 * 1024 * 1024) {
        die("A foto deve ter no máximo 3 MB.");
    }

    $tipo = mime_content_type($_FILES["foto"]["tmp_name"]);

    $tiposPermitidos = [
        "image/jpeg" => "jpg",
        "image/png" => "png",
        "image/webp" => "webp"
    ];

    if (!isset($tiposPermitidos[$tipo])) {
        die("Envie uma imagem JPG, PNG ou WEBP.");
    }

    $pasta = "uploads/";

    if (!is_dir($pasta)) {
        mkdir($pasta, 0755, true);
    }

    $extensao = $tiposPermitidos[$tipo];

    $nomeFoto = bin2hex(random_bytes(16)) . "." . $extensao;

    $destino = $pasta . $nomeFoto;

    if (!move_uploaded_file($_FILES["foto"]["tmp_name"], $destino)) {
        die("Erro ao salvar a foto.");
    }

    $foto = $destino;
}

// Protege a senha
$senhaHash = password_hash($senha, PASSWORD_DEFAULT);

// Cadastra o cliente
$stmt = $conexao->prepare(
    "INSERT INTO cliente (nome, email, telefone, senha, foto)
     VALUES (?, ?, ?, ?, ?)"
);

$stmt->bind_param(
    "sssss",
    $nome,
    $email,
    $telefone,
    $senhaHash,
    $foto
);

if ($stmt->execute()) {

    session_regenerate_id(true);

    $_SESSION["id_usuario"] = $conexao->insert_id;
    $_SESSION["nome"] = $nome;
    $_SESSION["email"] = $email;
    $_SESSION["telefone"] = $telefone;
    $_SESSION["foto"] = $foto;

    header("Location: usuario.php");
    exit;

} else {
    die("Erro ao realizar cadastro.");
}

$stmt->close();
?>
