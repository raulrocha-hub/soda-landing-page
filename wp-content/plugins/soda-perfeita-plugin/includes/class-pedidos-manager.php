<?php
/**
 * Orders Management for Soda Perfeita Plugin
 * Sistema customizado de pedidos - SEM WooCommerce
 * 
 * @package SodaPerfeita
 * @since 1.0.0
 */

// Prevenir acesso direto
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Classe principal para gerenciamento de pedidos
 */
class SodaPerfeita_Pedidos_Manager {
    
    public $order_statuses;
    public $price_config;
    
    public function __construct() {
        $this->setup_order_statuses();
        $this->setup_price_config();
    }

    public function init() {
        $this->register_hooks();
    }
    
    /**
     * Configura os status de pedido
     */
    public function setup_order_statuses() {
        $this->order_statuses = array(
            'solicitado' => array(
                'name' => 'Solicitado',
                'description' => 'Pedido solicitado pelo cliente',
                'color' => '#3498db',
                'next_actions' => array('aprovar', 'rejeitar')
            ),
            'aprovado' => array(
                'name' => 'Aprovado',
                'description' => 'Aprovado pela Preshh - aguardando distribuidor',
                'color' => '#2ecc71',
                'next_actions' => array('faturar', 'cancelar')
            ),
            'faturado' => array(
                'name' => 'Faturado',
                'description' => 'Faturado pelo distribuidor - em preparação',
                'color' => '#f39c12',
                'next_actions' => array('enviar', 'cancelar')
            ),
            'enviado' => array(
                'name' => 'Enviado',
                'description' => 'Enviado para o cliente',
                'color' => '#9b59b6',
                'next_actions' => array('entregar', 'problema_entrega')
            ),
            'entregue' => array(
                'name' => 'Entregue',
                'description' => 'Entregue ao cliente com sucesso',
                'color' => '#27ae60',
                'next_actions' => array()
            ),
            'cancelado' => array(
                'name' => 'Cancelado',
                'description' => 'Pedido cancelado',
                'color' => '#e74c3c',
                'next_actions' => array()
            ),
            'rejeitado' => array(
                'name' => 'Rejeitado',
                'description' => 'Pedido rejeitado pela Preshh',
                'color' => '#95a5a6',
                'next_actions' => array()
            )
        );
    }
    
    /**
     * Configura preços e regras comerciais
     */
    public function setup_price_config() {
        $this->price_config = array(
            'preco_unitario' => 45.00,
            'quantidade_minima' => 4,
            'quantidade_maxima' => 100,
            'tiers_precos' => array(
                'tier_1' => 45.00,
                'tier_2' => 45.00,
                'tier_3' => 45.00 // Preço fixo, benefícios são em materiais/subsídios
            )
        );
    }
    
    /**
     * Registra os hooks do WordPress
     */
    public function register_hooks() {
        // AJAX handlers
        add_action('wp_ajax_solicitar_pedido', array($this, 'handle_solicitacao_pedido_ajax'));
        add_action('wp_ajax_aprovar_pedido', array($this, 'handle_aprovacao_pedido_ajax'));
        add_action('wp_ajax_faturar_pedido', array($this, 'handle_faturamento_pedido_ajax'));
        add_action('wp_ajax_entregar_pedido', array($this, 'handle_entrega_pedido_ajax'));
        
        // Shortcodes
        add_shortcode('soda_pedidos_lista', array($this, 'render_pedidos_lista'));
        add_shortcode('soda_pedidos_form', array($this, 'render_pedidos_form'));
        add_shortcode('soda_pedidos_dashboard', array($this, 'render_pedidos_dashboard'));
        
        // Hooks de automação
        add_action('soda_perfeita_pedido_solicitado', array($this, 'handle_pedido_solicitado'), 10, 2);
        add_action('soda_perfeita_pedido_aprovado', array($this, 'handle_pedido_aprovado'), 10, 2);
        add_action('soda_perfeita_pedido_faturado', array($this, 'handle_pedido_faturado'), 10, 2);
        add_action('soda_perfeita_pedido_entregue', array($this, 'handle_pedido_entregue'), 10, 2);
        
        // Filtros para admin
        add_filter('manage_sp_pedidos_posts_columns', array($this, 'add_pedidos_columns'));
        add_action('manage_sp_pedidos_posts_custom_column', array($this, 'manage_pedidos_columns'), 10, 2);
        add_filter('manage_edit-sp_pedidos_sortable_columns', array($this, 'pedidos_sortable_columns'));
        
        // Bulk actions
        add_filter('bulk_actions-edit-sp_pedidos', array($this, 'register_bulk_actions'));
        add_filter('handle_bulk_actions-edit-sp_pedidos', array($this, 'handle_bulk_actions'), 10, 3);
    }
    
