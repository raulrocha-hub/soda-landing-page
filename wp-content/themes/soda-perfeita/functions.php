<?php
// Funções principais do tema Soda Perfeita

// Incluir arquivos necessários

// Configurações do tema
function soda_perfeita_setup() {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('custom-logo');
    add_theme_support('html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
    ));
    
    // Registrar menus
    register_nav_menus(array(
        'primary' => 'Menu Principal',
        'footer' => 'Menu Rodapé'
    ));
}
add_action('after_setup_theme', 'soda_perfeita_setup');
function register_navwalker(){
	require_once get_template_directory() . '/inc/bootstrap-navwalker.php';
}
add_action( 'after_setup_theme', 'register_navwalker' );
// Enfileirar scripts e styles
function soda_perfeita_scripts() {
    // CSS da pasta css/
    wp_enqueue_style('bootstrap-css', get_template_directory_uri() . '/css/bootstrap.css');
    wp_enqueue_style('root-css', get_template_directory_uri() . '/css/_root.css');
    wp_enqueue_style('app-css', get_template_directory_uri() . '/css/App.css');
    wp_enqueue_style('css2-css', get_template_directory_uri() . '/css/css2.css');
    wp_enqueue_style('index-css', get_template_directory_uri() . '/css/index.css');
    wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css');
    wp_enqueue_style('main-style', get_stylesheet_uri());

    // JS
    wp_enqueue_script('bootstrap-js', get_template_directory_uri() . '/assets/js/bootstrap.bundle.min.js', array('jquery'), '5.3.0', true);
    wp_enqueue_script('main-js', get_template_directory_uri() . '/assets/js/main.js', array('jquery'), '1.0', true);
}
add_action('wp_enqueue_scripts', 'soda_perfeita_scripts');

// Custom Post Type para Produtos
function soda_perfeita_register_products() {
    $labels = array(
        'name' => 'Produtos',
        'singular_name' => 'Produto',
        'menu_name' => 'Produtos',
        'add_new' => 'Adicionar Novo',
        'add_new_item' => 'Adicionar Novo Produto',
        'edit_item' => 'Editar Produto',
        'new_item' => 'Novo Produto',
        'view_item' => 'Ver Produto',
        'search_items' => 'Buscar Produtos',
        'not_found' => 'Nenhum produto encontrado',
        'not_found_in_trash' => 'Nenhum produto na lixeira'
    );

    $args = array(
        'labels' => $labels,
        'public' => true,
        'has_archive' => true,
        'menu_icon' => 'dashicons-carrot',
        'supports' => array('title', 'editor', 'thumbnail', 'excerpt'),
        'show_in_rest' => true,
    );

    register_post_type('produto', $args);
}
add_action('init', 'soda_perfeita_register_products');