<?php
get_header();

while (have_posts()) : the_post(); 
    global $product;
?>

<div class="container py-5">
    <div class="row">
        <!-- Breadcrumb -->
        <div class="col-12 mb-4">
            <?php woocommerce_breadcrumb(); ?>
        </div>

        <!-- Conteúdo do Produto -->
        <div class="col-12 col-lg-6 mb-5 mb-lg-0">
            <?php
            /**
             * Hook: woocommerce_before_single_product_summary.
             *
             * @hooked woocommerce_show_product_sale_flash - 10
             * @hooked woocommerce_show_product_images - 20
             */
            do_action('woocommerce_before_single_product_summary');
            ?>
        </div>

        <div class="col-12 col-lg-6">
            <div class="product-summary ps-lg-4">
                <?php
                /**
                 * Hook: woocommerce_single_product_summary.
                 *
                 * @hooked woocommerce_template_single_title - 5
                 * @hooked woocommerce_template_single_rating - 10
                 * @hooked woocommerce_template_single_price - 10
                 * @hooked woocommerce_template_single_excerpt - 20
                 * @hooked woocommerce_template_single_add_to_cart - 30
                 * @hooked woocommerce_template_single_meta - 40
                 * @hooked woocommerce_template_single_sharing - 50
                 * @hooked WC_Structured_Data::generate_product_data() - 60
                 */
                do_action('woocommerce_single_product_summary');
                ?>

                <!-- Informações Adicionais Soda Perfeita -->
                <div class="product-benefits mt-4 pt-4 border-top">
                    <h6 class="text-success mb-3">
                        <i class="fas fa-star me-2"></i>Vantagens Soda Perfeita
                    </h6>
                    <ul class="list-unstyled small">
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            Preço especial do programa
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            Entrega rápida pelo distribuidor regional
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            Suporte técnico Preshh + DaVinci
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Abas de Informações -->
    <div class="row mt-5">
        <div class="col-12">
            <?php
            /**
             * Hook: woocommerce_after_single_product_summary.
             *
             * @hooked woocommerce_output_product_data_tabs - 10
             * @hooked woocommerce_upsell_display - 15
             * @hooked woocommerce_output_related_products - 20
             */
            do_action('woocommerce_after_single_product_summary');
            ?>
        </div>
    </div>
</div>

<?php endwhile; ?>

<?php get_footer(); ?>