    /**
     * =========================================================================
     * HANDLERS AJAX
     * =========================================================================
     */
    
    /**
     * Handler AJAX para solicitação de pedido
     */
    public function handle_solicitacao_pedido_ajax() {
        check_ajax_referer('soda_perfeita_nonce', 'nonce');
        
        $user_id = get_current_user_id();
        $cliente_id = $this->get_cliente_id_by_user($user_id);
        
        if (!$cliente_id) {
            wp_send_json_error('Você precisa estar vinculado a um cliente para fazer pedidos.');
        }
        
        $quantidade = intval($_POST['quantidade']);
        $observacoes = sanitize_textarea_field($_POST['observacoes'] ?? '');
        
        // Validar quantidade
        if ($quantidade < $this->price_config['quantidade_minima'] || $quantidade > $this->price_config['quantidade_maxima']) {
            wp_send_json_error('Quantidade inválida. Mínimo: ' . $this->price_config['quantidade_minima'] . ', Máximo: ' . $this->price_config['quantidade_maxima']);
        }
        
        // Verificar se cliente pode fazer pedidos
        $can_order = $this->verificar_cliente_pode_pedir($cliente_id);
        
        if (!$can_order['success']) {
            wp_send_json_error($can_order['message']);
        }
        
        // Criar pedido
        $result = $this->criar_pedido($cliente_id, $quantidade, $observacoes, $user_id);
        
        if ($result['success']) {
            wp_send_json_success(array(
                'message' => 'Pedido solicitado com sucesso! Aguarde aprovação.',
                'pedido_id' => $result['pedido_id']
            ));
        } else {
            wp_send_json_error($result['message']);
        }
    }
    
    /**
     * Handler AJAX para aprovação de pedido
     */
    public function handle_aprovacao_pedido_ajax() {
        check_ajax_referer('soda_perfeita_nonce', 'nonce');
        
        if (!soda_perfeita_is_admin_preshh()) {
            wp_send_json_error('Sem permissão para aprovar pedidos.');
        }
        
        $pedido_id = intval($_POST['pedido_id']);
        $action = sanitize_text_field($_POST['action_type']); // 'aprovar' ou 'rejeitar'
        
        if ($action === 'aprovar') {
            $result = $this->aprovar_pedido($pedido_id);
        } else {
            $result = $this->rejeitar_pedido($pedido_id, sanitize_textarea_field($_POST['motivo'] ?? ''));
        }
        
        if ($result['success']) {
            wp_send_json_success($result['message']);
        } else {
            wp_send_json_error($result['message']);
        }
    }
    
    /**
     * Handler AJAX para faturamento de pedido
     */
    public function handle_faturamento_pedido_ajax() {
        check_ajax_referer('soda_perfeita_nonce', 'nonce');
        
        if (!soda_perfeita_is_distribuidor()) {
            wp_send_json_error('Sem permissão para faturar pedidos.');
        }
        
        $pedido_id = intval($_POST['pedido_id']);
        $result = $this->faturar_pedido($pedido_id);
        
        if ($result['success']) {
            wp_send_json_success($result['message']);
        } else {
            wp_send_json_error($result['message']);
        }
    }
    
    /**
     * Handler AJAX para entrega de pedido
     */
    public function handle_entrega_pedido_ajax() {
        check_ajax_referer('soda_perfeita_nonce', 'nonce');
        
        if (!soda_perfeita_is_distribuidor()) {
            wp_send_json_error('Sem permissão para registrar entregas.');
        }
        
        $pedido_id = intval($_POST['pedido_id']);
        $result = $this->entregar_pedido($pedido_id);
        
        if ($result['success']) {
            wp_send_json_success($result['message']);
        } else {
            wp_send_json_error($result['message']);
        }
    }
    
    /**
     * =========================================================================
     * GERENCIAMENTO DE PEDIDOS
     * =========================================================================
     */
    
