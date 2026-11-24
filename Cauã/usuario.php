
<?php
session_start();

$foto = $_SESSION['foto'] ?? '';

if (empty($foto)) {
    $foto = 'img/user.png';
}
?>

<img
    src="<?php echo htmlspecialchars($foto); ?>"
    alt="Foto de perfil"
    width="150"
    height="150"
    style="border-radius: 50%; object-fit: cover;"
>
