document.addEventListener('DOMContentLoaded', function () {
    function alterarTextoBotaoCheckout() {
        var labels = document.querySelectorAll(
            '.wc-block-cart__submit-button .wc-block-components-button__text'
        );

        if (!labels.length) {
            return;
        }

        labels.forEach(function (el) {
            var txt = el.textContent.trim();
            if (txt === 'Proceed to Checkout' || txt === 'Proceed to checkout') {
                el.textContent = 'Finalizar compra';
            }
        });

        // Marca que já pode exibir o texto
        document.documentElement.classList.add('soda-checkout-ready');
    }

    alterarTextoBotaoCheckout();

    const observer = new MutationObserver(function () {
        alterarTextoBotaoCheckout();
    });

    observer.observe(document.body, {
        childList: true,
        subtree: true
    });
});
