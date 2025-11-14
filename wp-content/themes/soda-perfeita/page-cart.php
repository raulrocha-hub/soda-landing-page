<?php
/**
 * Template Name: Carrinho
 * Template for cart page
 */

get_header('loja');
?>

<div class="container py-5">
    <div class="row">
        <div class="col-12">
            <!-- Breadcrumb -->
            <div class="mb-4">
                <?php woocommerce_breadcrumb(); ?>
            </div>

            <h1 class="mb-4">Seu Carrinho</h1>
            
            <?php
            if (function_exists('woocommerce_content')) {
                // Usar o shortcode do carrinho do WooCommerce
                echo do_shortcode('[woocommerce_cart]');
            } else {
                echo '<div class="alert alert-warning">WooCommerce não está ativo.</div>';
            }
            ?>
        </div>
    </div>
</div>

<style>
/* Estilos específicos para a página do carrinho */
.woocommerce-cart {
    background: #fff;
    border-radius: 16px;
    padding: 30px;
    box-shadow: 0 5px 25px rgba(0,0,0,0.08);
}

.woocommerce-cart h2 {
    color: #2c3e50;
    font-weight: 700;
    margin-bottom: 25px;
    font-size: 24px;
}

.woocommerce-cart-form {
    margin-bottom: 40px;
}

.shop_table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 30px;
}

.shop_table th {
    background: linear-gradient(135deg, #0054a3, #007bff);
    color: white;
    padding: 15px;
    text-align: left;
    font-weight: 600;
}

.shop_table td {
    padding: 20px 15px;
    border-bottom: 1px solid #e9ecef;
}

.shop_table tr:hover {
    background-color: #f8f9fa;
}

.product-thumbnail img {
    width: 80px;
    height: 80px;
    object-fit: cover;
    border-radius: 8px;
}

.product-name a {
    color: #2c3e50;
    text-decoration: none;
    font-weight: 600;
    transition: color 0.3s ease;
}

.product-name a:hover {
    color: #0054a3;
}

.quantity {
    display: flex;
    align-items: center;
    gap: 10px;
}

.quantity input {
    width: 60px;
    text-align: center;
    border: 2px solid #e9ecef;
    border-radius: 8px;
    padding: 8px;
}

.actions {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 15px;
    margin-top: 20px;
}

.coupon {
    display: flex;
    gap: 10px;
}

.coupon input {
    padding: 12px 15px;
    border: 2px solid #e9ecef;
    border-radius: 8px;
    min-width: 200px;
}

.button {
    background: linear-gradient(135deg, #28a745, #20c997);
    color: white;
    border: none;
    padding: 12px 25px;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    text-decoration: none;
    display: inline-block;
}

.button:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(40, 167, 69, 0.3);
}

.button.alt {
    background: linear-gradient(135deg, #0054a3, #007bff);
}

.cart-collaterals {
    background: #f8f9fa;
    border-radius: 12px;
    padding: 30px;
}

.cart_totals h2 {
    color: #2c3e50;
    margin-bottom: 20px;
}

.cart_totals table {
    width: 100%;
    margin-bottom: 20px;
}

.cart_totals th,
.cart_totals td {
    padding: 12px 0;
    border-bottom: 1px solid #e9ecef;
}

.cart_totals .order-total {
    font-size: 18px;
    font-weight: 700;
    color: #28a745;
}

.wc-proceed-to-checkout {
    margin-top: 20px;
}

.wc-proceed-to-checkout .checkout-button {
    width: 100%;
    text-align: center;
    font-size: 16px;
    padding: 15px;
}

/* Responsividade */
@media (max-width: 768px) {
    .woocommerce-cart {
        padding: 20px;
    }
    
    .shop_table thead {
        display: none;
    }
    
    .shop_table tr {
        display: block;
        margin-bottom: 20px;
        border: 1px solid #e9ecef;
        border-radius: 12px;
        padding: 15px;
    }
    
    .shop_table td {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 0;
        border: none;
    }
    
    .shop_table td::before {
        content: attr(data-title);
        font-weight: 600;
        color: #2c3e50;
    }
    
    .actions {
        flex-direction: column;
        align-items: stretch;
    }
    
    .coupon {
        flex-direction: column;
    }
    
    .coupon input {
        min-width: auto;
    }
}
/* Estilos globais para todas as páginas da loja */
.container.py-5 {
    padding-top: 2rem !important;
    padding-bottom: 2rem !important;
}

.woocommerce-breadcrumb {
    background: #f8f9fa;
    padding: 15px 20px;
    border-radius: 12px;
    margin-bottom: 2rem;
}

.woocommerce-breadcrumb a {
    color: #0054a3;
    text-decoration: none;
}

.woocommerce-breadcrumb a:hover {
    text-decoration: underline;
}

h1 {
    color: #2c3e50;
    font-weight: 700;
    font-size: 2.5rem;
    margin-bottom: 2rem;
}

/* Loading states */
.woocommerce-loading {
    position: relative;
}

.woocommerce-loading::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 30px;
    height: 30px;
    border: 3px solid #f3f3f3;
    border-top: 3px solid #0054a3;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: translate(-50%, -50%) rotate(0deg); }
    100% { transform: translate(-50%, -50%) rotate(360deg); }
}

/* Alertas comuns */
.woocommerce-message,
.woocommerce-info,
.woocommerce-error {
    padding: 15px 20px;
    border-radius: 8px;
    margin-bottom: 20px;
    border-left: 4px solid;
}

.woocommerce-message {
    background: #d4edda;
    color: #155724;
    border-left-color: #28a745;
}

.woocommerce-info {
    background: #d1ecf1;
    color: #0c5460;
    border-left-color: #17a2b8;
}

.woocommerce-error {
    background: #f8d7da;
    color: #721c24;
    border-left-color: #dc3545;
}

/* Responsividade geral */
@media (max-width: 768px) {
    .container {
        padding-left: 15px;
        padding-right: 15px;
    }
    
    h1 {
        font-size: 2rem;
    }
    
    .woocommerce-breadcrumb {
        padding: 10px 15px;
        font-size: 14px;
    }
}
body{
    margin: 0!important;
    padding: 0!important;
}
* {
    box-sizing: border-box;
}
.woocommerce-message,
.woocommerce-info,
.woocommerce-error {
    position: relative !important;
    padding-left: 3.2rem !important; /* espaço pro ícone */
    display: flex;
    align-items: center;
}

.woocommerce-message::before,
.woocommerce-info::before,
.woocommerce-error::before {
    left: 1rem !important; /* posiciona corretamente */
    top: 50% !important;
    transform: translateY(-50%) !important;
}

</style>

<?php get_footer('loja'); ?>