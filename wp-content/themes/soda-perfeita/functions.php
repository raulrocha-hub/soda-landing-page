<?php
// Funções principais do tema Soda Perfeita

// Incluir arquivos necessários

// Configurações do tema
function soda_perfeita_setup() {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('custom-logo');
    add_theme_support('woocommerce');
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
    wp_enqueue_style(
        'google-font-outfit',
        'https://fonts.googleapis.com/css2?family=Outfit:wght@100..900&display=swap',
        [],
        null
    );
    // JS
    wp_enqueue_script('bootstrap-js', get_template_directory_uri() . '/assets/js/bootstrap.bundle.min.js', array('jquery'), '5.3.0', true);
    wp_enqueue_script('main-js', get_template_directory_uri() . '/assets/js/main.js', array('jquery'), '1.0', true);
}
add_action('wp_enqueue_scripts', 'soda_perfeita_scripts');
/*
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
add_action('init', 'soda_perfeita_register_products');*/

add_action('init', function () {
    register_post_type('lead', [
        'label' => 'Leads',
        'public' => false,
        'show_ui' => true,
        'supports' => ['title'],
        'menu_icon' => 'dashicons-id'
    ]);
});

// Coloque no functions.php do tema ou em um plugin seu

add_action('acf/save_post', function ($post_id) {
    // Ignora telas de opções e autosave
    if ($post_id === 'options' || (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)) return;

    $post = get_post($post_id);
    if (!$post) return;

    // Opcional: restrinja ao seu post type (troque 'lead' se necessário)
    if ($post->post_type !== 'lead') return;

    // 1) Tenta pelo nome do field (se o ACF estiver com field name = 'nome')
    $nome = get_field('nome', $post_id);

    // 2) Fallback por field key (MAIS SEGURO)
    //    -> Substitua pela key real do seu campo "nome", ex.: 'field_690e0aae52d20'
    if (!$nome && !empty($_POST['acf']['field_XXXXX_nome'])) {
        $nome = sanitize_text_field($_POST['acf']['field_XXXXX_nome']);
    }

    $nome = is_string($nome) ? trim($nome) : '';

    if ($nome !== '') {
        // Evita loop ao atualizar o post dentro do próprio hook
        remove_action('acf/save_post', __FUNCTION__);

        wp_update_post([
            'ID'         => $post_id,
            'post_title' => $nome,
            'post_name'  => sanitize_title($nome), // atualiza o slug
        ]);

        // Reanexa o hook
        add_action('acf/save_post', __FUNCTION__);
    }
}, 20);


/**
 * Colunas do admin para CPT "lead"
 */

// 1) Define/ordena as colunas
add_filter('manage_edit-lead_columns', function ($cols) {
    // remove o padrão e remonta do jeito que queremos
    $new = [];
    $new['cb']       = '<input type="checkbox" />';
    $new['id']       = 'ID';
    $new['title']    = 'Nome';       // usamos o post_title (já é o "nome")
    $new['email']    = 'Email';
    $new['telefone'] = 'telefone';
    $new['estabelecimento'] = 'Estabelecimento';
    $new['cnpj']    = 'CNPJ';       // usamos o post_title (já é o "nome")
    $new['mensagem']    = 'mensagem';
    
    return $new;
});

// 2) Renderiza o conteúdo das colunas
add_action('manage_lead_posts_custom_column', function ($col, $post_id) {
    switch ($col) {
        case 'id':
            echo (int) $post_id;
            break;

        case 'email':
            // tenta por ACF (get_field), cai para meta se não houver
            $email = function_exists('get_field') ? get_field('email', $post_id) : get_post_meta($post_id, 'email', true);
            $email = sanitize_email($email);
            if ($email) {
                $t = esc_html($email);
                echo '<a href="mailto:' . esc_attr($email) . '">' . $t . '</a>';
            } else {
                echo '—';
            }
            break;

        case 'telefone':
            $wa = function_exists('get_field') ? get_field('telefone', $post_id) : get_post_meta($post_id, 'telefone', true);
            $wa = preg_replace('/\D+/', '', (string) $wa); // só dígitos
            if ($wa) {
                $link = 'https://wa.me/' . $wa;
                echo '<a href="' . esc_url($link) . '" target="_blank" rel="noopener">Abrir conversa</a><br><small>' . esc_html($wa) . '</small>';
            } else {
                echo '—';
            }
            break;
            case 'estabelecimento':
            $val = function_exists('get_field') ? get_field('estabelecimento', $post_id) : get_post_meta($post_id, 'estabelecimento', true);
            $val = is_string($val) ? trim($val) : '';
            echo $val ? esc_html($val) : '—';
            break;

        case 'cnpj':
            $cnpj = function_exists('get_field') ? get_field('cnpj', $post_id) : get_post_meta($post_id, 'cnpj', true);
            $digits = preg_replace('/\D+/', '', (string) $cnpj);
            if (strlen($digits) === 14) {
                // formata 00.000.000/0000-00
                $fmt = substr($digits,0,2).'.'.substr($digits,2,3).'.'.substr($digits,5,3).'/'.substr($digits,8,4).'-'.substr($digits,12,2);
                echo esc_html($fmt);
            } elseif ($digits) {
                echo esc_html($digits);
            } else {
                echo '—';
            }
            break;

        case 'mensagem':
            $msg = function_exists('get_field') ? get_field('mensagem', $post_id) : get_post_meta($post_id, 'mensagem', true);
            if (is_array($msg)) $msg = wp_json_encode($msg);
            $msg = is_string($msg) ? trim(wp_strip_all_tags($msg)) : '';
            if ($msg) {
                // mostra um resumo para não quebrar a listagem
                $excerpt = wp_trim_words($msg, 18, '…');
                echo '<span title="' . esc_attr($msg) . '">' . esc_html($excerpt) . '</span>';
            } else {
                echo '—';
            }
            break;
    }
}, 10, 2);

