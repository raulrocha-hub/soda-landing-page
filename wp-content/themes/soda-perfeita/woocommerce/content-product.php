<?php
/**
 * The template for displaying product items
 */
global $product;

// Ensure visibility
if (empty($product) || !$product->is_visible()) {
    return;
}
?>

<div <?php wc_product_class('card product-card h-100 border-0 shadow-sm position-relative'); ?>>
    <!-- Badge de Oferta/Destaque -->
    <?php if ($product->is_on_sale()) : ?>
        <span class="badge bg-danger position-absolute top-0 start-0 m-2">
            <?php esc_html_e('Oferta!', 'soda-perfeita'); ?>
        </span>
    <?php endif; ?>

    <!-- Imagem do Produto -->
    <div class="card-img-top position-relative overflow-hidden">
        <?php
        /**
         * Hook: woocommerce_before_shop_loop_item_title.
         *
         * @hooked woocommerce_show_product_loop_sale_flash - 10
         * @hooked woocommerce_template_loop_product_thumbnail - 10
         */
        do_action('woocommerce_before_shop_loop_item_title');
        ?>
        
        <!-- Overlay de Ações -->
        <div class="product-actions position-absolute top-50 start-50 translate-middle">
            <div class="btn-group" role="group">
                <a href="<?php echo esc_url($product->get_permalink()); ?>" 
                   class="btn btn-primary btn-sm rounded-circle">
                    <i class="fas fa-eye"></i>
                </a>
                <?php
                /**
                 * Hook: woocommerce_after_shop_loop_item.
                 *
                 * @hooked woocommerce_template_loop_add_to_cart - 10
                 */
                do_action('woocommerce_after_shop_loop_item');
                ?>
            </div>
        </div>
    </div>

    <!-- Informações do Produto -->
    <div class="card-body d-flex flex-column">
        <!-- Categoria -->
        <div class="product-categories mb-1">
            <?php
            $categories = get_the_terms($product->get_id(), 'product_cat');
            if ($categories && !is_wp_error($categories)) {
                $category = reset($categories);
                echo '<small class="text-muted">' . esc_html($category->name) . '</small>';
            }
            ?>
        </div>

        <!-- Título -->
        <h3 class="product-title h6 mb-2">
            <a href="<?php echo esc_url($product->get_permalink()); ?>" 
               class="text-decoration-none text-dark stretched-link">
                <?php echo esc_html($product->get_name()); ?>
            </a>
        </h3>

        <!-- Avaliações -->
        <div class="product-rating mb-2">
            <?php if ($product->get_average_rating() > 0) : ?>
                <div class="woocommerce-product-rating">
                    <?php echo wc_get_rating_html($product->get_average_rating()); ?>
                    <span class="text-muted small">
                        (<?php echo $product->get_review_count(); ?>)
                    </span>
                </div>
            <?php endif; ?>
        </div>

        <!-- Preço -->
        <div class="product-price mt-auto">
            <?php if ($product->get_price_html()) : ?>
                <span class="price h6 text-primary mb-0">
                    <?php echo $product->get_price_html(); ?>
                </span>
            <?php endif; ?>
        </div>
    </div>

    <!-- Botão Adicionar ao Carrinho (Footer) -->
    <div class="card-footer bg-transparent border-0 pt-0">
        <?php
        echo apply_filters('woocommerce_loop_add_to_cart_link',
            sprintf('<a href="%s" data-quantity="%s" class="%s btn btn-outline-primary w-100" %s>%s</a>',
                esc_url($product->add_to_cart_url()),
                1,
                'btn btn-outline-primary w-100',
                $product->is_purchasable() && $product->is_in_stock() ? '' : 'disabled',
                $product->is_purchasable() && $product->is_in_stock() ? 
                    '<i class="fas fa-cart-plus me-2"></i>Adicionar' : 'Fora de Estoque'
            ),
        $product);
        ?>
    </div>
</div>

<style>
.product-card {
    transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
}

.product-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.15) !important;
}

.product-actions {
    opacity: 0;
    transition: opacity 0.3s ease;
}

.product-card:hover .product-actions {
    opacity: 1;
}

.product-card .card-img-top img {
    transition: transform 0.3s ease;
}

.product-card:hover .card-img-top img {
    transform: scale(1.05);
}
</style>