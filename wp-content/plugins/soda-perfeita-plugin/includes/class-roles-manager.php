<?php
/**
 * Roles and Capabilities Management for Soda Perfeita Plugin
 * Integração com BuddyBoss/MemberPress para controle de acesso
 * 
 * @package SodaPerfeita
 * @since 1.0.0
 */

// Prevenir acesso direto
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Classe principal para gerenciamento de roles e capabilities
 */
class SodaPerfeita_Roles_Manager {
    
    private $roles_config;
    private $capabilities_config;
    
    public function __construct() {
        $this->setup_roles_config();
        $this->setup_capabilities_config();
        $this->register_hooks();
    }

    public function init() {
        // Método mantido para compatibilidade
    }
    
    /**
     * Configura os papéis do sistema
     */
    private function setup_roles_config() {
        $this->roles_config = array(
            'admin_preshh' => array(
                'name' => 'Admin Preshh',
                'display_name' => 'Administrador Preshh',
                'description' => 'Acesso total ao sistema Soda Perfeita - Equipe Preshh',
                'capabilities' => array(
                    // WordPress core
                    'read' => true,
                    'edit_posts' => true,
                    'delete_posts' => true,
                    'upload_files' => true,
                    // Custom capabilities
                    'manage_soda_system' => true,
                    'view_soda_reports' => true,
                    'approve_soda_clients' => true,
                    'manage_soda_orders' => true,
                    'manage_soda_financial' => true,
                    'access_soda_dashboard' => true,
                    'manage_soda_settings' => true
                )
            ),
            'admin_dvg' => array(
                'name' => 'Admin DVG',
                'display_name' => 'Administrador DVG',
                'description' => 'Acesso administrativo DVG - Gestão comercial e distribuição',
                'capabilities' => array(
                    // WordPress core
                    'read' => true,
                    'edit_posts' => true,
                    // Custom capabilities
                    'view_soda_reports' => true,
                    'view_soda_orders' => true,
                    'manage_soda_distributors' => true,
                    'view_soda_financial_dvg' => true,
                    'access_soda_dashboard' => true
                )
            ),
            'franqueado_preshh' => array(
                'name' => 'Franqueado Preshh',
                'display_name' => 'Franqueado Preshh',
                'description' => 'Franqueados Preshh - Gestão de clientes locais',
                'capabilities' => array(
                    // WordPress core
                    'read' => true,
                    'edit_posts' => true,
                    // Custom capabilities
                    'create_soda_clients' => true,
                    'edit_own_soda_clients' => true,
                    'view_own_soda_clients' => true,
                    'create_soda_orders' => true,
                    'view_own_soda_orders' => true,
                    'access_soda_dashboard' => true,
                    'view_soda_meritocracia' => true
                )
            ),
            'distribuidor_dvg' => array(
                'name' => 'Distribuidor DVG',
                'display_name' => 'Distribuidor DVG',
                'description' => 'Distribuidores DVG - Entrega e faturamento de pedidos',
                'capabilities' => array(
                    // WordPress core
                    'read' => true,
                    // Custom capabilities
                    'view_assigned_soda_orders' => true,
                    'update_order_status' => true,
                    'confirm_order_delivery' => true,
                    'view_assigned_clients' => true,
                    'access_soda_dashboard' => true
                )
            ),
            'cliente_final' => array(
                'name' => 'Cliente Final',
                'display_name' => 'Cliente Final',
                'description' => 'Clientes finais do programa Soda Perfeita',
                'capabilities' => array(
                    // WordPress core
                    'read' => true,
                    // Custom capabilities
                    'view_own_profile' => true,
                    'create_own_orders' => true,
                    'view_own_orders' => true,
                    'view_own_meritocracia' => true,
                    'access_soda_dashboard' => true,
                    'complete_soda_training' => true
                )
            )
        );
    }
    