    /**
     * Cria um novo pedido
     */
    public function criar_pedido($cliente_id, $quantidade, $observacoes = '', $user_id = null) {
        try {
            $user_id = $user_id ?: get_current_user_id();
            $distribuidor_id = get_field('distribuidor_responsavel', $cliente_id);
            
            if (!$distribuidor_id) {
                throw new Exception('Cliente não possui distribuidor atribuído.');
            }
            
            // Criar post do pedido
            $pedido_id = wp_insert_post(array(
                'post_type' => 'sp_pedidos',
                'post_title' => 'Pedido ' . date('YmdHis') . ' - ' . get_the_title($cliente_id),
                'post_status' => 'publish',
                'post_author' => $user_id
            ));
            
            if (is_wp_error($pedido_id)) {
                throw new Exception('Erro ao criar pedido: ' . $pedido_id->get_error_message());
            }
            
            // Calcular valores
            $valor_unitario = $this->price_config['preco_unitario'];
            $valor_total = $quantidade * $valor_unitario;
            
            // Salvar campos ACF
            update_field('cliente_id', $cliente_id, $pedido_id);
            update_field('distribuidor_id', $distribuidor_id, $pedido_id);
            update_field('quantidade_garrafas', $quantidade, $pedido_id);
            update_field('valor_unitario', $valor_unitario, $pedido_id);
            update_field('valor_total', $valor_total, $pedido_id);
            update_field('status', 'solicitado', $pedido_id);
            update_field('data_pedido', current_time('mysql'), $pedido_id);
            
            if (!empty($observacoes)) {
                update_field('observacoes', $observacoes, $pedido_id);
            }
            
            // Atualizar data do último pedido do cliente
            update_field('data_ultimo_pedido', current_time('mysql'), $cliente_id);
            
            // Disparar ação de pedido solicitado
            do_action('soda_perfeita_pedido_solicitado', $pedido_id, array(
                'cliente_id' => $cliente_id,
                'distribuidor_id' => $distribuidor_id,
                'quantidade' => $quantidade,
                'valor_total' => $valor_total,
                'user_id' => $user_id
            ));
            
            // Log da atividade
            soda_perfeita_log_activity(
                'pedido_criado',
                "Pedido {$pedido_id} criado para cliente {$cliente_id} - {$quantidade} garrafas",
                $user_id
            );
            
            return array(
                'success' => true,
                'pedido_id' => $pedido_id,
                'message' => 'Pedido criado com sucesso'
            );
            
        } catch (Exception $e) {
            return array(
                'success' => false,
                'message' => $e->getMessage()
            );
        }
    }
    
    /**
     * Aprova um pedido
     */
    public function aprovar_pedido($pedido_id) {
        try {
            $pedido = get_post($pedido_id);
            
            if (!$pedido || $pedido->post_type !== 'sp_pedidos') {
                throw new Exception('Pedido não encontrado.');
            }
            
            $status_atual = get_field('status', $pedido_id);
            
            if ($status_atual !== 'solicitado') {
                throw new Exception('Apenas pedidos solicitados podem ser aprovados.');
            }
            
            // Atualizar status
            update_field('status', 'aprovado', $pedido_id);
            update_field('data_aprovacao', current_time('mysql'), $pedido_id);
            update_field('aprovado_por', get_current_user_id(), $pedido_id);
            
            // Disparar ação de pedido aprovado
            do_action('soda_perfeita_pedido_aprovado', $pedido_id, array(
                'cliente_id' => get_field('cliente_id', $pedido_id),
                'distribuidor_id' => get_field('distribuidor_id', $pedido_id),
                'aprovado_por' => get_current_user_id()
            ));
            
            // Log da atividade
            soda_perfeita_log_activity(
                'pedido_aprovado',
                "Pedido {$pedido_id} aprovado por usuário " . get_current_user_id(),
                get_current_user_id()
            );
            
            return array(
                'success' => true,
                'message' => 'Pedido aprovado com sucesso.'
            );
            
        } catch (Exception $e) {
            return array(
                'success' => false,
                'message' => $e->getMessage()
            );
        }
    }
    
    /**
     * Rejeita um pedido
     */
    public function rejeitar_pedido($pedido_id, $motivo = '') {
        try {
            $pedido = get_post($pedido_id);
            
            if (!$pedido || $pedido->post_type !== 'sp_pedidos') {
                throw new Exception('Pedido não encontrado.');
            }
            
            $status_atual = get_field('status', $pedido_id);
            
            if ($status_atual !== 'solicitado') {
                throw new Exception('Apenas pedidos solicitados podem ser rejeitados.');
            }
            
            // Atualizar status
            update_field('status', 'rejeitado', $pedido_id);
            update_field('data_rejeicao', current_time('mysql'), $pedido_id);
            update_field('rejeitado_por', get_current_user_id(), $pedido_id);
            
            if (!empty($motivo)) {
                update_field('motivo_rejeicao', $motivo, $pedido_id);
            }
            
            // Disparar ação de pedido rejeitado
            do_action('soda_perfeita_pedido_rejeitado', $pedido_id, array(
                'cliente_id' => get_field('cliente_id', $pedido_id),
                'motivo' => $motivo,
                'rejeitado_por' => get_current_user_id()
            ));
            
            // Log da atividade
            soda_perfeita_log_activity(
                'pedido_rejeitado',
                "Pedido {$pedido_id} rejeitado. Motivo: {$motivo}",
                get_current_user_id()
            );
            
            return array(
                'success' => true,
                'message' => 'Pedido rejeitado com sucesso.'
            );
            
        } catch (Exception $e) {
            return array(
                'success' => false,
                'message' => $e->getMessage()
            );
        }
    }
    
