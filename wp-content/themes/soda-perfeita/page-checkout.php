<?php
/**
 * Template Name: Checkout
 * Template for checkout page
 */

get_header('loja');
?>

<div class="container-fluid p-0">
    <div class="row">
        <div class="col-12">
            <!-- Breadcrumb -->
            <div class="mb-4">
                <?php woocommerce_breadcrumb(); ?>
            </div>

            <h1 class="mb-4">Finalizar Compra</h1>
            
            <?php
            if (function_exists('woocommerce_content')) {
                // Usar o shortcode do checkout do WooCommerce
                echo do_shortcode('[woocommerce_checkout]');
            } else {
                echo '<div class="alert alert-warning">WooCommerce não está ativo.</div>';
            }
            ?>
        </div>
    </div>
</div>

<style>
/* Estilos específicos para a página de checkout */
.woocommerce-checkout {
    background: #fff;
    border-radius: 16px;
    padding: 40px;
    box-shadow: 0 5px 25px rgba(0,0,0,0.08);
}

.woocommerce-checkout h3 {
    color: #2c3e50;
    font-weight: 700;
    margin-bottom: 25px;
    font-size: 22px;
    padding-bottom: 15px;
    border-bottom: 2px solid #f1f3f4;
}

.woocommerce-billing-fields,
.woocommerce-shipping-fields,
.woocommerce-additional-fields,
.woocommerce-checkout-review-order {
    background: #f8f9fa;
    border-radius: 12px;
    padding: 30px;
    margin-bottom: 30px;
}

.woocommerce-billing-fields__field-wrapper,
.woocommerce-shipping-fields__field-wrapper,
.woocommerce-additional-fields__field-wrapper {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
}

.form-row {
    margin-bottom: 0;
}

.form-row label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    color: #2c3e50;
}

.form-row input,
.form-row textarea,
.form-row select {
    width: 100%;
    padding: 12px 15px;
    border: 2px solid #e9ecef;
    border-radius: 8px;
    font-size: 16px;
    transition: all 0.3s ease;
    background: white;
}

.form-row input:focus,
.form-row textarea:focus,
.form-row select:focus {
    outline: none;
    border-color: #0054a3;
    box-shadow: 0 0 0 3px rgba(0, 84, 163, 0.1);
}

.form-row.woocommerce-invalid input {
    border-color: #dc3545;
}

.form-row.woocommerce-validated input {
    border-color: #28a745;
}

#order_review_heading {
    color: #2c3e50;
    font-weight: 700;
    margin-bottom: 25px;
    font-size: 24px;
}

.woocommerce-checkout-review-order-table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 25px;
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 2px 15px rgba(0,0,0,0.05);
}

.woocommerce-checkout-review-order-table th {
    background: linear-gradient(135deg, #0054a3, #007bff);
    color: white;
    padding: 15px;
    text-align: left;
    font-weight: 600;
}

.woocommerce-checkout-review-order-table td {
    padding: 15px;
    border-bottom: 1px solid #e9ecef;
}

.woocommerce-checkout-review-order-table tr:last-child td {
    border-bottom: none;
}

.woocommerce-checkout-review-order-table .order-total {
    background: #e8f5e8;
    font-weight: 700;
    font-size: 18px;
    color: #28a745;
}

#payment {
    background: white;
    border-radius: 12px;
    padding: 25px;
    box-shadow: 0 2px 15px rgba(0,0,0,0.05);
}

#payment .wc_payment_methods {
    list-style: none;
    padding: 0;
    margin: 0 0 20px 0;
}

#payment .wc_payment_method {
    margin-bottom: 15px;
    padding: 15px;
    border: 2px solid #e9ecef;
    border-radius: 8px;
    transition: all 0.3s ease;
}

#payment .wc_payment_method:hover {
    border-color: #0054a3;
}

#payment .payment_box {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 8px;
    margin-top: 10px;
    font-size: 14px;
}

.woocommerce-terms-and-conditions-wrapper {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 8px;
    margin-bottom: 20px;
}

.woocommerce-terms-and-conditions {
    max-height: 200px;
    overflow-y: auto;
    padding: 15px;
    background: white;
    border-radius: 6px;
    font-size: 14px;
    line-height: 1.6;
}

#place_order {
    background: linear-gradient(135deg, #28a745, #20c997);
    color: white;
    border: none;
    padding: 18px 40px;
    border-radius: 8px;
    font-weight: 600;
    font-size: 18px;
    cursor: pointer;
    transition: all 0.3s ease;
    width: 100%;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

#place_order:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(40, 167, 69, 0.3);
}

.woocommerce-checkout-login,
.woocommerce-checkout-coupon {
    background: #f8f9fa;
    border-radius: 12px;
    padding: 25px;
    margin-bottom: 25px;
}

.woocommerce-checkout-login .button,
.woocommerce-checkout-coupon .button {
    background: #6c757d;
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 6px;
    font-size: 14px;
    cursor: pointer;
    transition: all 0.3s ease;
}

.woocommerce-checkout-login .button:hover,
.woocommerce-checkout-coupon .button:hover {
    background: #0054a3;
    transform: translateY(-1px);
}

/* Notificações */
.woocommerce-error,
.woocommerce-message,
.woocommerce-info {
    background: #f8d7da;
    color: #721c24;
    padding: 15px 20px;
    border-radius: 8px;
    margin-bottom: 20px;
    border-left: 4px solid #dc3545;
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

/* Responsividade */
@media (max-width: 768px) {
    .woocommerce-checkout {
        padding: 20px;
    }
    
    .woocommerce-billing-fields__field-wrapper,
    .woocommerce-shipping-fields__field-wrapper,
    .woocommerce-additional-fields__field-wrapper {
        grid-template-columns: 1fr;
    }
    
    .woocommerce-checkout-review-order-table {
        display: block;
        overflow-x: auto;
    }
    
    #payment {
        padding: 15px;
    }
    
    #place_order {
        padding: 15px 25px;
        font-size: 16px;
    }
}

/* Animações */
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

.woocommerce-checkout > * {
    animation: fadeIn 0.5s ease;
}
/*novos stilos gerais*/
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
</style>

<?php get_footer(); ?>