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
// Adicionar no functions.php ou no seu plugin
// 1. PRIMEIRO: Corrigir as capabilities da role distribuidor_dvg
function soda_perfeita_corrigir_capabilities_distribuidor() {
    // Remove a role se já existir
    //remove_role('distribuidor_dvg');
    
    // Cria a role com capabilities específicas do WooCommerce
    add_role('distribuidor_dvg', 'Distribuidor DVG', array(
        'read' => true,
        'edit_posts' => false,
        'upload_files' => true,
        'view_admin_dashboard' => true,
        
        // Capacidades específicas do WooCommerce
        'read_shop_order' => true,
        'read_private_shop_orders' => true,
        'edit_shop_orders' => true,
        'edit_private_shop_orders' => true,
        'edit_published_shop_orders' => true,
        'edit_others_shop_orders' => false, // CRÍTICO: não pode editar pedidos de outros
        'publish_shop_orders' => false,
        'delete_shop_orders' => false,
        'delete_private_shop_orders' => false,
        'delete_published_shop_orders' => false,
        'delete_others_shop_orders' => false,
        
        // Outras capacidades do WooCommerce
        'manage_woocommerce' => true,
        'view_woocommerce_reports' => false,
    ));
}
add_action('init', 'soda_perfeita_corrigir_capabilities_distribuidor');

/**
 * 1. FILTRAR A LISTA DE PEDIDOS no Admin para distribuidores
 * Restringe a lista para mostrar apenas pedidos vinculados ao distribuidor logado.
 */
/**
 * 1. FILTRAR A LISTA DE PEDIDOS no Admin para distribuidores
 * Restringe a lista para mostrar apenas pedidos vinculados ao distribuidor logado.
 */
// 2. FILTRAR OS PEDIDOS VISÍVEIS para o distribuidor
/**
 * Modifica a query principal da lista de pedidos no Admin para distribuidores
 * Hook específico para lojas usando HPOS (High-Performance Order Storage)
 */
add_filter( 'woocommerce_order_list_table_prepare_items_query_args', 'soda_perfeita_filtrar_pedidos_admin_por_distribuidor' );

function soda_perfeita_filtrar_pedidos_admin_por_distribuidor( $query_args ) {
    // Verifica se estamos no admin e se o usuário atual é um distribuidor
    if ( ! is_admin() || ! current_user_can( 'distribuidor_dvg' ) ) {
        return $query_args;
    }

    $current_user_id = get_current_user_id();
    $distribuidor_id = soda_perfeita_obter_distribuidor_por_usuario( $current_user_id );

    // Se encontrou o ID do distribuidor, aplica o filtro pela meta_key 'distribuidor'
    if ( $distribuidor_id ) {
        $query_args['meta_query'] = array(
            array(
                'key' => 'distribuidor', // Substitua pela sua meta_key
                'value' => $distribuidor_id,
                'compare' => '='
            )
        );
    } else {
        // Se o distribuidor não está configurado, não retorna nenhum pedido
        $query_args['post__in'] = array(0);
    }

    return $query_args;
}