    /**
     * Fatura um pedido
     */
    public function faturar_pedido($pedido_id) {
        try {
            $pedido = get_post($pedido_id);
            
            if (!$pedido || $pedido->post_type !== 'sp_pedidos') {
                throw new Exception('Pedido não encontrado.');
            }
            
            $status_atual = get_field('status', $pedido_id);
            
            if ($status_atual !== 'aprovado') {
                throw new Exception('Apenas pedidos aprovados podem ser faturados.');
            }
            
            // Atualizar status
            update_field('status', 'faturado', $pedido_id);
            update_field('data_faturamento', current_time('mysql'), $pedido_id);
            update_field('faturado_por', get_current_user_id(), $pedido_id);
            
            // Disparar ação de pedido faturado
            do_action('soda_perfeita_pedido_faturado', $pedido_id, array(
                'cliente_id' => get_field('cliente_id', $pedido_id),
                'distribuidor_id' => get_field('distribuidor_id', $pedido_id),
                'quantidade' => get_field('quantidade_garrafas', $pedido_id),
                'faturado_por' => get_current_user_id()
            ));
            
            // Log da atividade
            soda_perfeita_log_activity(
                'pedido_faturado',
                "Pedido {$pedido_id} faturado por distribuidor " . get_current_user_id(),
                get_current_user_id()
            );
            
            return array(
                'success' => true,
                'message' => 'Pedido faturado com sucesso.'
            );
            
        } catch (Exception $e) {
            return array(
                'success' => false,
                'message' => $e->getMessage()
            );
        }
    }
    
    /**
     * Registra entrega de pedido
     */
    public function entregar_pedido($pedido_id) {
        try {
            $pedido = get_post($pedido_id);
            
            if (!$pedido || $pedido->post_type !== 'sp_pedidos') {
                throw new Exception('Pedido não encontrado.');
            }
            
            $status_atual = get_field('status', $pedido_id);
            
            if ($status_atual !== 'faturado' && $status_atual !== 'enviado') {
                throw new Exception('Apenas pedidos faturados ou enviados podem ser entregues.');
            }
            
            // Atualizar status
            update_field('status', 'entregue', $pedido_id);
            update_field('data_entrega', current_time('mysql'), $pedido_id);
            update_field('entregue_por', get_current_user_id(), $pedido_id);
            
            // Disparar ação de pedido entregue
            do_action('soda_perfeita_pedido_entregue', $pedido_id, array(
                'cliente_id' => get_field('cliente_id', $pedido_id),
                'distribuidor_id' => get_field('distribuidor_id', $pedido_id),
                'quantidade' => get_field('quantidade_garrafas', $pedido_id),
                'entregue_por' => get_current_user_id()
            ));
            
            // Log da atividade
            soda_perfeita_log_activity(
                'pedido_entregue',
                "Pedido {$pedido_id} marcado como entregue",
                get_current_user_id()
            );
            
            return array(
                'success' => true,
                'message' => 'Pedido marcado como entregue com sucesso.'
            );
            
        } catch (Exception $e) {
            return array(
                'success' => false,
                'message' => $e->getMessage()
            );
        }
    }
    
    /**
     * =========================================================================
     * VERIFICAÇÕES E VALIDAÇÕES
     * =========================================================================
     */
    
    /**
     * Verifica se cliente pode fazer pedidos
     */
    public function verificar_cliente_pode_pedir($cliente_id) {
        $status = get_field('status', $cliente_id);
        $status_financeiro = get_field('status_financeiro', $cliente_id);
        $pedidos_bloqueados = get_field('pedidos_bloqueados', $cliente_id);
        
        // Verificar status geral
        if ($status !== 'ativo') {
            return array(
                'success' => false,
                'message' => 'Cliente não está ativo no sistema.'
            );
        }
        
        // Verificar status financeiro
        if ($status_financeiro !== 'adimplente') {
            return array(
                'success' => false,
                'message' => 'Cliente possui pendências financeiras. Pedido não autorizado.'
            );
        }
        
        // Verificar se pedidos estão bloqueados
        if ($pedidos_bloqueados) {
            return array(
                'success' => false,
                'message' => 'Pedidos bloqueados para este cliente.'
            );
        }
        
        // Verificar se tem distribuidor atribuído
        $distribuidor_id = get_field('distribuidor_responsavel', $cliente_id);
        if (!$distribuidor_id) {
            return array(
                'success' => false,
                'message' => 'Cliente não possui distribuidor atribuído.'
            );
        }
        
        return array('success' => true);
    }
    
