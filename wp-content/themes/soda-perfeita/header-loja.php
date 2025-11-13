<?php
/**
 * Header específico para a loja
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="profile" href="https://gmpg.org/xfn/11">
    <?php wp_head(); ?>

    <!-- CSS específico da loja -->
    <style>
        .header-loja {
            padding: 6px 0px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            position: sticky;
            top: 0;
            z-index: 1000;
            background: #0054a3 url('<?php echo get_template_directory_uri(); ?>/imgs/header-crop.png') no-repeat right center;
            background-size: cover;
        }

        .header-loja .logo a {
            color: white;
            font-size: 24px;
            font-weight: 700;
            text-decoration: none;
        }

        .header-loja .menu-loja {
            display: flex;
            list-style: none;
            margin: 0;
            padding: 0;
            gap: 20px;
        }

        .header-loja .menu-loja a {
            color: white;
            text-decoration: none;
            font-weight: 600;
            transition: opacity 0.3s ease;
        }

        .header-loja .menu-loja a:hover {
            opacity: 0.8;
        }

        /* ===== BARRA DE BUSCA POSICIONADA ABSOLUTA ===== */
        .search-container {
            position: absolute;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
            width: 400px;
            z-index: 1001;
        }

        .search-form {
            display: flex;
            background: white;
            border-radius: 25px;
            overflow: hidden;
            box-shadow: 0 2px 15px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }

        .search-form:focus-within {
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
            transform: translateY(-1px);
        }

        .search-field {
            flex: 1;
            border: none;
            padding: 12px 20px;
            font-size: 14px;
            outline: none;
            background: transparent;
        }

        .search-field::placeholder {
            color: #6c757d;
        }

        .search-submit {
            background: linear-gradient(135deg, #28a745, #20c997);
            border: none;
            padding: 12px 20px;
            color: white;
            cursor: pointer;
            transition: all 0.3s ease;
            min-width: 50px;
        }

        .search-submit:hover {
            background: linear-gradient(135deg, #20c997, #28a745);
        }

        .search-results {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
            margin-top: 10px;
            max-height: 400px;
            overflow-y: auto;
            display: none;
            z-index: 1002;
        }

        .search-results.active {
            display: block;
            animation: fadeIn 0.3s ease;
        }

        .search-result-item {
            display: flex;
            align-items: center;
            padding: 15px;
            border-bottom: 1px solid #f1f3f4;
            text-decoration: none;
            color: #2c3e50;
            transition: background-color 0.3s ease;
        }

        .search-result-item:hover {
            background-color: #f8f9fa;
        }

        .search-result-item:last-child {
            border-bottom: none;
        }

        .search-result-image {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 8px;
            margin-right: 15px;
        }

        .search-result-info {
            flex: 1;
        }

        .search-result-name {
            font-weight: 600;
            font-size: 14px;
            margin-bottom: 5px;
        }

        .search-result-price {
            color: #28a745;
            font-weight: 700;
            font-size: 14px;
        }

        .search-loading {
            padding: 20px;
            text-align: center;
            color: #6c757d;
        }

        .search-loading::after {
            content: '';
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 2px solid #f3f3f3;
            border-top: 2px solid #28a745;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-left: 10px;
        }

        .no-results {
            padding: 20px;
            text-align: center;
            color: #6c757d;
        }

        /* ===== ÍCONES DO HEADER ===== */
        .header-actions {
            display: flex;
            align-items: center;
            gap: 1.5rem;
            justify-content: flex-end;
        }

        .cart-icon-wrapper {
            position: relative;
        }

        .cart-count-badge {
            background: #ff6b6b;
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: bold;
            position: absolute;
            top: -8px;
            right: -8px;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }

        .header-actions a {
            color: white;
            text-decoration: none;
            transition: all 0.3s ease;
            position: relative;
        }

        .header-actions a:hover {
            transform: translateY(-2px);
        }

        /* ===== DROPDOWN DO CARRINHO ===== */
        .cart-dropdown {
            position: relative;
        }

        .mini-cart-dropdown {
            position: absolute;
            top: 100%;
            right: 0;
            width: 320px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
            padding: 0;
            z-index: 1000;
            opacity: 0;
            visibility: hidden;
            transform: translateY(10px);
            transition: all 0.3s ease;
            margin-top: 10px;
        }

        .cart-dropdown:hover .mini-cart-dropdown {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .mini-cart-header {
            background: linear-gradient(135deg, #0054a3, #007bff);
            color: white;
            padding: 15px 20px;
            border-radius: 12px 12px 0 0;
            font-weight: 600;
        }

        .mini-cart-content {
            max-height: 300px;
            overflow-y: auto;
            padding: 15px;
        }

        .mini-cart-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #f1f3f4;
        }

        .mini-cart-item:last-child {
            border-bottom: none;
        }

        .item-name {
            flex: 1;
            font-size: 14px;
            color: #2c3e50;
        }

        .item-quantity {
            background: #0054a3;
            color: white;
            border-radius: 12px;
            padding: 2px 8px;
            font-size: 12px;
            font-weight: 600;
            margin: 0 10px;
        }

        .item-price {
            font-weight: 600;
            color: #28a745;
            font-size: 14px;
        }

        .mini-cart-total {
            margin: 15px 0;
            padding: 15px;
            border-top: 2px solid #e9ecef;
            text-align: center;
            background: #f8f9fa;
        }

        .mini-cart-actions {
            padding: 15px;
            display: flex;
            gap: 10px;
        }

        .view-cart-btn, .checkout-btn {
            flex: 1;
            text-align: center;
            padding: 12px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            font-size: 14px;
        }

        .view-cart-btn {
            background: #6c757d;
            color: white;
        }

        .checkout-btn {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
        }

        .view-cart-btn:hover, .checkout-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        .mobile-search-container{
            display: none;
        }
        .empty-cart {
            text-align: center;
            color: #6c757d;
            padding: 30px 20px;
            margin: 0;
        }

        /* ===== RESPONSIVIDADE ===== */
        @media (max-width: 768px) {
.mobile-search-container{
            display: block;
        }            .header-loja .container {
                padding: 0 15px !important;
            }
            
            .header-loja .row {
                align-items: center !important;
            }
            
            .logo, .header-actions {
                display: flex;
                align-items: center;
                height: 60px;
            }
            
            .logo {
                justify-content: flex-start;
            }
            
            .header-actions {
                justify-content: flex-end;
            }
            
            .navbar-brand img {
                max-height: 40px;
                width: auto;
            }
            
            .mini-cart-dropdown {
                width: 280px;
                right: -10px;
            }
            
            /* Busca no mobile - escondemos a busca desktop */
            .search-container {
                display: none;
            }
            
            /* Busca mobile expandível */
            .mobile-search-container {
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                background: #0054a3;
                padding: 15px;
                z-index: 1003;
                box-shadow: 0 2px 10px rgba(0,0,0,0.1);
                transform: translateY(-100%);
                transition: transform 0.3s ease;
            }
            
            .mobile-search-container.active {
                transform: translateY(0);
            }
            
            .mobile-search-form {
                display: flex;
                background: white;
                border-radius: 25px;
                overflow: hidden;
            }
            
            .mobile-search-field {
                flex: 1;
                border: none;
                padding: 12px 15px;
                font-size: 16px;
                outline: none;
            }
            
            .mobile-search-submit {
                background: #28a745;
                border: none;
                padding: 12px 15px;
                color: white;
                cursor: pointer;
            }
            
            .mobile-search-close {
                background: none;
                border: none;
                color: white;
                font-size: 20px;
                margin-left: 10px;
                cursor: pointer;
            }
            
            /* Esconde o menu principal no mobile */
            .header-loja .col-md-6 {
                display: none;
            }
            
            /* Ajusta as colunas para mobile */
            .header-loja .col-12 {
                flex: 0 0 50%;
                max-width: 50%;
            }
        }

        @media (max-width: 576px) {
            .header-actions {
                gap: 1rem;
            }
            
            .mini-cart-dropdown {
                width: 250px;
            }
            
            .header-actions a i {
                font-size: 1.2rem !important;
            }
            
            .search-container {
                width: 300px;
            }
        }

        /* ===== ANIMAÇÕES ===== */
        .cart-bounce {
            animation: cartBounce 0.6s ease;
        }

        @keyframes cartBounce {
            0%, 20%, 60%, 100% { transform: translateY(0); }
            40% { transform: translateY(-5px); }
            80% { transform: translateY(-3px); }
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>

<body <?php body_class(); ?>>

    <header class="header-loja">
        <div class="container" style="padding: 0px 22px;">
            <div class="row align-items-center" style="position: relative; min-height: 60px;">
                <!-- Logo -->
                <div class="col-6 col-md-3">
                    <div class="logo">
                        <a href="<?php echo home_url('/'); ?>">
                            <?php if (has_custom_logo()): ?>
                                <div class="navbar-brand">
                                    <?php the_custom_logo(); ?>
                                </div>
                            <?php else: ?>
                                <a class="navbar-brand" href="<?php echo home_url(); ?>">
                                    <strong>Soda Perfeita</strong>
                                </a>
                            <?php endif; ?>
                        </a>
                    </div>
                </div>

                <!-- Menu Principal da Loja - Desktop -->
                <div class="col-12 col-md-6 d-none d-md-block">
                    <nav class="main-navigation">
                        <?php
                        wp_nav_menu(array(
                            'theme_location' => 'menu-loja',
                            'menu_class' => 'menu-loja justify-content-center',
                            'container' => false,
                            'fallback_cb' => false
                        ));
                        ?>
                    </nav>
                </div>

                <!-- Ícones de Ação -->
                <div class="col-6 col-md-3">
                    <div class="header-actions">
                        <!-- Carrinho com Dropdown -->
                        <div class="cart-dropdown">
                            <a href="<?php echo wc_get_cart_url(); ?>" class="cart-icon-wrapper position-relative">
                                <i class="fas fa-shopping-cart fa-lg"></i>
                                <?php
                                $count = WC()->cart->get_cart_contents_count();
                                if ($count > 0) {
                                    echo '<span class="cart-count-badge">' . $count . '</span>';
                                }
                                ?>
                            </a>
                            
                            <!-- Dropdown do Carrinho -->
                            <div class="mini-cart-dropdown">
                                <div class="mini-cart-header">
                                    <i class="fas fa-shopping-cart me-2"></i>Seu Carrinho
                                </div>
                                <div class="mini-cart-content">
                                    <?php
                                    $cart_items = WC()->cart->get_cart();
                                    if (sizeof($cart_items) > 0) {
                                        foreach ($cart_items as $cart_item_key => $cart_item) {
                                            $_product = apply_filters('woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key);
                                            $product_id = apply_filters('woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key);
                                            
                                            if ($_product && $_product->exists() && $cart_item['quantity'] > 0) {
                                                $product_name = apply_filters('woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key);
                                                $thumbnail = apply_filters('woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key);
                                                $product_price = apply_filters('woocommerce_cart_item_price', WC()->cart->get_product_price($_product), $cart_item, $cart_item_key);
                                                ?>
                                                <div class="mini-cart-item">
                                                    <div class="item-name"><?php echo esc_html($product_name); ?></div>
                                                    <span class="item-quantity"><?php echo $cart_item['quantity']; ?>x</span>
                                                    <div class="item-price"><?php echo $product_price; ?></div>
                                                </div>
                                                <?php
                                            }
                                        }
                                    } else {
                                        echo '<p class="empty-cart"><i class="fas fa-shopping-cart fa-2x mb-3"></i><br>Seu carrinho está vazio</p>';
                                    }
                                    ?>
                                </div>
                                
                                <?php if (sizeof($cart_items) > 0): ?>
                                <div class="mini-cart-total">
                                    <strong>Total: <?php echo WC()->cart->get_cart_total(); ?></strong>
                                </div>
                                <div class="mini-cart-actions">
                                    <a href="<?php echo wc_get_cart_url(); ?>" class="view-cart-btn">Ver Carrinho</a>
                                    <a href="<?php echo wc_get_checkout_url(); ?>" class="checkout-btn">Finalizar</a>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Conta -->
                        <a href="<?php echo get_permalink(get_option('woocommerce_myaccount_page_id')); ?>" class="d-none d-sm-inline">
                            <i class="fas fa-user fa-lg"></i>
                        </a>

                        <!-- Ícone de Busca Mobile -->
                        <a href="#" class="d-md-none" id="mobile-search-toggle">
                            <i class="fas fa-search fa-lg"></i>
                        </a>
                    </div>
                </div>

                <!-- Barra de Busca - Desktop (Posicionada Absolutamente) -->
                <div class="search-container d-none d-md-block">
                    <form role="search" method="get" class="search-form" action="<?php echo esc_url(home_url('/')); ?>">
                        <input type="hidden" name="post_type" value="product" />
                        <input type="search" 
                               class="search-field" 
                               placeholder="Buscar produtos..." 
                               value="<?php echo get_search_query(); ?>" 
                               name="s" 
                               id="search-input"
                               autocomplete="off" />
                        <button type="submit" class="search-submit">
                            <i class="fas fa-search"></i>
                        </button>
                    </form>
                    <div class="search-results" id="search-results"></div>
                </div>
            </div>
        </div>

        <!-- Barra de Busca - Mobile (Overlay) -->
        <div class="mobile-search-container" id="mobile-search-container">
            <div class="container">
                <div class="d-flex align-items-center">
                    <form role="search" method="get" class="mobile-search-form" action="<?php echo esc_url(home_url('/')); ?>" style="flex: 1;">
                        <input type="hidden" name="post_type" value="product" />
                        <input type="search" 
                               class="mobile-search-field" 
                               placeholder="Buscar produtos..." 
                               value="<?php echo get_search_query(); ?>" 
                               name="s" 
                               id="mobile-search-input"
                               autocomplete="off" />
                        <button type="submit" class="mobile-search-submit">
                            <i class="fas fa-search"></i>
                        </button>
                    </form>
                    <button class="mobile-search-close" id="mobile-search-close">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="search-results" id="mobile-search-results" style="margin-top: 10px;"></div>
            </div>
        </div>
    </header>

    <main>

<script>
// JavaScript para busca em tempo real e funcionalidades
document.addEventListener('DOMContentLoaded', function() {
    const cartDropdown = document.querySelector('.cart-dropdown');
    const cartIcon = document.querySelector('.cart-icon-wrapper');
    const searchInput = document.getElementById('search-input');
    const mobileSearchInput = document.getElementById('mobile-search-input');
    const searchResults = document.getElementById('search-results');
    const mobileSearchResults = document.getElementById('mobile-search-results');
    const mobileSearchToggle = document.getElementById('mobile-search-toggle');
    const mobileSearchContainer = document.getElementById('mobile-search-container');
    const mobileSearchClose = document.getElementById('mobile-search-close');
    
    let searchTimeout;

    // Fechar dropdown ao clicar fora
    document.addEventListener('click', function(e) {
        if (!cartDropdown.contains(e.target)) {
            cartDropdown.querySelector('.mini-cart-dropdown').style.opacity = '0';
            cartDropdown.querySelector('.mini-cart-dropdown').style.visibility = 'hidden';
        }
        
        // Fechar resultados de busca ao clicar fora
        if (!e.target.closest('.search-container')) {
            searchResults.classList.remove('active');
        }
        if (!e.target.closest('.mobile-search-container')) {
            mobileSearchResults.classList.remove('active');
        }
    });
    
    // Animação ao adicionar item no carrinho
    function animateCartAdd() {
        cartIcon.classList.add('cart-bounce');
        setTimeout(() => {
            cartIcon.classList.remove('cart-bounce');
        }, 600);
    }
    
    // Atualizar contador do carrinho
    jQuery(document.body).on('added_to_cart', function() {
        animateCartAdd();
    });
    
    // Mobile: toggle do dropdown no touch
    if (window.innerWidth < 768) {
        cartIcon.addEventListener('click', function(e) {
            e.preventDefault();
            const dropdown = cartDropdown.querySelector('.mini-cart-dropdown');
            const isVisible = dropdown.style.visibility === 'visible';
            
            if (isVisible) {
                dropdown.style.opacity = '0';
                dropdown.style.visibility = 'hidden';
            } else {
                dropdown.style.opacity = '1';
                dropdown.style.visibility = 'visible';
                dropdown.style.transform = 'translateY(0)';
            }
        });
    }

    // Toggle da busca mobile
    if (mobileSearchToggle && mobileSearchContainer) {
        mobileSearchToggle.addEventListener('click', function(e) {
            e.preventDefault();
            mobileSearchContainer.classList.add('active');
            setTimeout(() => {
                mobileSearchInput.focus();
            }, 100);
        });
    }

    // Fechar busca mobile
    if (mobileSearchClose) {
        mobileSearchClose.addEventListener('click', function(e) {
            e.preventDefault();
            mobileSearchContainer.classList.remove('active');
            mobileSearchResults.classList.remove('active');
        });
    }

    // Busca em tempo real
    function setupSearch(inputElement, resultsElement) {
        if (!inputElement) return;

        inputElement.addEventListener('input', function(e) {
            const searchTerm = e.target.value.trim();
            
            // Limpar timeout anterior
            clearTimeout(searchTimeout);
            
            if (searchTerm.length < 2) {
                resultsElement.classList.remove('active');
                return;
            }
            
            // Mostrar loading
            resultsElement.innerHTML = '<div class="search-loading">Buscando...</div>';
            resultsElement.classList.add('active');
            
            // Debounce para evitar muitas requisições
            searchTimeout = setTimeout(() => {
                performSearch(searchTerm, resultsElement);
            }, 300);
        });

        // Buscar ao pressionar Enter
        inputElement.addEventListener('keypress', function(e) {
            if (e.key === 'Enter' && this.value.trim().length > 0) {
                this.form.submit();
            }
        });
    }

    // Executar busca via AJAX
    function performSearch(searchTerm, resultsElement) {
        fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams({
                action: 'product_search',
                search_term: searchTerm
            })
        })
        .then(response => response.json())
        .then(data => {
            displaySearchResults(data, resultsElement);
        })
        .catch(error => {
            console.error('Erro na busca:', error);
            resultsElement.innerHTML = '<div class="no-results">Erro ao buscar produtos</div>';
        });
    }

    // Exibir resultados da busca
    function displaySearchResults(products, resultsElement) {
        if (!products || products.length === 0) {
            resultsElement.innerHTML = '<div class="no-results">Nenhum produto encontrado</div>';
            return;
        }

        let html = '';
        products.forEach(product => {
            html += `
                <a href="${product.permalink}" class="search-result-item">
                    <img src="${product.image}" alt="${product.name}" class="search-result-image" onerror="this.style.display='none'">
                    <div class="search-result-info">
                        <div class="search-result-name">${product.name}</div>
                        <div class="search-result-price">${product.price}</div>
                    </div>
                </a>
            `;
        });

        resultsElement.innerHTML = html;
    }

    // Inicializar busca para desktop e mobile
    setupSearch(searchInput, searchResults);
    setupSearch(mobileSearchInput, mobileSearchResults);
});

// Fechar resultados ao rolar a página
window.addEventListener('scroll', function() {
    const searchResults = document.getElementById('search-results');
    const mobileSearchResults = document.getElementById('mobile-search-results');
    
    if (searchResults) searchResults.classList.remove('active');
    if (mobileSearchResults) mobileSearchResults.classList.remove('active');
});

// Fechar busca mobile ao pressionar ESC
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        const mobileSearchContainer = document.getElementById('mobile-search-container');
        if (mobileSearchContainer) {
            mobileSearchContainer.classList.remove('active');
        }
    }
});
</script>
<?php