<?php
/**
 * Template Name: Minha Conta
 * Template for my account page
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

            <h1 class="mb-4">Minha Conta</h1>
            
            <?php
            if (function_exists('woocommerce_content')) {
                // Usar o shortcode da conta do WooCommerce
                echo do_shortcode('[woocommerce_my_account]');
            } else {
                echo '<div class="alert alert-warning">WooCommerce não está ativo.</div>';
            }
            ?>
        </div>
    </div>
</div>

<style>
/* Estilos específicos para a página da conta */
.woocommerce-account {
    background: #fff;
    border-radius: 16px;
    padding: 40px;
    box-shadow: 0 5px 25px rgba(0,0,0,0.08);
}

.woocommerce-MyAccount-navigation {
    background: #f8f9fa;
    border-radius: 12px;
    padding: 0;
    overflow: hidden;
    margin-bottom: 30px;
}

.woocommerce-MyAccount-navigation ul {
    list-style: none;
    padding: 0;
    margin: 0;
    display: flex;
    flex-wrap: wrap;
}

.woocommerce-MyAccount-navigation li {
    flex: 1;
    min-width: 200px;
    border-right: 1px solid #e9ecef;
}

.woocommerce-MyAccount-navigation li:last-child {
    border-right: none;
}

.woocommerce-MyAccount-navigation a {
    display: block;
    padding: 20px;
    color: #6c757d;
    text-decoration: none;
    text-align: center;
    font-weight: 600;
    transition: all 0.3s ease;
    border-bottom: 3px solid transparent;
}

.woocommerce-MyAccount-navigation a:hover {
    background: white;
    color: #0054a3;
}

.woocommerce-MyAccount-navigation .is-active a {
    background: white;
    color: #0054a3;
    border-bottom-color: #0054a3;
}

.woocommerce-MyAccount-content {
    padding: 30px 0;
}

.woocommerce-EditAccountForm,
.woocommerce-Addresses,
.woocommerce-orders-table {
    background: #f8f9fa;
    border-radius: 12px;
    padding: 30px;
    margin-bottom: 30px;
}

.woocommerce-EditAccountForm h3,
.woocommerce-Addresses h3 {
    color: #2c3e50;
    margin-bottom: 25px;
    font-size: 22px;
}

.woocommerce-form-row {
    margin-bottom: 20px;
}

.woocommerce-form-row label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    color: #2c3e50;
}

.woocommerce-form-row input {
    width: 100%;
    padding: 12px 15px;
    border: 2px solid #e9ecef;
    border-radius: 8px;
    font-size: 16px;
    transition: all 0.3s ease;
}

.woocommerce-form-row input:focus {
    outline: none;
    border-color: #0054a3;
    box-shadow: 0 0 0 3px rgba(0, 84, 163, 0.1);
}

.woocommerce-Button {
    background: linear-gradient(135deg, #28a745, #20c997);
    color: white;
    border: none;
    padding: 15px 30px;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    text-decoration: none;
    display: inline-block;
}

.woocommerce-Button:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(40, 167, 69, 0.3);
}

.woocommerce-orders-table {
    width: 100%;
    border-collapse: collapse;
}

.woocommerce-orders-table th {
    background: linear-gradient(135deg, #0054a3, #007bff);
    color: white;
    padding: 15px;
    text-align: left;
    font-weight: 600;
}

.woocommerce-orders-table td {
    padding: 15px;
    border-bottom: 1px solid #e9ecef;
}

.woocommerce-orders-table tr:hover {
    background: white;
}

.woocommerce-orders-table .woocommerce-orders-table__cell-order-actions a {
    background: #6c757d;
    color: white;
    padding: 8px 15px;
    border-radius: 6px;
    text-decoration: none;
    font-size: 14px;
    transition: all 0.3s ease;
}

.woocommerce-orders-table .woocommerce-orders-table__cell-order-actions a:hover {
    background: #0054a3;
    transform: translateY(-1px);
}

.woocommerce-Address {
    background: white;
    border-radius: 12px;
    padding: 25px;
    margin-bottom: 20px;
    box-shadow: 0 2px 15px rgba(0,0,0,0.05);
}

.woocommerce-address-fields {
    background: white;
    border-radius: 12px;
    padding: 30px;
    box-shadow: 0 2px 15px rgba(0,0,0,0.05);
}

/* Login/Register Forms */
.woocommerce-form-login,
.woocommerce-form-register {
    background: #f8f9fa;
    border-radius: 12px;
    padding: 30px;
    max-width: 500px;
    margin: 0 auto;
}

.woocommerce-form-login h2,
.woocommerce-form-register h2 {
    color: #2c3e50;
    text-align: center;
    margin-bottom: 25px;
}

.woocommerce-form-login .woocommerce-form-login__submit,
.woocommerce-form-register .woocommerce-form-register__submit {
    width: 100%;
    margin-top: 20px;
}

.woocommerce-form-login__rememberme {
    margin: 15px 0;
}

.lost_password {
    text-align: center;
    margin-top: 20px;
}

.lost_password a {
    color: #0054a3;
    text-decoration: none;
}

.lost_password a:hover {
    text-decoration: underline;
}

/* Responsividade */
@media (max-width: 768px) {
    .woocommerce-account {
        padding: 20px;
    }
    
    .woocommerce-MyAccount-navigation ul {
        flex-direction: column;
    }
    
    .woocommerce-MyAccount-navigation li {
        border-right: none;
        border-bottom: 1px solid #e9ecef;
    }
    
    .woocommerce-MyAccount-navigation li:last-child {
        border-bottom: none;
    }
    
    .woocommerce-orders-table {
        display: block;
        overflow-x: auto;
    }
    
    .woocommerce-Addresses .u-columns {
        flex-direction: column;
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
</style>

<?php get_footer('loja'); ?>