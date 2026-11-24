const pesquisa = document.getElementById("pesquisa");

if (pesquisa) {
    pesquisa.addEventListener("input", function() {
        // Mantenha aqui o código original da pesquisa.
    });
}

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



const CHAVE_CARRINHO = "saborBrasaCarrinho";

// Atualiza o carrinho salvo no navegador
async function atualizarCarrinhoLocal() {
    const resposta = await fetch("carrinho_api.php?acao=listar");

    if (!resposta.ok) {
        throw new Error("Erro ao consultar o carrinho.");
    }

    const dados = await resposta.json();

    localStorage.setItem(
        CHAVE_CARRINHO,
        JSON.stringify(dados.produtos)
    );

    return dados;
}

// Adicionar produtos
document.addEventListener("click", async function(event) {

    const botao = event.target.closest(".adicionar-carrinho");

    if (!botao) return;

    // Localiza qualquer artigo com data-id
    const artigo = botao.closest("article[data-id]");

    if (!artigo) return;

    const id = Number(artigo.dataset.id);

    botao.disabled = true;

    try {

        const resposta = await fetch(
            "carrinho_api.php?acao=adicionar",
            {
                method: "POST",

                headers: {
                    "Content-Type": "application/json"
                },

                body: JSON.stringify({
                    id: id,
                    quantidade: 1
                })
            }
        );

        const dados = await resposta.json();

        if (!resposta.ok) {
            throw new Error(dados.erro || "Não foi possível adicionar.");
        }

        localStorage.setItem(
            CHAVE_CARRINHO,
            JSON.stringify(dados.produtos)
        );

        alert("🍔 Produto adicionado ao carrinho!");

    } catch (erro) {

        alert(erro.message);

    } finally {

        botao.disabled = false;

    }

});
