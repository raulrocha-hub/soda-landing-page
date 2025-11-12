<?php
get_header(); 

// Configurações do catálogo
$columns = get_theme_mod('products_columns', 4);
?>

<div class="container py-5">
    <div class="row">
        <!-- Sidebar de Filtros -->
        <div class="col-lg-3 col-xl-2 mb-4 d-none d-lg-block">
            <div class="sticky-top" style="top: 100px;">
                <?php if (is_active_sidebar('woocommerce-sidebar')) : ?>
                    <?php dynamic_sidebar('woocommerce-sidebar'); ?>
                <?php else : ?>
                    <!-- Filtros Padrão -->
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title h6">Filtrar Produtos</h5>
                            <?php 
                            // Exibe widgets padrão do WooCommerce
                            the_widget('WC_Widget_Product_Categories', array(
                                'title' => 'Categorias',
                                'dropdown' => 0,
                                'count' => 1
                            )); 
                            ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Conteúdo Principal -->
        <div class="col-lg-9 col-xl-10">
            <!-- Header do Catálogo -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-1">
                        <?php 
                        if (is_product_category()) {
                            single_cat_title();
                        } else {
                            echo 'Nossos Produtos';
                        }
                        ?>
                    </h1>
                    <?php woocommerce_result_count(); ?>
                </div>
                
                <div class="d-flex gap-3 align-items-center">
                    <!-- Ordenação -->
                    <?php woocommerce_catalog_ordering(); ?>
                    
                    <!-- Botão Filtros Mobile -->
                    <button class="btn btn-outline-primary d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#filtersOffcanvas">
                        <i class="fas fa-filter"></i> Filtros
                    </button>
                </div>
            </div>

            <!-- Produtos -->
            <?php if (have_posts()) : ?>
                <div class="row">
                    <?php while (have_posts()) : the_post(); ?>
                        <div class="col-6 col-md-4 col-xl-3 mb-4">
                            <?php wc_get_template_part('content', 'product'); ?>
                        </div>
                    <?php endwhile; ?>
                </div>

                <!-- Paginação -->
                <div class="row mt-5">
                    <div class="col-12">
                        <?php
                        woocommerce_pagination();
                        ?>
                    </div>
                </div>

            <?php else : ?>
                <div class="alert alert-info text-center py-5">
                    <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                    <h3>Nenhum produto encontrado</h3>
                    <p class="mb-0">Tente ajustar seus filtros ou buscar outro termo.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Offcanvas Filtros Mobile -->
<div class="offcanvas offcanvas-start d-lg-none" tabindex="-1" id="filtersOffcanvas">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title">Filtros</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body">
        <?php if (is_active_sidebar('woocommerce-sidebar')) : ?>
            <?php dynamic_sidebar('woocommerce-sidebar'); ?>
        <?php else : ?>
            <?php the_widget('WC_Widget_Product_Categories'); ?>
            <?php the_widget('WC_Widget_Price_Filter'); ?>
        <?php endif; ?>
    </div>
</div>

<?php get_footer(); ?>