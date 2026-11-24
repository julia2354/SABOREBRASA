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


document.addEventListener("DOMContentLoaded", function () {

    const campoPesquisa = document.getElementById("pesquisa");
    const cardapio = document.querySelector(".batata");

    if (!campoPesquisa || !cardapio) return;

    const produtos = cardapio.querySelectorAll("article");
    const mensagem = document.getElementById("mensagemPesquisa");

    function filtrarProdutos(valor) {

        const pesquisa = valor
            .toLowerCase()
            .normalize("NFD")
            .replace(/[\u0300-\u036f]/g, "")
            .trim();

        let encontrados = 0;

        produtos.forEach(function (produto) {

            const nome = produto.querySelector("h2").textContent;
            const descricao = produto.querySelector("img").alt;

            const texto = (nome + " " + descricao)
                .toLowerCase()
                .normalize("NFD")
                .replace(/[\u0300-\u036f]/g, "");

            if (texto.includes(pesquisa)) {
                produto.style.display = "";
                encontrados++;
            } else {
                produto.style.display = "none";
            }

        });

        // Esconder linhas vazias
        cardapio.querySelectorAll(":scope > div").forEach(function (linha) {

            const visiveis = Array.from(
                linha.querySelectorAll("article")
            ).some(produto => produto.style.display !== "none");

            linha.style.display = visiveis ? "" : "none";

        });

        if (mensagem) {
            mensagem.textContent = pesquisa === ""
                ? ""
                : encontrados + " produto(s) encontrado(s)!";
        }
    }

    // Pesquisa enquanto digita
    campoPesquisa.addEventListener("input", function () {
        filtrarProdutos(this.value);
    });

    // Recupera a pesquisa enviada pelo formulário
    const parametros = new URLSearchParams(window.location.search);
    const buscaInicial = parametros.get("q") || "";

    if (buscaInicial) {
        campoPesquisa.value = buscaInicial;
        filtrarProdutos(buscaInicial);
    }

});
