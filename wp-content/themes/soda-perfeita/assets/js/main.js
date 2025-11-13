// Suavizar transição entre tabs
document.addEventListener('DOMContentLoaded', function() {
    const tabLinks = document.querySelectorAll('.woocommerce-tabs ul.tabs li a');
    
    tabLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Remove classe active de todas as tabs
            document.querySelectorAll('.woocommerce-tabs ul.tabs li').forEach(tab => {
                tab.classList.remove('active');
            });
            
            // Adiciona classe active na tab clicada
            this.parentElement.classList.add('active');
            
            // Encontra o painel correspondente
            const target = this.getAttribute('href');
            const targetPanel = document.querySelector(target);
            
            // Esconde todos os painéis
            document.querySelectorAll('.woocommerce-Tabs-panel').forEach(panel => {
                panel.style.display = 'none';
                panel.style.opacity = '0';
            });
            
            // Mostra o painel alvo com animação
            if (targetPanel) {
                setTimeout(() => {
                    targetPanel.style.display = 'block';
                    setTimeout(() => {
                        targetPanel.style.opacity = '1';
                    }, 50);
                }, 300);
            }
        });
    });
});