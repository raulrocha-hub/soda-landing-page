<?php
/**
 * Plugin Name: Soda Perfeita
 * Plugin URI: https://preshh.com/soda-perfeita
 * Description: Sistema de gestão para o programa Soda Perfeita - Integração Preshh e DVG
 * Version: 1.0.0
 * Author: Preshh
 * License: GPL v2 or later
 * Text Domain: soda-perfeita
 * Domain Path: /languages
 * Requires at least: 6.0
 * Requires PHP: 7.4
 */

// Prevenir acesso direto
if (!defined('ABSPATH')) {
    exit;
}

// Definições de constantes
define('SODA_PERFEITA_VERSION', '1.0.0');
define('SODA_PERFEITA_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('SODA_PERFEITA_PLUGIN_URL', plugin_dir_url(__FILE__));
define('SODA_PERFEITA_PLUGIN_BASENAME', plugin_basename(__FILE__));

// Verificar dependências necessárias
register_activation_hook(__FILE__, 'soda_perfeita_check_dependencies');
function soda_perfeita_check_dependencies() {
    $required_plugins = array(
        'jet-engine/jet-engine.php' => 'JetEngine',
        'sfwd-lms/sfwd_lms.php' => 'LearnDash',
    );
    
    $missing = array();
    foreach ($required_plugins as $plugin => $name) {
        if (!is_plugin_active($plugin)) {
            $missing[] = $name;
        }
    }
    
    if (!empty($missing)) {
        deactivate_plugins(plugin_basename(__FILE__));
        wp_die(
            sprintf(
                __('O plugin Soda Perfeita requer os seguintes plugins: %s. Por favor, instale e ative estas dependências antes de ativar o Soda Perfeita.', 'soda-perfeita'),
                implode(', ', $missing)
            )
        );
    }
}

// Classe principal do plugin
class SodaPerfeita {

    private static $instance = null;
    public $cpt_manager;
    public $roles_manager;
    public $pedidos_manager;
    public $meritocracia;
    public $dashboard;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action('plugins_loaded', array($this, 'init'));
        add_action('init', array($this, 'load_textdomain'));
        register_activation_hook(__FILE__, array($this, 'activate'));
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));
    }

    public function init() {
        // Carregar dependências
        $this->load_dependencies();
        
        // Inicializar componentes
        $this->initialize_components();
        
        // Carregar assets
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
        
        // Registrar shortcodes
        $this->register_shortcodes();
        
        // Registrar endpoints da API
        $this->register_rest_endpoints();
    }

    public function load_dependencies() {
        // Incluir classes necessárias
        require_once SODA_PERFEITA_PLUGIN_DIR . 'includes/class-cpt-manager.php';
        require_once SODA_PERFEITA_PLUGIN_DIR . 'includes/class-roles-manager.php';
        require_once SODA_PERFEITA_PLUGIN_DIR . 'includes/class-pedidos-manager.php';
        require_once SODA_PERFEITA_PLUGIN_DIR . 'includes/class-meritocracia.php';
        require_once SODA_PERFEITA_PLUGIN_DIR . 'includes/class-dashboard.php';
        require_once SODA_PERFEITA_PLUGIN_DIR . 'includes/class-form-handler.php';
        require_once SODA_PERFEITA_PLUGIN_DIR . 'includes/class-automations.php';
        
        // Incluir funções auxiliares
        require_once SODA_PERFEITA_PLUGIN_DIR . 'includes/helpers.php';
    }

    public function initialize_components() {
        // Inicializar gerenciador de CPTs
        $this->cpt_manager = new SodaPerfeita_CPT_Manager();
        
        // Inicializar gerenciador de roles
        $this->roles_manager = new SodaPerfeita_Roles_Manager();
        
        // Inicializar gerenciador de pedidos
        $this->pedidos_manager = new SodaPerfeita_Pedidos_Manager();
        
        // Inicializar sistema de meritocracia
        $this->meritocracia = new SodaPerfeita_Meritocracia();
        
        // Inicializar dashboard
        $this->dashboard = new SodaPerfeita_Dashboard();
    }

    public function load_textdomain() {
        load_plugin_textdomain(
            'soda-perfeita',
            false,
            dirname(SODA_PERFEITA_PLUGIN_BASENAME) . '/languages'
        );
    }

    public function enqueue_scripts() {
        // CSS principal
        wp_enqueue_style(
            'soda-perfeita-frontend',
            SODA_PERFEITA_PLUGIN_URL . 'assets/css/frontend.css',
            array(),
            SODA_PERFEITA_VERSION
        );

        // JS principal
        wp_enqueue_script(
            'soda-perfeita-frontend',
            SODA_PERFEITA_PLUGIN_URL . 'assets/js/frontend.js',
            array('jquery'),
            SODA_PERFEITA_VERSION,
            true
        );

        // Localizar script para AJAX
        wp_localize_script(
            'soda-perfeita-frontend',
            'sodaPerfeitaAjax',
            array(
                'ajaxurl' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('soda_perfeita_nonce')
            )
        );
    }

    public function enqueue_admin_scripts($hook) {
        // Carregar apenas nas páginas do plugin
        if (strpos($hook, 'soda-perfeita') === false) {
            return;
        }

        wp_enqueue_style(
            'soda-perfeita-admin',
            SODA_PERFEITA_PLUGIN_URL . 'assets/css/admin.css',
            array(),
            SODA_PERFEITA_VERSION
        );

        wp_enqueue_script(
            'soda-perfeita-admin',
            SODA_PERFEITA_PLUGIN_URL . 'assets/js/admin.js',
            array('jquery', 'wp-util'),
            SODA_PERFEITA_VERSION,
            true
        );

        // Chart.js para dashboards
        wp_enqueue_script(
            'chart-js',
            'https://cdn.jsdelivr.net/npm/chart.js',
            array(),
            '3.9.1',
            true
        );
    }

    public function register_shortcodes() {
        add_shortcode('soda_perfeita_dashboard', array($this->dashboard, 'render_dashboard'));
        add_shortcode('soda_perfeita_pedidos', array($this->pedidos_manager, 'render_pedidos_form'));
        add_shortcode('soda_perfeita_meritocracia', array($this->meritocracia, 'render_meritocracia_status'));
    }

    public function register_rest_endpoints() {
        add_action('rest_api_init', function() {
            register_rest_route('soda-perfeita/v1', '/verificar-status', array(
                'methods' => 'POST',
                'callback' => array($this->pedidos_manager, 'verificar_status_financeiro'),
                'permission_callback' => function() {
                    return current_user_can('read');
                }
            ));
            
            register_rest_route('soda-perfeita/v1', '/dashboard-data', array(
                'methods' => 'GET',
                'callback' => array($this->dashboard, 'get_dashboard_data'),
                'permission_callback' => function() {
                    return current_user_can('read');
                }
            ));
        });
    }

    public function activate() {
        // Configurar roles e capabilities
        $this->roles_manager->setup_roles();
        
        // Configurar CPTs e taxonomias
        $this->cpt_manager->register_custom_post_types();
        flush_rewrite_rules();
        
        // Configurar páginas necessárias
        $this->create_necessary_pages();
        
        // Configurar opções padrão
        $this->set_default_options();
        
        // Registrar cron jobs para atualização de tiers
        if (!wp_next_scheduled('soda_perfeita_daily_maintenance')) {
            wp_schedule_event(time(), 'daily', 'soda_perfeita_daily_maintenance');
        }
    }

    public function deactivate() {
        // Limpar regras de rewrite
        flush_rewrite_rules();
        
        // Limpar cron jobs
        wp_clear_scheduled_hook('soda_perfeita_daily_maintenance');
        
        // Remover capabilities (opcional - avaliar se necessário)
        // $this->roles_manager->cleanup_roles();
    }

    private function create_necessary_pages() {
        $pages = array(
            'dashboard-soda-perfeita' => array(
                'title' => __('Dashboard Soda Perfeita', 'soda-perfeita'),
                'content' => '[soda_perfeita_dashboard]',
                'parent' => null
            ),
            'pedidos-xarope' => array(
                'title' => __('Pedidos de Xarope', 'soda-perfeita'),
                'content' => '[soda_perfeita_pedidos]',
                'parent' => 'dashboard-soda-perfeita'
            ),
            'meritocracia' => array(
                'title' => __('Minha Meritocracia', 'soda-perfeita'),
                'content' => '[soda_perfeita_meritocracia]',
                'parent' => 'dashboard-soda-perfeita'
            )
        );

        foreach ($pages as $slug => $page) {
            $existing = get_page_by_path($slug);
            if (!$existing) {
                $page_data = array(
                    'post_title' => $page['title'],
                    'post_name' => $slug,
                    'post_content' => $page['content'],
                    'post_status' => 'publish',
                    'post_type' => 'page',
                    'post_author' => 1,
                );

                if ($page['parent']) {
                    $parent_page = get_page_by_path($page['parent']);
                    if ($parent_page) {
                        $page_data['post_parent'] = $parent_page->ID;
                    }
                }

                wp_insert_post($page_data);
            }
        }
    }

    private function set_default_options() {
        $default_options = array(
            'preco_xarope' => 45.00,
            'dias_meritocracia' => 90,
            'tier1_min_pedidos' => 4,
            'tier2_min_pedidos' => 12,
            'tier3_min_pedidos' => 25,
            'bloquear_inadimplentes' => 'yes',
            'email_notificacoes' => get_option('admin_email'),
        );

        foreach ($default_options as $key => $value) {
            if (get_option('soda_perfeita_' . $key) === false) {
                update_option('soda_perfeita_' . $key, $value);
            }
        }
    }

    /**
     * Método para log de eventos (debug)
     */
    public static function log($message, $type = 'info') {
        if (defined('WP_DEBUG') && WP_DEBUG) {
            $timestamp = current_time('mysql');
            $log_entry = "[{$timestamp}] [{$type}] {$message}" . PHP_EOL;
            
            $log_file = SODA_PERFEITA_PLUGIN_DIR . 'logs/debug.log';
            
            // Criar diretório de logs se não existir
            if (!file_exists(dirname($log_file))) {
                wp_mkdir_p(dirname($log_file));
            }
            
            error_log($log_entry, 3, $log_file);
        }
    }
}

// Inicializar o plugin
function soda_perfeita_init() {
    return SodaPerfeita::get_instance();
}
add_action('plugins_loaded', 'soda_perfeita_init');

// Hook para manutenção diária
add_action('soda_perfeita_daily_maintenance', 'soda_perfeita_run_daily_tasks');
function soda_perfeita_run_daily_tasks() {
    $meritocracia = new SodaPerfeita_Meritocracia();
    $meritocracia->atualizar_tiers_diarios();
}

// Função auxiliar para acesso fácil à instância principal
function SodaPerfeita() {
    return SodaPerfeita::get_instance();
}

// Funções globais de utilidade
if (!function_exists('soda_perfeita_get_client_tier')) {
    function soda_perfeita_get_client_tier($client_id) {
        $meritocracia = SodaPerfeita()->meritocracia;
        return $meritocracia->calcular_tier_cliente($client_id);
    }
}

if (!function_exists('soda_perfeita_can_make_order')) {
    function soda_perfeita_can_make_order($client_id) {
        $pedidos_manager = SodaPerfeita()->pedidos_manager;
        return $pedidos_manager->verificar_status_cliente($client_id);
    }
}
?>