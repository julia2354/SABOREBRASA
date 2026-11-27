<?php
session_start();

// Apaga todos os dados da sessão (id_usuario, nome, email, telefone, foto, carrinho...)
$_SESSION = [];

// Apaga o cookie da sessão no navegador
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        "",
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// Destrói a sessão no servidor
session_destroy();

// Volta para a tela de login
header("Location: login.html");
exit;
?>