    /**
     * Verifica status financeiro do cliente (para API)
     */
    public function verificar_status_financeiro($request) {
        $cliente_id = $request->get_param('cliente_id');
        
        if (!$cliente_id) {
            return new WP_Error('missing_client', 'Cliente ID é obrigatório.', array('status' => 400));
        }
        
        $pode_pedir = $this->verificar_cliente_pode_pedir($cliente_id);
        
        return rest_ensure_response(array(
            'pode_pedir' => $pode_pedir['success'],
            'message' => $pode_pedir['message'] ?? '',
            'cliente_id' => $cliente_id
        ));
    }
    
    /**
     * =========================================================================
     * HANDLERS DE AUTOMAÇÃO
     * =========================================================================
     */
    
    /**
     * Handler para pedido solicitado
     */
    public function handle_pedido_solicitado($pedido_id, $dados_pedido) {
        // Notificar Admin Preshh
        $this->notificar_admin_preshh_pedido($pedido_id);
        
        // Atualizar métricas do cliente
        $this->atualizar_metricas_cliente($dados_pedido['cliente_id']);
    }
    
    /**
     * Handler para pedido aprovado
     */
    public function handle_pedido_aprovado($pedido_id, $dados_pedido) {
        // Notificar distribuidor
        $this->notificar_distribuidor_pedido($pedido_id);
        
        // Atualizar meritocracia
        do_action('soda_perfeita_pedido_aprovado', $pedido_id, $dados_pedido);
    }
    
    /**
     * Handler para pedido faturado
     */
    public function handle_pedido_faturado($pedido_id, $dados_pedido) {
        // Atualizar dashboard
        $this->atualizar_dashboard_pedidos();
        
        // Atualizar meritocracia
        do_action('soda_perfeita_pedido_faturado', $pedido_id, $dados_pedido);
    }
    
    /**
     * Handler para pedido entregue
     */
    public function handle_pedido_entregue($pedido_id, $dados_pedido) {
        // Finalizar ciclo do pedido
        $this->finalizar_pedido($pedido_id);
        
        // Atualizar meritocracia
        do_action('soda_perfeita_pedido_entregue', $pedido_id, $dados_pedido);
        
        // Verificar evolução de tier
        do_action('soda_perfeita_check_tier_evolution', null, array('cliente_id' => $dados_pedido['cliente_id']));
    }
    
    /**
     * =========================================================================
     * NOTIFICAÇÕES
     * =========================================================================
     */
    
    /**
     * Notifica admin Preshh sobre novo pedido
     */
    public function notificar_admin_preshh_pedido($pedido_id) {
        $admin_email = soda_perfeita_get_option('email_admin_preshh', get_option('admin_email'));
        $cliente_id = get_field('cliente_id', $pedido_id);
        $quantidade = get_field('quantidade_garrafas', $pedido_id);
        
        $subject = "Novo Pedido Solicitado - #{$pedido_id}";
        $message = "
            <h2>Novo Pedido Solicitado</h2>
            <p>Um novo pedido foi solicitado e aguarda sua aprovação.</p>
            <p><strong>Pedido ID:</strong> #{$pedido_id}</p>
            <p><strong>Cliente:</strong> " . get_the_title($cliente_id) . "</p>
            <p><strong>Quantidade:</strong> {$quantidade} garrafas</p>
            <p><strong>Valor Total:</strong> R$ " . number_format(get_field('valor_total', $pedido_id), 2, ',', '.') . "</p>
            <p><a href='" . admin_url("post.php?post={$pedido_id}&action=edit") . "'>Aprovar ou Rejeitar Pedido</a></p>
        ";
        
        soda_perfeita_send_email($admin_email, $subject, $message);
    }
    
