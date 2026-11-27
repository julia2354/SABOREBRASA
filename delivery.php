
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Finalizar pedido - Sabor & Brasa</title>

    <link rel="stylesheet" href="delivery.css">
    <link rel="icon" type="image/x-icon" href="img/favicon.ico">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Alfa+Slab+One&family=Anton+SC&family=Bungee&family=Chango&family=Cinzel:wght@400..900&family=Climate+Crisis:YEAR@1979..2050&family=Comic+Neue:ital,wght@0,300;0,400;0,700;1,300;1,400;1,700&family=Edu+TAS+Beginner&family=Emilys+Candy&family=Fascinate+Inline&family=Kranky&family=Limelight&family=Sancreek&family=Shadows+Into+Light+Two&family=Spicy+Rice&family=Tapestry&display=swap" rel="stylesheet">
</head>

<body>


<header>
    <nav class="nav">

        <img src="img/Logo.png" alt="Logo Sabor & Brasa">

        <form action="pesquisar.php" method="get">
            <input
                type="search"
                class="buscar"
                name="q"
                placeholder="🔍︎ Pesquise no cardápio..."
            >

            <button type="submit">Buscar</button>
        </form>

        <ul class="list">
            <li><a href="index.html">Home</a></li>
            <li><a href="index.html#Promoções">Promoções</a></li>
            <li><a href="cardápio.html">Cardápio</a></li>
            <li><a href="carrinho.html">Carrinho</a></li>
            <li><a href="cadastro.html">Cadastrar</a></li>
            <li><a href="login.html">Login</a></li>
        </ul>

    </nav>
</header>


<main class="finalizacao">

    <h1>Finalizar pedido</h1>
    <p class="subtitulo">Estamos quase lá! Complete seus dados para receber seu pedido.</p>

    <div class="conteudo">

        <!-- DADOS DO CLIENTE -->
        <section class="dados">

            <form action="finalizar_pedido.php" method="POST" id="formPedido">

                <h2>1. Dados pessoais</h2>

                <label for="nome">Nome completo</label>
                <input type="text" id="nome" name="nome"
                       placeholder="Digite seu nome completo" required>

                <label for="telefone">Telefone</label>
                <input type="tel" id="telefone" name="telefone"
                       placeholder="(00) 00000-0000" required>

                <h2>2. Endereço de entrega</h2>

                <label for="cep">CEP</label>
                <input type="text" id="cep" name="cep"
                       placeholder="00000-000" maxlength="9" required>

                <label for="rua">Rua / Avenida</label>
                <input type="text" id="rua" name="rua"
                       placeholder="Nome da rua" required>

                <div class="campos">
                    <div>
                        <label for="numero">Número</label>
                        <input type="text" id="numero" name="numero"
                               placeholder="Nº" required>
                    </div>

                    <div>
                        <label for="bairro">Bairro</label>
                        <input type="text" id="bairro" name="bairro"
                               placeholder="Seu bairro" required>
                    </div>
                </div>

                <label for="complemento">Complemento</label>
                <input type="text" id="complemento" name="complemento"
                       placeholder="Apartamento, casa, referência...">

                <h2>3. Forma de pagamento</h2>

                <div class="pagamentos">

                    <label class="opcao">
                        <input type="radio" name="pagamento" value="Pix" required>
                        <span>💠 Pix</span>
                    </label>

                    <label class="opcao">
                        <input type="radio" name="pagamento" value="Cartão de crédito">
                        <span>💳 Cartão de crédito</span>
                    </label>

                    <label class="opcao">
                        <input type="radio" name="pagamento" value="Cartão de débito">
                        <span>💳 Cartão de débito</span>
                    </label>

                    <label class="opcao">
                        <input type="radio" name="pagamento" value="Dinheiro">
                        <span>💵 Dinheiro</span>
                    </label>

                </div>

                <div id="campoTroco" hidden>
                    <label for="troco">Troco para quanto?</label>
                    <input type="number" id="troco" name="troco"
                           min="0" step="0.01" placeholder="Ex.: 50,00">
                </div>

                <h2>4. Observações</h2>

                <textarea name="observacoes" id="observacoes"
                    placeholder="Alguma informação importante sobre seu pedido?"></textarea>

                <button type="submit" id="confirmarPedido">
                    Confirmar compra
                </button>

            </form>

        </section>

        <!-- RESUMO DO PEDIDO -->
        <aside class="resumo">

            <h2>Resumo da compra</h2>

            <div id="produtosResumo">
                <p>Carregando produtos...</p>
            </div>

            <div class="linha">
                <span>Subtotal:</span>
                <strong id="subtotal">R$ 0,00</strong>
            </div>

            <div class="linha">
                <span>Taxa de entrega:</span>
                <strong id="entrega">A calcular</strong>
            </div>

            <div class="total">
                <span>Total:</span>
                <strong id="total">R$ 0,00</strong>
            </div>

            <a href="carrinho.html">← Voltar ao carrinho</a>

        </aside>

    </div>

</main>

<script src="delivery.js"></script>

</body>
</html>
