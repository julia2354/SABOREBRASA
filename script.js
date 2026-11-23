const upload = document.getElementById("uploadFoto");
const fotoPerfil = document.getElementById("fotoPerfil");

upload.addEventListener("change", function() {
    const arquivo = upload.files[0];

    if (arquivo && arquivo.type.startsWith("image/")) {
        fotoPerfil.src = URL.createObjectURL(arquivo);
    }
});

document.getElementById("uploadFoto").addEventListener("change", function(event) {

    const arquivo = event.target.files[0];

    if (arquivo) {
        document.getElementById("fotoPerfil").src =
            URL.createObjectURL(arquivo);
    }

});