    /**
     * Notifica distribuidor sobre pedido aprovado
     */
    public function notificar_distribuidor_pedido($pedido_id) {
        $distribuidor_id = get_field('distribuidor_id', $pedido_id);
        $distribuidor_email = get_field('email', $distribuidor_id);
        
        if (!$distribuidor_email) {
            return;
        }
        
        $cliente_id = get_field('cliente_id', $pedido_id);
        $quantidade = get_field('quantidade_garrafas', $pedido_id);
        
        $subject = "Pedido Aprovado - Pronto para Faturamento - #{$pedido_id}";
        $message = "
            <h2>Pedido Aprovado - Pronto para Faturamento</h2>
            <p>Um pedido foi aprovado pela Preshh e está pronto para faturamento.</p>
            <p><strong>Pedido ID:</strong> #{$pedido_id}</p>
            <p><strong>Cliente:</strong> " . get_the_title($cliente_id) . "</p>
            <p><strong>Endereço:</strong> " . get_field('endereco', $cliente_id) . "</p>
            <p><strong>Quantidade:</strong> {$quantidade} garrafas</p>
            <p><strong>Valor Total:</strong> R$ " . number_format(get_field('valor_total', $pedido_id), 2, ',', '.') . "</p>
            <p><a href='" . admin_url("post.php?post={$pedido_id}&action=edit") . "'>Ver Detalhes do Pedido</a></p>
        ";
        
        soda_perfeita_send_email($distribuidor_email, $subject, $message);
    }
    
    /**
     * =========================================================================
     * RENDERIZAÇÃO FRONT-END
     * =========================================================================
     */
    