    /**
     * Configura as capabilities customizadas
     */
    private function setup_capabilities_config() {
        $this->capabilities_config = array(
            // Sistema Geral
            'manage_soda_system' => 'Gerenciar todo o sistema Soda Perfeita',
            'access_soda_dashboard' => 'Acessar dashboard do Soda Perfeita',
            'view_soda_reports' => 'Visualizar relatórios do sistema',
            
            // Gestão de Clientes
            'create_soda_clients' => 'Criar novos clientes no sistema',
            'edit_soda_clients' => 'Editar clientes existentes',
            'edit_own_soda_clients' => 'Editar apenas clientes próprios',
            'view_soda_clients' => 'Visualizar todos os clientes',
            'view_own_soda_clients' => 'Visualizar apenas clientes próprios',
            'view_assigned_clients' => 'Visualizar clientes atribuídos',
            'approve_soda_clients' => 'Aprovar novos clientes',
            'block_soda_clients' => 'Bloquear clientes',
            
            // Gestão de Pedidos
            'manage_soda_orders' => 'Gerenciar todos os pedidos',
            'create_soda_orders' => 'Criar pedidos',
            'create_own_orders' => 'Criar pedidos próprios',
            'view_soda_orders' => 'Visualizar todos os pedidos',
            'view_own_soda_orders' => 'Visualizar apenas pedidos próprios',
            'view_assigned_soda_orders' => 'Visualizar pedidos atribuídos',
            'approve_soda_orders' => 'Aprovar pedidos',
            'update_order_status' => 'Atualizar status de pedidos',
            'confirm_order_delivery' => 'Confirmar entrega de pedidos',
            
            // Financeiro
            'manage_soda_financial' => 'Gerenciar informações financeiras',
            'view_soda_financial_dvg' => 'Visualizar dados financeiros DVG',
            'update_financial_status' => 'Atualizar status financeiro',
            
            // Distribuição
            'manage_soda_distributors' => 'Gerenciar distribuidores',
            'assign_distributors' => 'Atribuir distribuidores a clientes',
            
            // Meritocracia e Treinamento
            'manage_soda_meritocracia' => 'Gerenciar sistema de meritocracia',
            'view_soda_meritocracia' => 'Visualizar meritocracia',
            'view_own_meritocracia' => 'Visualizar própria meritocracia',
            'complete_soda_training' => 'Completar treinamentos Soda Perfeita',
            
            // Configurações
            'manage_soda_settings' => 'Gerenciar configurações do sistema',
            'manage_soda_integrations' => 'Gerenciar integrações'
        );
    }
    
    /**
     * Registra os hooks do WordPress
     */
    private function register_hooks() {
        // Ativação/Desativação
        register_activation_hook(SODA_PERFEITA_PLUGIN_DIR . 'soda-perfeita.php', array($this, 'setup_roles'));
        register_deactivation_hook(SODA_PERFEITA_PLUGIN_DIR . 'soda-perfeita.php', array($this, 'cleanup_roles'));
        
        // Hooks de inicialização
        add_action('init', array($this, 'maybe_update_roles'));
        
        // Filtros de capacidades
        add_filter('user_has_cap', array($this, 'custom_capability_check'), 10, 4);
        
        // Integração com BuddyBoss
        add_action('bp_setup_globals', array($this, 'integrate_with_buddyboss'));
        
        // Admin UI
        add_action('admin_menu', array($this, 'add_roles_admin_page'));
        add_action('admin_init', array($this, 'handle_roles_management'));
        
        // User profile fields
        add_action('show_user_profile', array($this, 'add_user_role_fields'));
        add_action('edit_user_profile', array($this, 'add_user_role_fields'));
        add_action('personal_options_update', array($this, 'save_user_role_fields'));
        add_action('edit_user_profile_update', array($this, 'save_user_role_fields'));
    }
    
