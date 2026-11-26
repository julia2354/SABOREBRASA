
document.addEventListener("DOMContentLoaded", function () {

    const produtosResumo = document.getElementById("produtosResumo");
    const subtotalElemento = document.getElementById("subtotal");
    const entregaElemento = document.getElementById("entrega");
    const totalElemento = document.getElementById("total");

    const formulario = document.getElementById("formPedido");
    const botaoConfirmar = document.getElementById("confirmarPedido");

    const campoTroco = document.getElementById("campoTroco");
    const troco = document.getElementById("troco");

    let carrinhoValido = false;

    function formatarPreco(valor) {
        return Number(valor || 0).toLocaleString("pt-BR", {
            style: "currency",
            currency: "BRL"
        });
    }

    // BUSCAR PRODUTOS DO CARRINHO
    async function carregarResumo() {

        try {

            const resposta = await fetch("carrinho_api.php?acao=listar");

            if (!resposta.ok) {
                throw new Error("Erro ao consultar o carrinho.");
            }

            const dados = await resposta.json();

            const produtos = Array.isArray(dados.produtos)
                ? dados.produtos
                : [];

            const subtotal = Number(dados.total) || 0;

            produtosResumo.innerHTML = "";

            if (produtos.length === 0) {

                produtosResumo.innerHTML = `
                    <p>Seu carrinho está vazio!</p>
                    <a href="cardápio.html">Voltar ao cardápio</a>
                `;

                subtotalElemento.textContent = formatarPreco(0);
                entregaElemento.textContent = "A calcular";
                totalElemento.textContent = formatarPreco(0);

                botaoConfirmar.disabled = true;

                return;
            }

            carrinhoValido = true;

            produtos.forEach(produto => {

                const item = document.createElement("div");

                item.className = "item-resumo";

                const nome = document.createElement("span");

                nome.textContent =
                    `${produto.nome} x ${produto.quantidade}`;

                const preco = document.createElement("strong");

                preco.textContent = formatarPreco(produto.subtotal);

                item.append(nome, preco);

                produtosResumo.appendChild(item);

            });

            subtotalElemento.textContent = formatarPreco(subtotal);

            // A taxa ainda precisa ser definida pelo estabelecimento.
            entregaElemento.textContent = "A combinar";

            // O total abaixo representa apenas o subtotal.
            totalElemento.textContent = formatarPreco(subtotal);

        } catch (erro) {

            console.error(erro);

            produtosResumo.textContent =
                "Não foi possível carregar seu pedido. Tente novamente.";

            botaoConfirmar.disabled = true;

        }
    }

    // MOSTRAR CAMPO DE TROCO
    document.querySelectorAll('input[name="pagamento"]')
        .forEach(opcao => {

            opcao.addEventListener("change", function () {

                const dinheiro = this.value === "Dinheiro";

                campoTroco.hidden = !dinheiro;

                troco.required = dinheiro;

                if (!dinheiro) {
                    troco.value = "";
                }

            });

        });

    // NÃO PERMITIR FINALIZAR COM CARRINHO VAZIO
    formulario.addEventListener("submit", function (event) {

        if (!carrinhoValido) {

            event.preventDefault();

            alert("Adicione produtos ao carrinho antes de finalizar.");

            return;
        }

        botaoConfirmar.disabled = true;
        botaoConfirmar.textContent = "Enviando pedido...";

    });

    carregarResumo();

});
