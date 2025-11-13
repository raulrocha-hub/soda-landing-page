<?php
get_header('loja');

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
            <div class="product-image-container">
                <div class="product-image-wrapper">
                    <div class="product-image-main">
                        <?php
                        // Imagem principal com loading otimizado
                        if (has_post_thumbnail()) {
                            $image_id = get_post_thumbnail_id();
                            $image_url = wp_get_attachment_image_url($image_id, 'full');
                            $image_alt = get_post_meta($image_id, '_wp_attachment_image_alt', true);
                            
                            echo '<img src="' . esc_url($image_url) . '" 
                                  alt="' . esc_attr($image_alt) . '" 
                                  class="product-image-loading"
                                  onload="this.classList.add(\'product-image-loaded\')">';
                        }
                        ?>
                    </div>
                </div>
                
                <!-- Galeria de miniaturas -->
                <div class="product-gallery-thumbnails">
                    <?php
                    $attachment_ids = $product->get_gallery_image_ids();
                    
                    // Inclui a imagem principal como primeira miniatura
                    if (has_post_thumbnail()) {
                        $main_thumb = wp_get_attachment_image_url(get_post_thumbnail_id(), 'thumbnail');
                        echo '<div class="thumbnail-item active" 
                              onclick="changeProductImage(\'' . esc_url(wp_get_attachment_image_url(get_post_thumbnail_id(), 'full')) . '\', this)">
                              <img src="' . esc_url($main_thumb) . '" alt="Thumbnail"></div>';
                    }
                    
                    // Miniaturas da galeria
                    if ($attachment_ids) {
                        foreach ($attachment_ids as $attachment_id) {
                            $thumb_url = wp_get_attachment_image_url($attachment_id, 'thumbnail');
                            $full_url = wp_get_attachment_image_url($attachment_id, 'full');
                            $alt = get_post_meta($attachment_id, '_wp_attachment_image_alt', true);
                            
                            echo '<div class="thumbnail-item" 
                                  onclick="changeProductImage(\'' . esc_url($full_url) . '\', this)">
                                  <img src="' . esc_url($thumb_url) . '" alt="' . esc_attr($alt) . '"></div>';
                        }
                    }
                    ?>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-6">
            <div class="product-summary ps-lg-4">
                <?php do_action('woocommerce_single_product_summary'); ?>

                <!-- Informações Adicionais Soda Perfeita -->
                <div class="product-benefits mt-4 pt-4">
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
            <?php do_action('woocommerce_after_single_product_summary'); ?>
        </div>
    </div>
</div>

<script>
// Função para trocar a imagem principal
function changeProductImage(imageUrl, element) {
    const mainImage = document.querySelector('.product-image-main img');
    const thumbnails = document.querySelectorAll('.thumbnail-item');
    
    // Remove classe active de todas as miniaturas
    thumbnails.forEach(thumb => {
        thumb.classList.remove('active');
    });
    
    // Adiciona classe active na miniatura clicada
    element.classList.add('active');
    
    // Efeito de transição suave
    mainImage.classList.remove('product-image-loaded');
    
    setTimeout(() => {
        mainImage.src = imageUrl;
        mainImage.onload = () => {
            mainImage.classList.add('product-image-loaded');
        };
    }, 300);
}

// Zoom na imagem (opcional)
document.addEventListener('DOMContentLoaded', function() {
    const imageContainer = document.querySelector('.product-image-container');
    const mainImage = document.querySelector('.product-image-main img');
    
    imageContainer.addEventListener('mousemove', (e) => {
        if (window.innerWidth > 768) { // Só no desktop
            const { left, top, width, height } = imageContainer.getBoundingClientRect();
            const x = (e.clientX - left) / width * 100;
            const y = (e.clientY - top) / height * 100;
            
            mainImage.style.transformOrigin = `${x}% ${y}%`;
            mainImage.style.transform = 'scale(1.8)';
        }
    });
    
    imageContainer.addEventListener('mouseleave', () => {
        mainImage.style.transform = 'scale(1)';
    });
});
</script>

<?php endwhile; ?>

<?php get_footer(); ?>