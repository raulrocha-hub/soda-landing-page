<?php
/**
 * The template for displaying WooCommerce pages
 */
get_header('loja'); ?>

<div class="container py-5">
    <div class="row">
        <div class="col-12">
            <?php 
            if (function_exists('woocommerce_content')) {
                woocommerce_content();
            } else {
                echo '<div class="alert alert-warning">WooCommerce não está ativo.</div>';
            }
            ?>
        </div>
    </div>
</div>

<?php get_footer(); ?>