    /**
     * Configura os roles durante a ativação do plugin
     */
    public function setup_roles() {
        global $wp_roles;
        
        if (!class_exists('WP_Roles')) {
            return;
        }
        
        if (!isset($wp_roles)) {
            $wp_roles = new WP_Roles();
        }
        
        // Remover roles existentes do Soda Perfeita (para limpeza)
        $this->cleanup_existing_soda_roles();
        
        // Criar novos roles
        foreach ($this->roles_config as $role_slug => $role_config) {
            $display_name = $role_config['display_name'];
            $capabilities = $role_config['capabilities'];
            
            // Adicionar role se não existir
            if (!$wp_roles->is_role($role_slug)) {
                add_role($role_slug, $display_name, $capabilities);
            } else {
                // Atualizar capabilities do role existente
                $role = $wp_roles->get_role($role_slug);
                foreach ($capabilities as $cap => $grant) {
                    if ($grant) {
                        $role->add_cap($cap);
                    } else {
                        $role->remove_cap($cap);
                    }
                }
            }
        }
        
        // Garantir que administradores tenham todas as capabilities
        $admin_role = $wp_roles->get_role('administrator');
        if ($admin_role) {
            foreach ($this->get_all_capabilities() as $capability) {
                $admin_role->add_cap($capability);
            }
        }
        
        // Registrar capabilities no sistema
        $this->register_capabilities();
        
        soda_perfeita_log_activity('roles_setup', 'Sistema de roles do Soda Perfeita configurado');
    }
    
    /**
     * Limpa roles existentes do Soda Perfeita
     */
    private function cleanup_existing_soda_roles() {
        global $wp_roles;
        
        $soda_roles = array_keys($this->roles_config);
        
        foreach ($soda_roles as $role_slug) {
            if ($wp_roles->is_role($role_slug)) {
                remove_role($role_slug);
            }
        }
    }
    
    /**
     * Limpa os roles durante a desativação (opcional)
     */
    public function cleanup_roles() {
        // Por segurança, NÃO removemos os roles na desativação
        // para evitar problemas com usuários existentes
        // $this->cleanup_existing_soda_roles();
        
        soda_perfeita_log_activity('roles_cleanup', 'Plugin Soda Perfeita desativado - roles mantidos');
    }
    
    /**
     * Verifica e atualiza roles se necessário
     */
    public function maybe_update_roles() {
        $roles_version = get_option('soda_perfeita_roles_version', '1.0.0');
        $current_version = SODA_PERFEITA_VERSION;
        
        if (version_compare($roles_version, $current_version, '<')) {
            $this->setup_roles();
            update_option('soda_perfeita_roles_version', $current_version);
        }
    }
    
    /**
     * Registra todas as capabilities no sistema
     */
    private function register_capabilities() {
        global $wp_roles;
        
        if (!isset($wp_roles)) {
            $wp_roles = new WP_Roles();
        }
        
        // Para cada capability, garantir que esteja registrada
        foreach ($this->get_all_capabilities() as $capability) {
            // A capability é automaticamente registrada quando atribuída a um role
        }
    }
    
    /**
     * Retorna todas as capabilities do sistema
     */
    private function get_all_capabilities() {
        $all_caps = array();
        
        foreach ($this->roles_config as $role_config) {
            foreach ($role_config['capabilities'] as $cap => $grant) {
                if ($grant) {
                    $all_caps[$cap] = true;
                }
            }
        }
        
        return array_keys($all_caps);
    }
    
    /**
     * Verificação customizada de capabilities
     */
    public function custom_capability_check($allcaps, $caps, $args, $user) {
        $user_id = $user->ID;
        
        // Para cada capability solicitada
        foreach ($caps as $cap) {
            // Verificar capabilities customizadas
            switch ($cap) {
                case 'edit_own_soda_clients':
                    if ($this->user_can_edit_own_clients($user_id)) {
                        $allcaps[$cap] = true;
                    }
                    break;
                    
                case 'view_own_soda_clients':
                    if ($this->user_can_view_own_clients($user_id)) {
                        $allcaps[$cap] = true;
                    }
                    break;
                    
                case 'view_assigned_soda_orders':
                    if ($this->user_can_view_assigned_orders($user_id)) {
                        $allcaps[$cap] = true;
                    }
                    break;
                    
                case 'create_own_orders':
                    if ($this->user_can_create_own_orders($user_id)) {
                        $allcaps[$cap] = true;
                    }
                    break;
            }
        }
        
        return $allcaps;
    }
    
