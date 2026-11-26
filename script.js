
// PESQUISA DO CARDÁPIO

const pesquisa = document.getElementById("pesquisa");

if (pesquisa) {

    pesquisa.addEventListener("input", function() {

        // Sua lógica de pesquisa pode ficar aqui.

    });

}




// FOTO DE PERFIL


const upload = document.getElementById("uploadFoto");
const fotoPerfil = document.getElementById("fotoPerfil");

if (upload && fotoPerfil) {

    upload.addEventListener("change", function() {

        const arquivo = upload.files[0];

        if (arquivo && arquivo.type.startsWith("image/")) {

            fotoPerfil.src = URL.createObjectURL(arquivo);

        }

    });

}

