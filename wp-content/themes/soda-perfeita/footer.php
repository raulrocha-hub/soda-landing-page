        </main>

        <footer class="site-footer bg-dark text-white py-5">
            <div class="container">
                <div class="row">
                    <div class="col-md-4">
                        <h5>Soda Perfeita</h5>
                        <p class="text-muted">A Revolução das Bebidas Artesanais no Seu Negócio</p>
                    </div>
                    <div class="col-md-4">
                        <h5>Links Rápidos</h5>
                        <?php
                        wp_nav_menu(array(
                            'theme_location' => 'footer',
                            'container' => false,
                            'menu_class' => 'list-unstyled',
                            'fallback_cb' => false
                        ));
                        ?>
                    </div>
                    <div class="col-md-4">
                        <h5>Contato</h5>
                        <p class="text-muted">Entre em contato conosco</p>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12 text-center">
                        <p class="text-muted mb-0">&copy; <?php echo date('Y'); ?> Soda Perfeita. Todos os direitos reservados.</p>
                    </div>
                </div>
            </div>
        </footer>
    </div>

    <?php wp_footer(); ?>
</body>
</html>