    /**
     * =========================================================================
     * VERIFICAÇÕES DE PERMISSÃO ESPECÍFICAS
     * =========================================================================
     */
    
    /**
     * Verifica se usuário pode editar seus próprios clientes
     */
    private function user_can_edit_own_clients($user_id) {
        $user_roles = $this->get_user_roles($user_id);
        
        // Admin Preshh pode editar todos os clientes
        if (in_array('admin_preshh', $user_roles)) {
            return true;
        }
        
        // Franqueado pode editar seus clientes
        if (in_array('franqueado_preshh', $user_roles)) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Verifica se usuário pode visualizar seus próprios clientes
     */
    private function user_can_view_own_clients($user_id) {
        $user_roles = $this->get_user_roles($user_id);
        
        // Admin Preshh pode ver todos os clientes
        if (in_array('admin_preshh', $user_roles)) {
            return true;
        }
        
        // Franqueado pode ver seus clientes
        if (in_array('franqueado_preshh', $user_roles)) {
            return true;
        }
        
        // Admin DVG pode ver clientes
        if (in_array('admin_dvg', $user_roles)) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Verifica se usuário pode visualizar pedidos atribuídos
     */
    private function user_can_view_assigned_orders($user_id) {
        $user_roles = $this->get_user_roles($user_id);
        
        // Distribuidores podem ver pedidos atribuídos a eles
        if (in_array('distribuidor_dvg', $user_roles)) {
            return true;
        }
        
        // Admin Preshh e DVG podem ver todos os pedidos
        if (in_array('admin_preshh', $user_roles) || in_array('admin_dvg', $user_roles)) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Verifica se usuário pode criar seus próprios pedidos
     */
    private function user_can_create_own_orders($user_id) {
        $user_roles = $this->get_user_roles($user_id);
        
        // Cliente final pode criar seus próprios pedidos
        if (in_array('cliente_final', $user_roles)) {
            return $this->client_has_active_status($user_id);
        }
        
        // Franqueado pode criar pedidos para seus clientes
        if (in_array('franqueado_preshh', $user_roles)) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Verifica se cliente tem status ativo
     */
    private function client_has_active_status($user_id) {
        $cliente_id = $this->get_client_id_by_user($user_id);
        
        if (!$cliente_id) {
            return false;
        }
        
        $status = get_field('status', $cliente_id);
        $status_financeiro = get_field('status_financeiro', $cliente_id);
        $pedidos_bloqueados = get_field('pedidos_bloqueados', $cliente_id);
        
        return $status === 'ativo' && 
               $status_financeiro === 'adimplente' && 
               !$pedidos_bloqueados;
    }
    
    /**
     * =========================================================================
     * INTEGRAÇÃO COM BUDDYBOSS
     * =========================================================================
     */
    
    /**
     * Integra com BuddyBoss Platform
     */
    public function integrate_with_buddyboss() {
        if (!function_exists('bp_is_active')) {
            return;
        }
        
        // Configurar componentes do BuddyBoss para roles Soda Perfeita
        add_filter('bp_user_can', array($this, 'buddyboss_capability_check'), 10, 3);
        add_filter('bp_current_user_can', array($this, 'buddyboss_current_user_can'), 10, 2);
        
        // Personalizar exibição baseado no role
        add_action('bp_setup_nav', array($this, 'setup_buddyboss_navigation'), 100);
        
        // Restringir acesso a conteúdos baseado no role
        add_action('template_redirect', array($this, 'buddyboss_content_restrictions'));
    }
    
    /**
     * Verificação de capabilities para BuddyBoss
     */
    public function buddyboss_capability_check($can, $user_id, $capability) {
        // Mapear capabilities do BuddyBoss para as nossas
        $capability_map = array(
            'bp_moderate' => 'manage_soda_system',
            'bp_read' => 'access_soda_dashboard'
        );
        
        if (isset($capability_map[$capability])) {
            return $this->user_has_capability($user_id, $capability_map[$capability]);
        }
        
        return $can;
    }
    
    /**
     * Verificação de capabilities para usuário atual no BuddyBoss
     */
    public function buddyboss_current_user_can($can, $capability) {
        $user_id = get_current_user_id();
        return $this->buddyboss_capability_check($can, $user_id, $capability);
    }
    
    /**
     * Configura navegação do BuddyBoss baseado nos roles
     */
    public function setup_buddyboss_navigation() {
        $user_id = get_current_user_id();
        $user_roles = $this->get_user_roles($user_id);
        
        // Remover itens de navegação baseado no role
        if (!in_array('admin_preshh', $user_roles) && !in_array('administrator', $user_roles)) {
            bp_core_remove_nav_item('admin'); // Remover admin do BuddyBoss
        }
        
        // Adicionar itens customizados para cada role
        if ($this->user_has_capability($user_id, 'access_soda_dashboard')) {
            bp_core_new_nav_item(array(
                'name' => 'Soda Perfeita',
                'slug' => 'soda-perfeita',
                'default_subnav_slug' => 'dashboard',
                'position' => 50,
                'screen_function' => array($this, 'buddyboss_soda_dashboard_screen'),
                'show_for_displayed_user' => false
            ));
            
            // Subnav items
            bp_core_new_subnav_item(array(
                'name' => 'Dashboard',
                'slug' => 'dashboard',
                'parent_url' => bp_loggedin_user_domain() . 'soda-perfeita/',
                'parent_slug' => 'soda-perfeita',
                'screen_function' => array($this, 'buddyboss_soda_dashboard_screen'),
                'position' => 10
            ));
            
            if ($this->user_has_capability($user_id, 'view_own_meritocracia')) {
                bp_core_new_subnav_item(array(
                    'name' => 'Minha Meritocracia',
                    'slug' => 'meritocracia',
                    'parent_url' => bp_loggedin_user_domain() . 'soda-perfeita/',
                    'parent_slug' => 'soda-perfeita',
                    'screen_function' => array($this, 'buddyboss_soda_meritocracia_screen'),
                    'position' => 20
                ));
            }
            
            if ($this->user_has_capability($user_id, 'view_own_orders') || $this->user_has_capability($user_id, 'view_assigned_soda_orders')) {
                bp_core_new_subnav_item(array(
                    'name' => 'Pedidos',
                    'slug' => 'pedidos',
                    'parent_url' => bp_loggedin_user_domain() . 'soda-perfeita/',
                    'parent_slug' => 'soda-perfeita',
                    'screen_function' => array($this, 'buddyboss_soda_pedidos_screen'),
                    'position' => 30
                ));
            }
        }
    }
    
    /**
     * Screen function para dashboard no BuddyBoss
     */
    public function buddyboss_soda_dashboard_screen() {
        add_action('bp_template_title', array($this, 'buddyboss_soda_dashboard_title'));
        add_action('bp_template_content', array($this, 'buddyboss_soda_dashboard_content'));
        bp_core_load_template(apply_filters('bp_core_template_plugin', 'members/single/plugins'));
    }
    
    /**
     * Título do dashboard no BuddyBoss
     */
    public function buddyboss_soda_dashboard_title() {
        echo 'Dashboard Soda Perfeita';
    }
    
    /**
     * Conteúdo do dashboard no BuddyBoss
     */
    public function buddyboss_soda_dashboard_content() {
        echo do_shortcode('[soda_perfeita_dashboard]');
    }
    
    /**
     * Screen function para meritocracia no BuddyBoss
     */
    public function buddyboss_soda_meritocracia_screen() {
        add_action('bp_template_title', array($this, 'buddyboss_soda_meritocracia_title'));
        add_action('bp_template_content', array($this, 'buddyboss_soda_meritocracia_content'));
        bp_core_load_template(apply_filters('bp_core_template_plugin', 'members/single/plugins'));
    }
    
    /**
     * Título da meritocracia no BuddyBoss
     */
    public function buddyboss_soda_meritocracia_title() {
        echo 'Minha Meritocracia';
    }
    
    /**
     * Conteúdo da meritocracia no BuddyBoss
     */
    public function buddyboss_soda_meritocracia_content() {
        echo do_shortcode('[soda_meritocracia_dashboard]');
    }
    
    /**
     * Screen function para pedidos no BuddyBoss
     */
    public function buddyboss_soda_pedidos_screen() {
        add_action('bp_template_title', array($this, 'buddyboss_soda_pedidos_title'));
        add_action('bp_template_content', array($this, 'buddyboss_soda_pedidos_content'));
        bp_core_load_template(apply_filters('bp_core_template_plugin', 'members/single/plugins'));
    }
    
    /**
     * Título dos pedidos no BuddyBoss
     */
    public function buddyboss_soda_pedidos_title() {
        echo 'Meus Pedidos';
    }
    
    /**
     * Conteúdo dos pedidos no BuddyBoss
     */
    public function buddyboss_soda_pedidos_content() {
        echo do_shortcode('[soda_pedidos_lista]');
    }
    
    /**
     * Restrições de conteúdo no BuddyBoss
     */
    public function buddyboss_content_restrictions() {
        if (!bp_is_active()) {
            return;
        }
        
        $user_id = get_current_user_id();
        
        // Restringir acesso a grupos/comunidades baseado no role
        if (bp_is_group() && !$this->user_has_capability($user_id, 'access_soda_dashboard')) {
            bp_core_add_message('Você não tem permissão para acessar esta área.', 'error');
            bp_core_redirect(bp_get_root_domain());
        }
    }
    
    /**
     * =========================================================================
     * INTERFACE ADMINISTRATIVA
     * =========================================================================
     */
    
    /**
     * Adiciona página de administração de roles
     */
    public function add_roles_admin_page() {
        add_users_page(
            'Gestão de Roles - Soda Perfeita',
            'Roles Soda Perfeita',
            'manage_options',
            'soda-perfeita-roles',
            array($this, 'render_roles_admin_page')
        );
    }
    
    /**
     * Renderiza página de administração de roles
     */
    public function render_roles_admin_page() {
        if (!current_user_can('manage_options')) {
            wp_die('Sem permissão para acessar esta página.');
        }
        
        $roles = $this->get_all_roles_with_users();
        ?>
        <div class="wrap">
            <h1>Gestão de Roles - Soda Perfeita</h1>
            
            <div class="soda-roles-admin">
                <!-- Estatísticas Gerais -->
                <div class="stats-cards">
                    <?php foreach ($roles as $role_slug => $role_data): ?>
                    <div class="stats-card">
                        <h3><?php echo esc_html($role_data['display_name']); ?></h3>
                        <div class="user-count"><?php echo count($role_data['users']); ?> usuários</div>
                        <div class="role-description"><?php echo esc_html($role_data['description']); ?></div>
                    </div>
                    <?php endforeach; ?>
                </div>
                
                <!-- Tabela de Usuários por Role -->
                <div class="roles-users-table">
                    <h2>Distribuição de Usuários por Role</h2>
                    <table class="wp-list-table widefat fixed striped">
                        <thead>
                            <tr>
                                <th>Role</th>
                                <th>Usuários</th>
                                <th>Capabilities</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($roles as $role_slug => $role_data): ?>
                            <tr>
                                <td>
                                    <strong><?php echo esc_html($role_data['display_name']); ?></strong>
                                    <br><small><?php echo esc_html($role_slug); ?></small>
                                </td>
                                <td>
                                    <?php if (!empty($role_data['users'])): ?>
                                        <ul>
                                        <?php foreach ($role_data['users'] as $user): ?>
                                            <li>
                                                <a href="<?php echo get_edit_user_link($user->ID); ?>">
                                                    <?php echo esc_html($user->display_name); ?>
                                                </a>
                                            </li>
                                        <?php endforeach; ?>
                                        </ul>
                                    <?php else: ?>
                                        <em>Nenhum usuário</em>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php 
                                    $capabilities = array_slice($role_data['capabilities'], 0, 5);
                                    foreach ($capabilities as $cap => $grant):
                                        if ($grant): ?>
                                        <span class="capability-badge"><?php echo esc_html($cap); ?></span>
                                    <?php endif;
                                    endforeach; 
                                    if (count($role_data['capabilities']) > 5): ?>
                                        <br><small>+<?php echo count($role_data['capabilities']) - 5; ?> mais</small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <button class="button button-small" onclick="sodaPerfeitaViewRoleDetails('<?php echo esc_js($role_slug); ?>')">
                                        Ver Detalhes
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <!-- Ferramentas de Gestão -->
                <div class="management-tools">
                    <h2>Ferramentas de Gestão</h2>
                    <div class="tool-card">
                        <h3>Reset de Roles</h3>
                        <p>Reconfigurar todos os roles e capabilities do sistema</p>
                        <form method="post">
                            <?php wp_nonce_field('soda_perfeita_reset_roles', 'soda_nonce'); ?>
                            <input type="hidden" name="soda_action" value="reset_roles">
                            <button type="submit" class="button button-primary" onclick="return confirm('Tem certeza? Isso redefinirá todas as configurações de roles.')">
                                Resetar Roles
                            </button>
                        </form>
                    </div>
                    
                    <div class="tool-card">
                        <h3>Exportar Configuração</h3>
                        <p>Exportar configuração atual de roles e capabilities</p>
                        <button type="button" class="button" onclick="sodaPerfeitaExportRoles()">
                            Exportar Configuração
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
        <style>
        .soda-roles-admin .stats-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin: 20px 0;
        }
        .stats-card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            border-left: 4px solid #0073aa;
        }
        .stats-card h3 {
            margin: 0 0 10px 0;
            color: #0073aa;
        }
        .user-count {
            font-size: 24px;
            font-weight: bold;
            color: #333;
        }
        .role-description {
            font-size: 12px;
            color: #666;
            margin-top: 10px;
        }
        .capability-badge {
            display: inline-block;
            background: #f1f1f1;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 11px;
            margin: 2px;
        }
        .management-tools {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 30px;
        }
        .tool-card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        </style>
        
        <script>
        function sodaPerfeitaViewRoleDetails(roleSlug) {
            alert('Detalhes do role: ' + roleSlug + '\n\nEsta funcionalidade exibirá informações detalhadas sobre o role selecionado.');
        }
        
        function sodaPerfeitaExportRoles() {
            alert('Exportando configuração de roles...\n\nEsta funcionalidade gerará um arquivo JSON com a configuração atual.');
        }
        </script>
        <?php
    }
    
    /**
     * Processa ações de gestão de roles
     */
    public function handle_roles_management() {
        if (!isset($_POST['soda_action']) || !isset($_POST['soda_nonce'])) {
            return;
        }
        
        if (!wp_verify_nonce($_POST['soda_nonce'], 'soda_perfeita_reset_roles')) {
            wp_die('Token de segurança inválido.');
        }
        
        if ($_POST['soda_action'] === 'reset_roles') {
            $this->setup_roles();
            add_action('admin_notices', function() {
                echo '<div class="notice notice-success is-dismissible"><p>Roles do Soda Perfeita resetados com sucesso!</p></div>';
            });
        }
    }
    
    /**
     * Adiciona campos de role no perfil do usuário
     */
    public function add_user_role_fields($user) {
        if (!current_user_can('manage_options')) {
            return;
        }
        ?>
        <h3>Roles Soda Perfeita</h3>
        <table class="form-table">
            <tr>
                <th><label for="soda_perfeita_roles">Roles Atribuídos</label></th>
                <td>
                    <?php 
                    $current_roles = array_intersect($this->get_user_roles($user->ID), array_keys($this->roles_config));
                    
                    foreach ($this->roles_config as $role_slug => $role_config): ?>
                    <label>
                        <input type="checkbox" name="soda_perfeita_roles[]" value="<?php echo esc_attr($role_slug); ?>" 
                            <?php checked(in_array($role_slug, $current_roles)); ?>>
                        <?php echo esc_html($role_config['display_name']); ?>
                        <small>(<?php echo esc_html($role_config['description']); ?>)</small>
                    </label><br>
                    <?php endforeach; ?>
                    <p class="description">Selecione os roles do Soda Perfeita para este usuário.</p>
                </td>
            </tr>
        </table>
        <?php
    }
    
    /**
     * Salva campos de role do perfil do usuário
     */
    public function save_user_role_fields($user_id) {
        if (!current_user_can('manage_options')) {
            return;
        }
        
        if (isset($_POST['soda_perfeita_roles'])) {
            $selected_roles = array_map('sanitize_text_field', $_POST['soda_perfeita_roles']);
            
            // Primeiro remover todos os roles Soda Perfeita atuais
            $user = get_userdata($user_id);
            $current_roles = $user->roles;
            $soda_roles = array_keys($this->roles_config);
            
            foreach ($soda_roles as $soda_role) {
                if (in_array($soda_role, $current_roles)) {
                    $user->remove_role($soda_role);
                }
            }
            
            // Adicionar roles selecionados
            foreach ($selected_roles as $role_slug) {
                if (array_key_exists($role_slug, $this->roles_config)) {
                    $user->add_role($role_slug);
                }
            }
        }
    }
    
    /**
     * =========================================================================
     * MÉTODOS AUXILIARES
     * =========================================================================
     */
    
    /**
     * Obtém roles de um usuário
     */
    public function get_user_roles($user_id) {
        $user = get_userdata($user_id);
        return $user ? $user->roles : array();
    }
    
    /**
     * Verifica se usuário tem uma capability específica
     */
    public function user_has_capability($user_id, $capability) {
        $user = get_userdata($user_id);
        return $user ? $user->has_cap($capability) : false;
    }
    
    /**
     * Obtém todos os roles com informações detalhadas
     */
    public function get_all_roles_with_users() {
        $roles_data = array();
        
        foreach ($this->roles_config as $role_slug => $role_config) {
            $users = get_users(array(
                'role' => $role_slug,
                'fields' => 'all_with_meta'
            ));
            
            $roles_data[$role_slug] = array(
                'display_name' => $role_config['display_name'],
                'description' => $role_config['description'],
                'capabilities' => $role_config['capabilities'],
                'users' => $users
            );
        }
        
        return $roles_data;
    }
    
    /**
     * Obtém ID do cliente pelo usuário
     */
    private function get_client_id_by_user($user_id) {
        $args = array(
            'post_type' => 'sp_clientes',
            'posts_per_page' => 1,
            'meta_query' => array(
                array(
                    'key' => 'usuario_vinculado',
                    'value' => $user_id,
                    'compare' => '='
                )
            )
        );
        
        $clientes = get_posts($args);
        return $clientes ? $clientes[0]->ID : false;
    }
    
    /**
     * Retorna configuração de roles
     */
    public function get_roles_config() {
        return $this->roles_config;
    }
    
    /**
     * Retorna configuração de capabilities
     */
    public function get_capabilities_config() {
        return $this->capabilities_config;
    }
    
    /**
     * Atribui role a um usuário
     */
    public function assign_role_to_user($user_id, $role_slug) {
        if (!array_key_exists($role_slug, $this->roles_config)) {
            return false;
        }
        
        $user = get_userdata($user_id);
        if (!$user) {
            return false;
        }
        
        $user->add_role($role_slug);
        
        soda_perfeita_log_activity(
            'role_assigned',
            "Role {$role_slug} atribuído ao usuário {$user_id}",
            get_current_user_id()
        );
        
        return true;
    }
    
    /**
     * Remove role de um usuário
     */
    public function remove_role_from_user($user_id, $role_slug) {
        $user = get_userdata($user_id);
        if (!$user) {
            return false;
        }
        
        $user->remove_role($role_slug);
        
        soda_perfeita_log_activity(
            'role_removed',
            "Role {$role_slug} removido do usuário {$user_id}",
            get_current_user_id()
        );
        
        return true;
    }
}