// FUNÇÃO PARA OBTER META DO PEDIDO VIA SQL
function soda_perfeita_obter_meta_distribuidor_sql($order_id) {
    global $wpdb;
    
    // Tenta na tabela HPOS primeiro
    $meta_value = $wpdb->get_var($wpdb->prepare("
        SELECT meta_value 
        FROM {$wpdb->prefix}wc_orders_meta 
        WHERE order_id = %d 
        AND meta_key = 'distribuidor'
    ", $order_id));
    
    if ($meta_value !== null) {
        return $meta_value;
    }
    
    // Se não encontrou no HPOS, tenta na tabela postmeta tradicional
    $meta_value = $wpdb->get_var($wpdb->prepare("
        SELECT meta_value 
        FROM {$wpdb->prefix}postmeta 
        WHERE post_id = %d 
        AND meta_key = 'distribuidor'
    ", $order_id));
    
    return $meta_value;
}

// CORREÇÃO DA VERIFICAÇÃO DE ACESSO
add_action('before_woocommerce_init', 'soda_perfeita_verificar_acesso_pedido_distribuidor');

function soda_perfeita_verificar_acesso_pedido_distribuidor() {
    if (!is_admin()) return;
    
    if (isset($_GET['page']) && $_GET['page'] === 'wc-orders' && isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['id'])) {
        $current_user = wp_get_current_user();
        
        if (in_array('distribuidor_dvg', $current_user->roles)) {
            $order_id = absint($_GET['id']);
            $distribuidor_id = soda_perfeita_obter_distribuidor_por_usuario($current_user->ID);
            
            if ($distribuidor_id) {
                // USA SQL DIRETO PARA OBTER O META
                (int)$order_distribuidor = soda_perfeita_obter_meta_distribuidor_sql($order_id);
                // Debug para ver o que está acontecendo
                error_log("DEBUG - Order ID: {$order_id}, Distribuidor ID: {$distribuidor_id}, Order Distribuidor: {$order_distribuidor}");
                
                if ($order_distribuidor != $distribuidor_id) {
                    wp_die(
                        'Você não tem permissão para acessar este pedido. Apenas pedidos atribuídos ao seu distribuidor podem ser visualizados.',
                        'Acesso Negado',
                        array('response' => 403)
                    );
                }
            } else {
                wp_die(
                    'Seu usuário não está vinculado a um distribuidor. Contate o administrador.',
                    'Erro de Configuração',
                    array('response' => 403)
                );
            }
        }
    }
}

// DEBUG MELHORADO


/**
 * Função auxiliar para obter o ID do post do distribuidor pelo ID do usuário associado
 */
function soda_perfeita_obter_distribuidor_por_usuario( $user_id ) {
    $args = array(
        'post_type' => 'distribuidor', // Altere para o slug do seu CPT de distribuidor
        'posts_per_page' => 1,
        'meta_query' => array(
            array(
                'key' => 'usuario_associado', // Nome do campo ACF que relaciona o CPT ao usuário
                'value' => $user_id,
                'compare' => '='
            )
        )
    );

    $distribuidores = get_posts( $args );

    if ( ! empty( $distribuidores ) ) {
        return $distribuidores[0]->ID; // Retorna o ID do post do distribuidor
    }
    return false;
}

$user_id = get_current_user_id();
$distribuidorID = soda_perfeita_obter_distribuidor_por_usuario($user_id);
//var_dump($user_id); var_dump($distribuidorID);  exit;
// Restringir acesso a pedidos individuais
add_action('current_screen', 'soda_perfeita_restringir_acesso_pedido_individual');
// DEBUG - Verificar capabilities e queries

/**
 * 2. RESTRINGIR ACESSO À EDIÇÃO DE PEDIDOS INDIVIDUAIS
 * Impede que um distribuidor acesse pedidos que não são dele.
 */
add_action( 'current_screen', 'soda_perfeita_restringir_acesso_pedido_individual' );

function soda_perfeita_restringir_acesso_pedido_individual() {
    $screen = get_current_screen();

    // Verificar se estamos na tela de edição de um pedido individual
    if ( $screen && $screen->id == 'woocommerce_page_wc-orders' && isset( $_GET['action'] ) && $_GET['action'] == 'edit' ) {
        $current_user = wp_get_current_user();

        if ( in_array( 'distribuidor_dvg', $current_user->roles ) ) {
            $order_id = absint( $_GET['id'] );
            $distribuidor_id = soda_perfeita_obter_distribuidor_por_usuario( $current_user->ID );

            if ( $distribuidor_id ) {
                $order_distribuidor = get_field( 'distribuidor', $order_id ); // Usando ACF

                // Se o pedido não pertence a este distribuidor, bloquear o acesso
                if ( $order_distribuidor != $distribuidor_id ) {
                    wp_die(
                        'Você não tem permissão para acessar este pedido.',
                        'Acesso Negado',
                        array( 'response' => 403 )
                    );
                }
            } else {
                wp_die(
                    'Seu usuário não está vinculado a um distribuidor. Contate o administrador.',
                    'Erro de Configuração',
                    array( 'response' => 403 )
                );
            }
        }
    }
}

// Adicionar coluna personalizada na listagem de pedidos
add_filter('manage_edit-shop_order_columns', 'soda_perfeita_adicionar_coluna_distribuidor');

function soda_perfeita_adicionar_coluna_distribuidor($columns) {
    $new_columns = array();
    
    foreach ($columns as $key => $column) {
        $new_columns[$key] = $column;
        if ($key === 'order_status') {
            $new_columns['distribuidor'] = 'Distribuidor';
        }
    }
    
    return $new_columns;
}

// Preencher a coluna do distribuidor
add_action('manage_shop_order_posts_custom_column', 'soda_perfeita_preencher_coluna_distribuidor', 10, 2);

function soda_perfeita_preencher_coluna_distribuidor($column, $post_id) {
    if ($column === 'distribuidor') {
        $distribuidor_id = get_field('distribuidor', $post_id);
        
        if ($distribuidor_id) {
            echo get_the_title($distribuidor_id);
        } else {
            echo '<span style="color:#ccc;">—</span>';
        }
    }
}

// Função para debug - verificar se tudo está funcionando
function soda_perfeita_debug_distribuidor_access() {
    if (isset($_GET['debug_distribuidor']) && current_user_can('manage_options')) {
        $current_user = wp_get_current_user();
        echo '<pre>';
        echo 'Usuário atual: ' . $current_user->display_name . "\n";
        echo 'Roles: ' . implode(', ', $current_user->roles) . "\n";
        
        if (in_array('distribuidor_dvg', $current_user->roles)) {
            $distribuidor_id = soda_perfeita_obter_distribuidor_por_usuario($current_user->ID);
            echo 'ID do Distribuidor: ' . ($distribuidor_id ? $distribuidor_id : 'Não encontrado') . "\n";
            
            if ($distribuidor_id) {
                $args = array(
                    'post_type' => 'shop_order',
                    'posts_per_page' => 5,
                    'meta_query' => array(
                        array(
                            'key' => 'distribuidor',
                            'value' => $distribuidor_id,
                            'compare' => '='
                        )
                    )
                );
                
                $pedidos = get_posts($args);
                echo 'Pedidos encontrados: ' . count($pedidos) . "\n";
                
                foreach ($pedidos as $pedido) {
                    echo '- Pedido #' . $pedido->ID . ': ' . $pedido->post_title . "\n";
                }
            }
        }
        echo '</pre>';
    }
}
add_action('admin_init', 'soda_perfeita_debug_distribuidor_access');
/**
 * Summary of redirecionar_apos_login
 * @param mixed $user_login
 * @param mixed $user
 * @return void
 */
function redirecionar_apos_login($user_login, $user) {
    // Verificar se o usuário tem o papel 'distribuidor_dvg'
    if (in_array('distribuidor_dvg', $user->roles)) {
        wp_redirect(admin_url());
        exit();
    }
    // Verificar se o usuário tem o papel 'customer'
    elseif (in_array('customer', $user->roles)) {
        wp_redirect(home_url('/shop/'));
        exit();
    }
}
add_action('wp_login', 'redirecionar_apos_login', 10, 2);

// DEBUG PARA VER TODOS OS MENUS DO WOOCOMMERCE
// REMOVER SUBMENUS ESPECÍFICOS DO WOOCOMMERCE PARA DISTRIBUIDORES
add_action('admin_menu', 'soda_perfeita_remover_submenus_distribuidor', 9999);

function soda_perfeita_remover_submenus_distribuidor() {
    $current_user = wp_get_current_user();
    
    if (!in_array('distribuidor_dvg', $current_user->roles)) {
        return;
    }
    
    // Remover submenus específicos do WooCommerce
    remove_submenu_page('woocommerce', 'wc-settings');      // Configurações
    remove_submenu_page('woocommerce', 'wc-status');        // Status do sistema
    remove_submenu_page('woocommerce', 'wc-addons');        // Extensões
    remove_submenu_page('woocommerce', 'wc-admin');         // Dashboard WooCommerce
    
    // Tentar remover marketing (pode variar entre versões)
    remove_submenu_page('woocommerce', 'wc-settings&tab=checkout&from=PAYMENTS_MENU_ITEM');
    
    // Remover analytics se existir
    remove_submenu_page('woocommerce', 'wc-admin&path=/analytics');

}

// CSS PARA ESCONDER MENUS RESIDUAIS
add_action('admin_head', 'soda_perfeita_esconder_menus_css_distribuidor');

function soda_perfeita_esconder_menus_css_distribuidor() {
    $current_user = wp_get_current_user();
    
    if (!in_array('distribuidor_dvg', $current_user->roles)) {
        return;
    }
    
    echo '<style>
        /* Esconder submenus específicos do WooCommerce */
        .toplevel_page_woocommerce .wp-submenu-wrap li:not(:nth-child(3)):not(:nth-child(4)) {
            display: none !important;
        }
        
        /* Esconder itens específicos por texto */
        .toplevel_page_woocommerce .wp-submenu a[href*="wc-settings"],
        .toplevel_page_woocommerce .wp-submenu a[href*="wc-status"],
        .toplevel_page_woocommerce .wp-submenu a[href*="wc-addons"],
        #toplevel_page_admin-page-wc-settings-tab-checkout-from-PAYMENTS_MENU_ITEM,
        #toplevel_page_woocommerce-marketing {
            display: none !important;
        }
        
        /* Manter apenas Pedidos e Produtos visíveis */
        .toplevel_page_woocommerce .wp-submenu li:nth-child(3),
        .toplevel_page_woocommerce .wp-submenu li:nth-child(4) {
            display: block !important;
        }
    </style>';
}

// REMOVER TODOS OS WIDGETS DO PAINEL WORDPRESS
add_action('wp_dashboard_setup', 'soda_perfeita_remover_todos_widgets_dashboard');

function soda_perfeita_remover_todos_widgets_dashboard() {
    global $wp_meta_boxes;
    
    // Lista de todos os contextos do dashboard
    $contexts = array('normal', 'side', 'advanced');
    
    foreach ($contexts as $context) {
        if (isset($wp_meta_boxes['dashboard'][$context])) {
            foreach ($wp_meta_boxes['dashboard'][$context] as $priority => $boxes) {
                foreach ($boxes as $box_id => $box_data) {
                    remove_meta_box($box_id, 'dashboard', $context);
                }
            }
        }
    }
    
    // Remover widgets específicos que podem escapar
    $widgets_especificos = array(
        'dashboard_primary',           // Notícias do WordPress
        'dashboard_quick_press',       // Rascunho rápido
        'dashboard_right_now',         // Resumo do site
        'dashboard_activity',          // Atividade recente
        'dashboard_site_health',       // Saúde do site
        'woocommerce_dashboard_status', // Status do WooCommerce
        'wc_admin_dashboard_setup',    // Setup WooCommerce
    );
    
    foreach ($widgets_especificos as $widget) {
        remove_meta_box($widget, 'dashboard', 'normal');
        remove_meta_box($widget, 'dashboard', 'side');
    }
}