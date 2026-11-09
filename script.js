const upload = document.getElementById("uploadFoto");
const fotoPerfil = document.getElementById("fotoPerfil");

upload.addEventListener("change", function() {
    const arquivo = upload.files[0];

    if (arquivo && arquivo.type.startsWith("image/")) {
        fotoPerfil.src = URL.createObjectURL(arquivo);
    }
});