    /**
     * Renderiza lista de pedidos
     */
    public function render_pedidos_lista($atts = array()) {
        $user_id = get_current_user_id();
        $user_role = soda_perfeita_get_current_user_role();
        
        $atts = shortcode_atts(array(
            'limit' => 10,
            'status' => '',
            'cliente_id' => ''
        ), $atts);
        
        ob_start();
        ?>
        <div class="soda-pedidos-lista">
            <div class="pedidos-header">
                <h3>Meus Pedidos</h3>
                <div class="pedidos-filters">
                    <select id="filter-status" onchange="sodaPerfeitaFilterPedidos()">
                        <option value="">Todos os Status</option>
                        <?php foreach ($this->order_statuses as $status => $config): ?>
                        <option value="<?php echo esc_attr($status); ?>"><?php echo esc_html($config['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            
            <div class="pedidos-container" id="pedidos-container">
                <?php echo $this->get_pedidos_html($user_id, $user_role, $atts); ?>
            </div>
        </div>
        
        <script>
        function sodaPerfeitaFilterPedidos() {
            const status = document.getElementById('filter-status').value;
            const container = document.getElementById('pedidos-container');
            
            // Mostrar loading
            container.innerHTML = '<div class="loading">Carregando...</div>';
            
            // Fazer requisição AJAX
            fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams({
                    action: 'filter_pedidos',
                    status: status,
                    nonce: '<?php echo wp_create_nonce('soda_perfeita_nonce'); ?>'
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    container.innerHTML = data.data.html;
                } else {
                    container.innerHTML = '<div class="error">Erro ao carregar pedidos.</div>';
                }
            });
        }
        </script>
        
        <style>
        .soda-pedidos-lista {
            max-width: 100%;
        }
        .pedidos-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .pedido-card {
            background: white;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 15px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            border-left: 4px solid #3498db;
        }
        .pedido-card.entregue { border-left-color: #27ae60; }
        .pedido-card.faturado { border-left-color: #f39c12; }
        .pedido-card.aprovado { border-left-color: #2ecc71; }
        .pedido-card.rejeitado { border-left-color: #e74c3c; }
        .pedido-card.cancelado { border-left-color: #95a5a6; }
        
        .pedido-info {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 10px;
            margin-bottom: 15px;
        }
        .pedido-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        .btn-action {
            padding: 5px 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
        }
        .btn-approve { background: #2ecc71; color: white; }
        .btn-reject { background: #e74c3c; color: white; }
        .btn-invoice { background: #f39c12; color: white; }
        .btn-deliver { background: #9b59b6; color: white; }
        </style>
        <?php
        return ob_get_clean();
    }
    
    /**
     * Obtém HTML dos pedidos
     */
    public function get_pedidos_html($user_id, $user_role, $atts = array()) {
        $args = array(
            'post_type' => 'sp_pedidos',
            'posts_per_page' => $atts['limit'],
            'meta_query' => array()
        );
        
        // Filtrar por cliente (se for cliente final ou franqueado)
        if (in_array($user_role, array('cliente_final', 'franqueado_preshh'))) {
            $cliente_id = $this->get_cliente_id_by_user($user_id);
            if ($cliente_id) {
                $args['meta_query'][] = array(
                    'key' => 'cliente_id',
                    'value' => $cliente_id,
                    'compare' => '='
                );
            }
        }
        
        // Filtrar por distribuidor
        if ($user_role === 'distribuidor_dvg') {
            $distribuidor_id = $this->get_distribuidor_id_by_user($user_id);
            if ($distribuidor_id) {
                $args['meta_query'][] = array(
                    'key' => 'distribuidor_id',
                    'value' => $distribuidor_id,
                    'compare' => '='
                );
            }
        }
        
        // Filtrar por status
        if (!empty($atts['status'])) {
            $args['meta_query'][] = array(
                'key' => 'status',
                'value' => $atts['status'],
                'compare' => '='
            );
        }
        
        $pedidos = get_posts($args);
        
        if (empty($pedidos)) {
            return '<p>Nenhum pedido encontrado.</p>';
        }
        
        ob_start();
        foreach ($pedidos as $pedido) {
            $this->render_pedido_card($pedido->ID, $user_role);
        }
        return ob_get_clean();
    }
    
    /**
     * Renderiza card individual do pedido
     */
    public function render_pedido_card($pedido_id, $user_role) {
        $status = get_field('status', $pedido_id);
        $status_config = $this->order_statuses[$status];
        $cliente_id = get_field('cliente_id', $pedido_id);
        $distribuidor_id = get_field('distribuidor_id', $pedido_id);
        ?>
        <div class="pedido-card <?php echo esc_attr($status); ?>">
            <div class="pedido-header">
                <h4>Pedido #<?php echo esc_html($pedido_id); ?></h4>
                <span class="pedido-status" style="background: <?php echo esc_attr($status_config['color']); ?>">
                    <?php echo esc_html($status_config['name']); ?>
                </span>
            </div>
            
            <div class="pedido-info">
                <div>
                    <strong>Cliente:</strong><br>
                    <?php echo esc_html(get_the_title($cliente_id)); ?>
                </div>
                <div>
                    <strong>Distribuidor:</strong><br>
                    <?php echo esc_html(get_the_title($distribuidor_id)); ?>
                </div>
                <div>
                    <strong>Quantidade:</strong><br>
                    <?php echo esc_html(get_field('quantidade_garrafas', $pedido_id)); ?> garrafas
                </div>
                <div>
                    <strong>Valor Total:</strong><br>
                    R$ <?php echo number_format(get_field('valor_total', $pedido_id), 2, ',', '.'); ?>
                </div>
                <div>
                    <strong>Data:</strong><br>
                    <?php echo get_the_date('d/m/Y', $pedido_id); ?>
                </div>
            </div>
            
            <?php if (!empty($status_config['next_actions'])): ?>
            <div class="pedido-actions">
                <?php foreach ($status_config['next_actions'] as $action): ?>
                    <?php if ($this->user_can_perform_action($user_role, $action, $pedido_id)): ?>
                    <button class="btn-action btn-<?php echo esc_attr($action); ?>" 
                            onclick="sodaPerfeitaPedidoAction(<?php echo $pedido_id; ?>, '<?php echo $action; ?>')">
                        <?php echo $this->get_action_label($action); ?>
                    </button>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
        <?php
    }
    
    /**
     * =========================================================================
     * MÉTODOS AUXILIARES
     * =========================================================================
     */
    
    /**
     * Obtém ID do cliente pelo usuário
     */
    public function get_cliente_id_by_user($user_id) {
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
     * Obtém ID do distribuidor pelo usuário
     */
    public function get_distribuidor_id_by_user($user_id) {
        $args = array(
            'post_type' => 'sp_distribuidores',
            'posts_per_page' => 1,
            'meta_query' => array(
                array(
                    'key' => 'usuario_vinculado',
                    'value' => $user_id,
                    'compare' => '='
                )
            )
        );
        
        $distribuidores = get_posts($args);
        return $distribuidores ? $distribuidores[0]->ID : false;
    }
    
    /**
     * Verifica se usuário pode realizar ação
     */
    public function user_can_perform_action($user_role, $action, $pedido_id) {
        $action_permissions = array(
            'aprovar' => array('admin_preshh'),
            'rejeitar' => array('admin_preshh'),
            'faturar' => array('distribuidor_dvg', 'admin_preshh'),
            'enviar' => array('distribuidor_dvg'),
            'entregar' => array('distribuidor_dvg'),
            'cancelar' => array('admin_preshh', 'distribuidor_dvg')
        );
        
        return in_array($user_role, $action_permissions[$action] ?? array());
    }
    
    /**
     * Obtém label da ação
     */
    public function get_action_label($action) {
        $labels = array(
            'aprovar' => 'Aprovar',
            'rejeitar' => 'Rejeitar',
            'faturar' => 'Faturar',
            'enviar' => 'Enviar',
            'entregar' => 'Entregue',
            'cancelar' => 'Cancelar'
        );
        
        return $labels[$action] ?? $action;
    }
    
    /**
     * Atualiza métricas do cliente
     */
    public function atualizar_metricas_cliente($cliente_id) {
        // Implementar atualização de métricas como total de pedidos, valor médio, etc.
        update_field('data_ultimo_pedido', current_time('mysql'), $cliente_id);
    }
    
    /**
     * Atualiza dashboard de pedidos
     */
    public function atualizar_dashboard_pedidos() {
        // Forçar atualização de caches do dashboard
        delete_transient('soda_perfeita_pedidos_stats');
    }
    
    /**
     * Finaliza pedido (ações pós-entrega)
     */
    public function finalizar_pedido($pedido_id) {
        // Implementar ações de finalização se necessário
    }
    
    /**
     * =========================================================================
     * ADMIN COLUMNS E BULK ACTIONS
     * =========================================================================
     */
    
    /**
     * Adiciona colunas personalizadas na listagem de pedidos
     */
    public function add_pedidos_columns($columns) {
        $new_columns = array(
            'cb' => $columns['cb'],
            'title' => 'Pedido',
            'cliente' => 'Cliente',
            'distribuidor' => 'Distribuidor',
            'quantidade' => 'Qtd',
            'valor_total' => 'Valor Total',
            'status' => 'Status',
            'data_pedido' => 'Data Pedido',
            'date' => $columns['date']
        );
        
        return $new_columns;
    }
    
    /**
     * Gerencia o conteúdo das colunas personalizadas
     */
    public function manage_pedidos_columns($column, $post_id) {
        switch ($column) {
            case 'cliente':
                $cliente_id = get_field('cliente_id', $post_id);
                if ($cliente_id) {
                    echo '<a href="' . admin_url("post.php?post={$cliente_id}&action=edit") . '">';
                    echo esc_html(get_the_title($cliente_id));
                    echo '</a>';
                }
                break;
                
            case 'distribuidor':
                $distribuidor_id = get_field('distribuidor_id', $post_id);
                if ($distribuidor_id) {
                    echo esc_html(get_the_title($distribuidor_id));
                }
                break;
                
            case 'quantidade':
                echo esc_html(get_field('quantidade_garrafas', $post_id));
                break;
                
            case 'valor_total':
                $valor_total = get_field('valor_total', $post_id);
                echo 'R$ ' . number_format($valor_total, 2, ',', '.');
                break;
                
            case 'status':
                $status = get_field('status', $post_id);
                $status_config = $this->order_statuses[$status];
                echo '<span class="status-badge" style="background: ' . esc_attr($status_config['color']) . '">';
                echo esc_html($status_config['name']);
                echo '</span>';
                break;
                
            case 'data_pedido':
                $data_pedido = get_field('data_pedido', $post_id);
                if ($data_pedido) {
                    echo date('d/m/Y H:i', strtotime($data_pedido));
                }
                break;
        }
    }
    
    /**
     * Define colunas ordenáveis
     */
    public function pedidos_sortable_columns($columns) {
        $columns['data_pedido'] = 'data_pedido';
        $columns['valor_total'] = 'valor_total';
        return $columns;
    }
    
    /**
     * Registra bulk actions
     */
    public function register_bulk_actions($bulk_actions) {
        $bulk_actions['approve_orders'] = 'Aprovar Pedidos';
        $bulk_actions['reject_orders'] = 'Rejeitar Pedidos';
        return $bulk_actions;
    }
    
    /**
     * Handler para bulk actions
     */
    public function handle_bulk_actions($redirect_to, $doaction, $post_ids) {
        if (!in_array($doaction, array('approve_orders', 'reject_orders'))) {
            return $redirect_to;
        }
        
        foreach ($post_ids as $post_id) {
            if ($doaction === 'approve_orders') {
                $this->aprovar_pedido($post_id);
            } elseif ($doaction === 'reject_orders') {
                $this->rejeitar_pedido($post_id, 'Ação em massa');
            }
        }
        
        $redirect_to = add_query_arg('bulk_processed', count($post_ids), $redirect_to);
        return $redirect_to;
    }
    
    /**
     * Retorna configuração de status
     */
    public function get_order_statuses() {
        return $this->order_statuses;
    }
    
    /**
     * Retorna configuração de preços
     */
    public function get_price_config() {
        return $this->price_config;
    }
}