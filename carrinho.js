
(function () {

    const CHAVE_CARRINHO = "saborBrasaCarrinho";
    const API_CARRINHO = "carrinho_api.php";

    const listaCarrinho = document.getElementById("listaCarrinho");
    const totalCarrinho = document.getElementById("totalCarrinho");
    const botaoLimpar = document.getElementById("limparCarrinho");
    const botaoFinalizar = document.getElementById("finalizarPedido");

    let ocupado = false;

    // FORMATAR PREÇOS
    function formatarPreco(valor) {
        return Number(valor || 0).toLocaleString("pt-BR", {
            style: "currency",
            currency: "BRL"
        });
    }

    // COMUNICAÇÃO COM O PHP
    async function requisicaoCarrinho(acao, dados = {}) {

        const metodo = acao === "listar" ? "GET" : "POST";

        const opcoes = {
            method: metodo,
            headers: {
                "Content-Type": "application/json"
            }
        };

        if (metodo === "POST") {
            opcoes.body = JSON.stringify(dados ?? {});
        }

        console.log("Ação:", acao);
        console.log("Método enviado:", metodo);

        console.log("AÇÃO:", acao);
        console.log("DADOS:", dados);
        console.log("MÉTODO:", metodo);

        const resposta = await fetch(
            `${API_CARRINHO}?acao=${encodeURIComponent(acao)}`,
            {
                ...opcoes,
                method: acao === "listar" ? "GET" : "POST"
            }
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

        resultado.produtos = Array.isArray(resultado.produtos)
            ? resultado.produtos
            : [];

        resultado.total = Number(resultado.total) || 0;

        try {
            localStorage.setItem(
                CHAVE_CARRINHO,
                JSON.stringify(resultado.produtos)
            );
        } catch (erro) {
            console.warn("Não foi possível atualizar o localStorage.");
        }

        return resultado;
    }

    // ATUALIZAR BOTÕES DO RESUMO
    function atualizarBotoesResumo(vazio) {

        if (botaoLimpar) {
            botaoLimpar.disabled = vazio;
        }

        if (botaoFinalizar) {
            botaoFinalizar.classList.toggle("desabilitado", vazio);

            botaoFinalizar.setAttribute(
                "aria-disabled",
                vazio ? "true" : "false"
            );
        }
    }

    // MOSTRAR PRODUTOS NA TELA
    function mostrarCarrinho(produtos, total) {

        if (!listaCarrinho || !totalCarrinho) return;

        listaCarrinho.innerHTML = "";
        totalCarrinho.textContent = formatarPreco(total);

        const vazio = !produtos || produtos.length === 0;

        atualizarBotoesResumo(vazio);

        if (vazio) {

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

            imagem.onerror = function () {
                this.style.display = "none";
            };

            const informacoes = document.createElement("div");
            informacoes.className = "informacoes-produto";

            const nome = document.createElement("h2");
            nome.textContent = produto.nome;

            const preco = document.createElement("p");
            preco.textContent = "Preço: " + formatarPreco(produto.preco);

            const subtotal = document.createElement("p");
            subtotal.textContent = "Subtotal: " + formatarPreco(produto.subtotal);

            const quantidade = document.createElement("div");
            quantidade.className = "quantidade";

            const diminuir = criarBotao("-", "diminuir", produto);

            diminuir.setAttribute(
                "aria-label",
                "Diminuir quantidade de " + produto.nome
            );

            const numero = document.createElement("span");
            numero.textContent = produto.quantidade;

            const aumentar = criarBotao("+", "aumentar", produto);

            aumentar.setAttribute(
                "aria-label",
                "Aumentar quantidade de " + produto.nome
            );

            const remover = criarBotao("Remover", "remover", produto);

            quantidade.append(diminuir, numero, aumentar);

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

    // CRIAR BOTÕES DOS PRODUTOS
    function criarBotao(texto, acao, produto) {

        const botao = document.createElement("button");

        botao.type = "button";
        botao.textContent = texto;
        botao.dataset.acao = acao;
        botao.dataset.id = produto.id;
        botao.dataset.quantidade = produto.quantidade;

        return botao;
    }

    // MOSTRAR ERROS
    function mostrarErro(mensagem) {

        if (!listaCarrinho) return;

        const p = document.createElement("p");

        p.textContent = "Não foi possível carregar o carrinho: " + mensagem;

        listaCarrinho.replaceChildren(p);
    }

    // CARREGAR CARRINHO
    async function carregarCarrinho() {

        if (!listaCarrinho) return;

        try {

            const dados = await requisicaoCarrinho("listar");

            mostrarCarrinho(dados.produtos, dados.total);

        } catch (erro) {

            mostrarErro(erro.message);
            atualizarBotoesResumo(true);

            console.error(erro);
        }
    }

    // ADICIONAR PRODUTOS
    document.addEventListener("click", async function (event) {

        const botao = event.target.closest(".adicionar-carrinho");

        if (!botao || botao.dataset.acao) return;

        const artigo = botao.closest("article[data-id]");

        if (!artigo) return;

        const id = Number(artigo.dataset.id);

        if (!id || botao.disabled) return;

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

    // AUMENTAR, DIMINUIR E REMOVER PRODUTOS
    document.addEventListener("click", async function (event) {

        const botao = event.target.closest("button[data-acao]");

        if (!botao) return;

        const acao = botao.dataset.acao;
        const id = Number(botao.dataset.id);

        if (!["aumentar", "diminuir", "remover"].includes(acao) || !id) {
            return;
        }

        if (ocupado) return;

        ocupado = true;
        botao.disabled = true;

        try {

            let dados;

            let novaQuantidade =
                Number(botao.dataset.quantidade) || 0;

            if (acao === "aumentar") novaQuantidade++;

            if (acao === "diminuir") novaQuantidade--;

            if (acao === "remover" || novaQuantidade < 1) {

                dados = await requisicaoCarrinho("remover", {
                    id: id
                });

            } else {

                dados = await requisicaoCarrinho("atualizar", {
                    id: id,
                    quantidade: novaQuantidade
                });
            }

            mostrarCarrinho(dados.produtos, dados.total);

        } catch (erro) {

            alert(erro.message);

            botao.disabled = false;

        } finally {

            ocupado = false;
        }
    });

    // LIMPAR TODOS OS PRODUTOS
    if (botaoLimpar) {

        botaoLimpar.addEventListener("click", async function (event) {

            event.preventDefault();

            if (!confirm("Deseja limpar seu carrinho?")) return;

            botaoLimpar.disabled = true;

            try {

                const dados = await requisicaoCarrinho("limpar", {});

                mostrarCarrinho(dados.produtos, dados.total);

                alert("Carrinho limpo com sucesso!");

            } catch (erro) {

                alert(erro.message);

            } finally {

                botaoLimpar.disabled = false;
            }
        });
    }

    // FINALIZAR PEDIDO
    if (botaoFinalizar) {

        botaoFinalizar.addEventListener("click", function (event) {

            if (
                botaoFinalizar.getAttribute("aria-disabled") === "true"
            ) {

                event.preventDefault();

                alert(
                    "Adicione pelo menos um produto antes de finalizar o pedido."
                );
            }
        });
    }

    // EXECUTAR QUANDO ABRIR A PÁGINA
    carregarCarrinho();

})();