// 3) Define colunas ordenáveis
add_filter('manage_edit-lead_sortable_columns', function ($sortable) {
    $sortable['id']       = 'ID';
    $sortable['nome'] = 'nome';
    $sortable['email']    = 'email';
    $sortable['telefone'] = 'telefone';
    $sortable['estabelecimento'] = 'estabelecimento';
    $sortable['cnpj'] = 'cnpj';
    $sortable['mensagem'] = 'mensagem';
    // 'title' e 'date' já são ordenáveis nativos
    return $sortable;
});
// Suporte ao WooCommerce
function soda_perfeita_woocommerce_support() {
    add_theme_support('woocommerce');
    add_theme_support('wc-product-gallery-zoom');
    add_theme_support('wc-product-gallery-lightbox');
    add_theme_support('wc-product-gallery-slider');
}
add_action('after_setup_theme', 'soda_perfeita_woocommerce_support');
// 4) Implementa a ordenação por meta (email/telefone/telefone)
add_action('pre_get_posts', function ($q) {
    if (!is_admin() || !$q->is_main_query()) return;
    if ($q->get('post_type') !== 'lead') return;

    $orderby = $q->get('orderby');
    $map = [
        'nome' => 'nome',
        'email'    => 'email',
        'telefone' => 'telefone',
        
    ];
    if (isset($map[$orderby])) {
        $meta_key = $map[$orderby];
        $q->set('meta_key', $meta_key);
        // Como são strings, usar meta_value (ou meta_value_num se só dígitos)
        $q->set('orderby', 'meta_value');
    }
});

// 5) Um CSS rápido para largura/legibilidade (opcional)
add_action('admin_head-edit.php', function () {
    $screen = get_current_screen();
    if (!$screen || $screen->post_type !== 'lead') return; ?>
    <style>
      .post-type-lead table.wp-list-table .column-id       { width: 80px; }
      .post-type-lead table.wp-list-table .column-email    { width: 240px; }
      .post-type-lead table.wp-list-table .column-telefone { width: 160px; }
      .post-type-lead table.wp-list-table .column-whatsapp { width: 160px; }
    </style>
<?php
});

$updated = isset($_GET['updated']) ? strtolower((string) $_GET['updated']) : '';
if (in_array($updated, ['true','1'], true)) : ?>
  <div class="alert alert-success alert-dismissible fade show" role="alert" style="max-width:740px;margin:0 auto 16px;">
    <i class="fas fa-check-circle me-2" aria-hidden="true"></i>
    Dados enviados/atualizados com sucesso.
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
  </div>
  <script>
    (function () {
      try {
        var url = new URL(window.location.href);
        url.searchParams.delete('updated');
        window.history.replaceState(null, '', url.toString());
      } catch(e) {}
    })();
  </script>
<?php endif; 

// Registrar menus
function register_loja_menus() {
    register_nav_menus(array(
        'menu-loja' => 'Menu Principal da Loja',
        'menu-categorias' => 'Menu de Categorias da Loja'
    ));
}
add_action('init', 'register_loja_menus');

add_action('wp_ajax_product_search', 'product_search');
add_action('wp_ajax_nopriv_product_search', 'product_search');

function product_search() {
    $search_term = sanitize_text_field($_POST['search_term']);
    
    $args = array(
        'post_type' => 'product',
        'posts_per_page' => 8,
        's' => $search_term,
        'post_status' => 'publish'
    );
    
    $search_query = new WP_Query($args);
    $products = array();
    
    if ($search_query->have_posts()) {
        while ($search_query->have_posts()) {
            $search_query->the_post();
            $product = wc_get_product(get_the_ID());
            
            $products[] = array(
                'name' => get_the_title(),
                'permalink' => get_permalink(),
                'price' => $product->get_price_html(),
                'image' => get_the_post_thumbnail_url(get_the_ID(), 'thumbnail')
            );
        }
        wp_reset_postdata();
    }
    
    wp_send_json($products);
}
/*
function redirecionar_visitantes_para_login() {
    // Verifica se é um usuário não logado em uma página do WooCommerce
    if ( ! is_user_logged_in() && ( is_shop() || is_product_category() || is_product() || is_cart() || is_checkout() || is_account_page() ) ) {
        wp_redirect( wp_login_url( get_permalink() ) );
        exit;
    }
}
add_action( 'template_redirect', 'redirecionar_visitantes_para_login' );
*/