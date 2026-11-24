

const CHAVE_CARRINHO = "saborBrasaCarrinho";

const API_CARRINHO = "carrinho_api.php";

const listaCarrinho = document.getElementById("listaCarrinho");
const totalCarrinho = document.getElementById("totalCarrinho");

function formatarPreco(valor) {
    return Number(valor).toLocaleString("pt-BR", {
        style: "currency",
        currency: "BRL"
    });
}

// Comunicação com o PHP
async function requisicaoCarrinho(acao, dados = null) {

    const opcoes = {};

    if (dados !== null) {
        opcoes.method = "POST";

        opcoes.headers = {
            "Content-Type": "application/json"
        };

        opcoes.body = JSON.stringify(dados);
    }

    const resposta = await fetch(
        `${API_CARRINHO}?acao=${acao}`,
        opcoes
    );

    const texto = await resposta.text();

    let resultado;

    try {
        resultado = JSON.parse(texto);
    } catch (erro) {
        console.error("Resposta do PHP:", texto);
        throw new Error("O servidor não retornou JSON válido.");
    }

    if (!resposta.ok) {
        throw new Error(resultado.erro || "Erro no carrinho.");
    }

    localStorage.setItem(
        CHAVE_CARRINHO,
        JSON.stringify(resultado.produtos)
    );

    return resultado;
}

// Mostrar produtos na tela
function mostrarCarrinho(produtos, total) {

    if (!listaCarrinho || !totalCarrinho) return;

    listaCarrinho.innerHTML = "";

    totalCarrinho.textContent = formatarPreco(total);

    if (!produtos || produtos.length === 0) {

        listaCarrinho.innerHTML = `
            <div class="carrinho-vazio">
                <h2>Seu carrinho está vazio! 🍔</h2>
                <p>Escolha seus hambúrgueres favoritos.</p>
                <a href="cardápio.html">Ver cardápio</a>
            </div>
        `;

        return;
    }

    produtos.forEach(produto => {

        const artigo = document.createElement("article");

        artigo.className = "produto-carrinho";

        const imagem = document.createElement("img");

        imagem.src = produto.imagem;
        imagem.alt = produto.nome;
        imagem.onerror = function() {
            this.style.display = "none";
        };

        const informacoes = document.createElement("div");

        informacoes.className = "informacoes-produto";

        const nome = document.createElement("h2");

        nome.textContent = produto.nome;

        const preco = document.createElement("p");

        preco.textContent =
            "Preço: " + formatarPreco(produto.preco);

        const subtotal = document.createElement("p");

        subtotal.textContent =
            "Subtotal: " + formatarPreco(produto.subtotal);

        const quantidade = document.createElement("div");

        quantidade.className = "quantidade";

        const diminuir = document.createElement("button");

        diminuir.textContent = "-";
        diminuir.dataset.acao = "diminuir";
        diminuir.dataset.id = produto.id;

        const numero = document.createElement("span");

        numero.textContent = produto.quantidade;

        const aumentar = document.createElement("button");

        aumentar.textContent = "+";
        aumentar.dataset.acao = "aumentar";
        aumentar.dataset.id = produto.id;

        const remover = document.createElement("button");

        remover.textContent = "Remover";
        remover.dataset.acao = "remover";
        remover.dataset.id = produto.id;

        quantidade.append(
            diminuir,
            numero,
            aumentar
        );

        informacoes.append(
            nome,
            preco,
            subtotal,
            quantidade,
            remover
        );

        artigo.append(imagem, informacoes);

        listaCarrinho.appendChild(artigo);

    });

}

// Carregar carrinho
async function carregarCarrinho() {

    if (!listaCarrinho) return;

    try {

        const dados = await requisicaoCarrinho("listar");

        mostrarCarrinho(dados.produtos, dados.total);

    } catch (erro) {

        listaCarrinho.innerHTML =
            "<p>Não foi possível carregar o carrinho: " +
            erro.message + "</p>";

        console.error(erro);

    }
}

// Adicionar produtos no cardápio
document.addEventListener("click", async function(event) {

    const botao = event.target.closest(".adicionar-carrinho");

    if (!botao) return;

    const artigo = botao.closest("article[data-id]");

    if (!artigo) return;

    const id = Number(artigo.dataset.id);

    botao.disabled = true;

    try {

        const dados = await requisicaoCarrinho("adicionar", {
            id: id,
            quantidade: 1
        });

        if (listaCarrinho) {
            mostrarCarrinho(dados.produtos, dados.total);
        }

        alert("🍔 Produto adicionado ao carrinho!");

    } catch (erro) {

        alert(erro.message);

    } finally {

        botao.disabled = false;

    }

});

// Botões de quantidade e remoção
document.addEventListener("click", async function(event) {

    const botao = event.target.closest("button[data-acao]");

    if (!botao) return;

    const acao = botao.dataset.acao;

    const id = Number(botao.dataset.id);

    if (!["aumentar", "diminuir", "remover"].includes(acao)) {
        return;
    }

    try {

        if (acao === "remover") {

            const dados = await requisicaoCarrinho("remover", {
                id: id
            });

            mostrarCarrinho(dados.produtos, dados.total);

            return;
        }

        const dadosAtuais = await requisicaoCarrinho("listar");

        const produto = dadosAtuais.produtos.find(
            item => Number(item.id) === id
        );

        if (!produto) return;

        let novaQuantidade = Number(produto.quantidade);

        if (acao === "aumentar") {
            novaQuantidade++;
        }

        if (acao === "diminuir") {
            novaQuantidade--;
        }

        const dados = await requisicaoCarrinho("atualizar", {
            id: id,
            quantidade: novaQuantidade
        });

        mostrarCarrinho(dados.produtos, dados.total);

    } catch (erro) {

        alert(erro.message);

    }

});

// Limpar todos os produtos
const botaoLimpar = document.getElementById("limparCarrinho");

if (botaoLimpar) {

    botaoLimpar.addEventListener("click", async function() {

        if (!confirm("Deseja limpar seu carrinho?")) return;

        try {

            const dados = await requisicaoCarrinho("limpar");

            mostrarCarrinho(dados.produtos, dados.total);

        } catch (erro) {

            alert(erro.message);

        }

    });

}

// Executar quando abrir a página
carregarCarrinho();
