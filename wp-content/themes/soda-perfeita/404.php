<?php get_header(); ?>

<div class="container py-5">
    <div class="row justify-content-center text-center">
        <div class="col-12 col-md-8 col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body py-5">
                    <i class="fas fa-search fa-5x text-muted mb-4"></i>
                    
                    <h1 class="h2 text-muted mb-3">Página Não Encontrada</h1>
                    
                    <p class="text-muted mb-4">
                        Desculpe, a página que você está procurando não existe ou foi movida.
                    </p>

                    <div class="d-flex gap-3 justify-content-center flex-wrap">
                        <a href="<?php echo esc_url(home_url('/')); ?>" class="btn btn-primary">
                            <i class="fas fa-home me-2"></i>Página Inicial
                        </a>
                        
                        <a href="<?php echo esc_url(home_url('/loja')); ?>" class="btn btn-outline-primary">
                            <i class="fas fa-store me-2"></i>Ir para a Loja
                        </a>
                    </div>

                    <!-- Busca -->
                    <div class="mt-4">
                        <p class="text-muted small mb-2">Ou tente uma busca:</p>
                        <?php get_search_form(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php get_footer(); ?>