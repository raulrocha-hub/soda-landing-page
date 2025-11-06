<?php
/**
 * Form Handler for Soda Perfeita Plugin - ACF Version
 * 
 * @package SodaPerfeita
 * @since 1.0.0
 */

// Prevenir acesso direto
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Classe principal para gerenciamento de formulários com ACF
 */
class SodaPerfeita_Form_Handler {
    
    private $forms;
    
    public function __construct() {
        $this->setup_forms();
        $this->register_hooks();
        $this->register_acf_forms();
    }
    
    /**
     * Configura os formulários do sistema
     */
    private function setup_forms() {
        $this->forms = array(
            'cadastro_cliente' => array(
                'name' => 'Cadastro de Cliente',
                'post_id' => 'new_client',
                'field_groups' => array('group_sp_clientes_cadastro'),
                'submit_value' => 'Cadastrar Cliente',
                'updated_message' => 'Cliente cadastrado com sucesso! Aguarde aprovação.',
                'handler' => 'process_cadastro_cliente'
            ),
            'solicitacao_pedido' => array(
                'name' => 'Solicitação de Pedido',
                'post_id' => 'new_order',
                'field_groups' => array('group_sp_pedidos_form'),
                'submit_value' => 'Solicitar Pedido',
                'updated_message' => 'Pedido solicitado com sucesso!',
                'handler' => 'process_solicitacao_pedido'
            ),
            'contato_suporte' => array(
                'name' => 'Contato com Suporte',
                'post_id' => 'new_contact',
                'field_groups' => array('group_sp_contato_form'),
                'submit_value' => 'Enviar Mensagem',
                'updated_message' => 'Mensagem enviada com sucesso!',
                'handler' => 'process_contato_suporte'
            )
        );
    }
    
    /**
     * Registra os hooks do WordPress
     */
    private function register_hooks() {
        // Shortcodes
        add_shortcode('soda_form_cadastro_cliente', array($this, 'render_cadastro_cliente_form'));
        add_shortcode('soda_form_pedido', array($this, 'render_pedido_form'));
        add_shortcode('soda_form_contato', array($this, 'render_contato_form'));
        
        // Handlers de submissão
        add_action('acf/save_post', array($this, 'handle_acf_form_submission'), 5);
    }
    
    /**
     * Registra os field groups para formulários ACF
     */
    private function register_acf_forms() {
        if (!function_exists('acf_add_local_field_group')) return;
        
        // Field Group para Cadastro de Cliente
        acf_add_local_field_group(array(
            'key' => 'group_sp_clientes_cadastro',
            'title' => 'Formulário de Cadastro - Cliente',
            'fields' => $this->get_cadastro_cliente_fields(),
            'location' => array(
                array(
                    array(
                        'param' => 'options_page',
                        'operator' => '==',
                        'value' => 'acf-options-formularios',
                    ),
                ),
            ),
            'style' => 'seamless',
        ));
        
        // Field Group para Solicitação de Pedido
        acf_add_local_field_group(array(
            'key' => 'group_sp_pedidos_form',
            'title' => 'Formulário de Pedido',
            'fields' => $this->get_pedido_fields(),
            'location' => array(
                array(
                    array(
                        'param' => 'options_page',
                        'operator' => '==',
                        'value' => 'acf-options-formularios',
                    ),
                ),
            ),
            'style' => 'seamless',
        ));
        
        // Field Group para Contato
        acf_add_local_field_group(array(
            'key' => 'group_sp_contato_form',
            'title' => 'Formulário de Contato',
            'fields' => $this->get_contato_fields(),
            'location' => array(
                array(
                    array(
                        'param' => 'options_page',
                        'operator' => '==',
                        'value' => 'acf-options-formularios',
                    ),
                ),
            ),
            'style' => 'seamless',
        ));
    }
    
    /**
     * Campos para formulário de cadastro de cliente
     */
    private function get_cadastro_cliente_fields() {
        return array(
            array(
                'key' => 'field_form_cliente_nome',
                'label' => 'Nome do Estabelecimento',
                'name' => 'nome_estabelecimento',
                'type' => 'text',
                'required' => 1,
                'wrapper' => array(
                    'width' => '100',
                ),
            ),
            array(
                'key' => 'field_form_cliente_cnpj',
                'label' => 'CNPJ',
                'name' => 'cnpj',
                'type' => 'text',
                'required' => 1,
                'instructions' => 'Apenas números',
                'wrapper' => array(
                    'width' => '50',
                ),
            ),
            array(
                'key' => 'field_form_cliente_segmento',
                'label' => 'Segmento',
                'name' => 'segmento',
                'type' => 'select',
                'choices' => array(
                    'cafeteria' => 'Cafeteria',
                    'bar' => 'Bar',
                    'restaurante' => 'Restaurante',
                    'lanchonete' => 'Lanchonete',
                    'hotel' => 'Hotel',
                    'outro' => 'Outro',
                ),
                'wrapper' => array(
                    'width' => '50',
                ),
            ),
            array(
                'key' => 'field_form_cliente_email',
                'label' => 'Email',
                'name' => 'email',
                'type' => 'email',
                'required' => 1,
                'wrapper' => array(
                    'width' => '50',
                ),
            ),
            array(
                'key' => 'field_form_cliente_telefone',
                'label' => 'Telefone',
                'name' => 'telefone',
                'type' => 'text',
                'wrapper' => array(
                    'width' => '50',
                ),
            ),
            array(
                'key' => 'field_form_cliente_endereco',
                'label' => 'Endereço Completo',
                'name' => 'endereco_completo',
                'type' => 'textarea',
                'rows' => 3,
                'wrapper' => array(
                    'width' => '100',
                ),
            ),
            array(
                'key' => 'field_form_cliente_volume_previsto',
                'label' => 'Volume Previsto (garrafas/mês)',
                'name' => 'volume_previsto',
                'type' => 'select',
                'choices' => array(
                    '4' => '4 (Mínimo)',
                    '8' => '8',
                    '12' => '12',
                    '16' => '16',
                    '20' => '20+',
                ),
                'default_value' => '4',
                'wrapper' => array(
                    'width' => '50',
                ),
            ),
            array(
                'key' => 'field_form_cliente_observacoes',
                'label' => 'Observações Adicionais',
                'name' => 'observacoes_cadastro',
                'type' => 'textarea',
                'rows' => 2,
                'wrapper' => array(
                    'width' => '50',
                ),
            ),
        );
    }
    
    /**
     * Campos para formulário de pedido
     */
    private function get_pedido_fields() {
        return array(
            array(
                'key' => 'field_form_pedido_quantidade',
                'label' => 'Quantidade de Garrafas',
                'name' => 'quantidade_garrafas',
                'type' => 'select',
                'required' => 1,
                'choices' => array(
                    '4' => '4 garrafas - R$ 180,00',
                    '8' => '8 garrafas - R$ 360,00',
                    '12' => '12 garrafas - R$ 540,00',
                    '16' => '16 garrafas - R$ 720,00',
                    '20' => '20 garrafas - R$ 900,00',
                    '24' => '24 garrafas - R$ 1.080,00',
                ),
                'wrapper' => array(
                    'width' => '50',
                ),
            ),
            array(
                'key' => 'field_form_pedido_data_entrega',
                'label' => 'Data de Entrega Preferida',
                'name' => 'data_entrega_preferida',
                'type' => 'date_picker',
                'display_format' => 'd/m/Y',
                'return_format' => 'Y-m-d',
                'min_date' => date('Y-m-d', strtotime('+1 day')),
                'wrapper' => array(
                    'width' => '50',
                ),
            ),
            array(
                'key' => 'field_form_pedido_observacoes',
                'label' => 'Observações do Pedido',
                'name' => 'observacoes_pedido',
                'type' => 'textarea',
                'rows' => 3,
                'wrapper' => array(
                    'width' => '100',
                ),
            ),
        );
    }
    
    /**
     * Campos para formulário de contato
     */
    private function get_contato_fields() {
        return array(
            array(
                'key' => 'field_form_contato_nome',
                'label' => 'Seu Nome',
                'name' => 'nome_contato',
                'type' => 'text',
                'required' => 1,
                'wrapper' => array(
                    'width' => '50',
                ),
            ),
            array(
                'key' => 'field_form_contato_email',
                'label' => 'Seu Email',
                'name' => 'email_contato',
                'type' => 'email',
                'required' => 1,
                'wrapper' => array(
                    'width' => '50',
                ),
            ),
            array(
                'key' => 'field_form_contato_telefone',
                'label' => 'Telefone',
                'name' => 'telefone_contato',
                'type' => 'text',
                'wrapper' => array(
                    'width' => '50',
                ),
            ),
            array(
                'key' => 'field_form_contato_tipo',
                'label' => 'Tipo de Contato',
                'name' => 'tipo_contato',
                'type' => 'select',
                'choices' => array(
                    'duvida' => 'Dúvida',
                    'sugestao' => 'Sugestão',
                    'problema' => 'Problema Técnico',
                    'outro' => 'Outro',
                ),
                'wrapper' => array(
                    'width' => '50',
                ),
            ),
            array(
                'key' => 'field_form_contato_assunto',
                'label' => 'Assunto',
                'name' => 'assunto_contato',
                'type' => 'text',
                'required' => 1,
                'wrapper' => array(
                    'width' => '100',
                ),
            ),
            array(
                'key' => 'field_form_contato_mensagem',
                'label' => 'Mensagem',
                'name' => 'mensagem_contato',
                'type' => 'textarea',
                'required' => 1,
                'rows' => 5,
                'wrapper' => array(
                    'width' => '100',
                ),
            ),
        );
    }
    
    /**
     * Handler principal para submissões de formulários ACF
     */
    public function handle_acf_form_submission($post_id) {
        // Verificar se é um dos nossos formulários
        if (strpos($post_id, 'new_') === false) {
            return;
        }
        
        // Determinar tipo de formulário
        $form_type = str_replace('new_', '', $post_id);
        
        if (!isset($this->forms[$form_type])) {
            return;
        }
        
        // Verificar nonce (já verificado pelo ACF)
        if (!acf_verify_nonce($form_type)) {
            return;
        }
        
        // Validar valores (já validado pelo ACF)
        if (!acf_validate_save_post()) {
            return;
        }
        
        // Processar formulário
        $form_config = $this->forms[$form_type];
        $handler_method = $form_config['handler'];
        
        if (method_exists($this, $handler_method)) {
            $form_data = $_POST['acf']; // Dados já sanitizados pelo ACF
            call_user_func(array($this, $handler_method), $form_data, $form_type);
        }
        
        // Redirecionar para evitar re-submissão
        if (!wp_doing_ajax()) {
            $redirect_url = add_query_arg('form_success', $form_type, wp_get_referer());
            wp_redirect($redirect_url);
            exit;
        }
    }
    
    /**
     * =========================================================================
     * PROCESSADORES DE FORMULÁRIOS
     * =========================================================================
     */
    
    /**
     * Processa cadastro de cliente
     */
    private function process_cadastro_cliente($data, $form_type) {
        try {
            // Verificar se cliente já existe pelo CNPJ
            $existing_client = $this->find_client_by_cnpj($data['field_form_cliente_cnpj']);
            
            if ($existing_client) {
                wp_die('Já existe um cliente cadastrado com este CNPJ.');
            }
            
            // Criar post do cliente
            $client_id = wp_insert_post(array(
                'post_type' => 'sp_clientes',
                'post_title' => $data['field_form_cliente_nome'],
                'post_status' => 'publish',
                'post_author' => get_current_user_id()
            ));
            
            if (is_wp_error($client_id)) {
                throw new Exception('Erro ao criar registro do cliente: ' . $client_id->get_error_message());
            }
            
            // Mapear e salvar campos ACF
            $this->map_form_fields_to_client($client_id, $data);
            
            // Disparar ação de cliente cadastrado
            do_action('soda_perfeita_cliente_cadastrado', $client_id, array(
                'nome_estabelecimento' => $data['field_form_cliente_nome'],
                'cnpj' => $data['field_form_cliente_cnpj'],
                'email' => $data['field_form_cliente_email']
            ));
            
            // Log da atividade
            soda_perfeita_log_activity(
                'cliente_cadastrado_form',
                "Cliente {$client_id} cadastrado via formulário ACF",
                get_current_user_id()
            );
            
        } catch (Exception $e) {
            wp_die('Erro ao processar cadastro: ' . $e->getMessage());
        }
    }
    
    /**
     * Processa solicitação de pedido
     */
    private function process_solicitacao_pedido($data, $form_type) {
        try {
            $user_id = get_current_user_id();
            $cliente_id = $this->get_client_id_by_user($user_id);
            $quantidade = intval($data['field_form_pedido_quantidade']);
            
            if (!$cliente_id) {
                wp_die('Você precisa estar vinculado a um cliente para fazer pedidos.');
            }
            
            // Verificar se cliente está ativo
            $cliente_status = get_field('status', $cliente_id);
            $cliente_bloqueado = get_field('pedidos_bloqueados', $cliente_id);
            
            if (!$cliente_status || $cliente_status !== 'ativo' || $cliente_bloqueado) {
                wp_die('Cliente não está ativo ou possui pedidos bloqueados.');
            }
            
            // Verificar status financeiro
            if (!soda_perfeita_cliente_adimplente($cliente_id)) {
                wp_die('Cliente possui pendências financeiras. Pedido não autorizado.');
            }
            
            // Obter distribuidor do cliente
            $distribuidor_id = get_field('distribuidor_responsavel', $cliente_id);
            
            if (!$distribuidor_id) {
                wp_die('Cliente não possui distribuidor atribuído.');
            }
            
            // Criar post do pedido
            $pedido_id = wp_insert_post(array(
                'post_type' => 'sp_pedidos',
                'post_title' => 'Pedido ' . date('YmdHis'),
                'post_status' => 'publish',
                'post_author' => $user_id
            ));
            
            if (is_wp_error($pedido_id)) {
                throw new Exception('Erro ao criar pedido: ' . $pedido_id->get_error_message());
            }
            
            // Salvar campos do pedido
            $this->save_order_fields($pedido_id, $cliente_id, $distribuidor_id, $quantidade, $data);
            
            // Disparar ação de pedido solicitado
            do_action('soda_perfeita_pedido_solicitado', $pedido_id, array(
                'cliente_id' => $cliente_id,
                'distribuidor_id' => $distribuidor_id,
                'quantidade' => $quantidade
            ));
            
            // Log da atividade
            soda_perfeita_log_activity(
                'pedido_solicitado_form',
                "Pedido {$pedido_id} solicitado via formulário ACF para cliente {$cliente_id}",
                $user_id
            );
            
        } catch (Exception $e) {
            wp_die('Erro ao processar pedido: ' . $e->getMessage());
        }
    }
    
    /**
     * Processa contato com suporte
     */
    private function process_contato_suporte($data, $form_type) {
        try {
            $to = soda_perfeita_get_option('email_suporte', get_option('admin_email'));
            $subject = "Contato via Sistema Soda Perfeita: {$data['field_form_contato_assunto']}";
            
            $message = "
                <h2>Novo Contato via Sistema Soda Perfeita</h2>
                <p><strong>Nome:</strong> {$data['field_form_contato_nome']}</p>
                <p><strong>Email:</strong> {$data['field_form_contato_email']}</p>
                <p><strong>Telefone:</strong> {$data['field_form_contato_telefone']}</p>
                <p><strong>Tipo de Contato:</strong> {$data['field_form_contato_tipo']}</p>
                <p><strong>Assunto:</strong> {$data['field_form_contato_assunto']}</p>
                <p><strong>Mensagem:</strong></p>
                <div style='background: #f9f9f9; padding: 15px; border-left: 4px solid #0073aa;'>
                    " . nl2br(esc_html($data['field_form_contato_mensagem'])) . "
                </div>
            ";
            
            $sent = soda_perfeita_send_email($to, $subject, $message);
            
            if ($sent) {
                soda_perfeita_log_activity(
                    'contato_suporte_form',
                    "Contato de suporte enviado por {$data['field_form_contato_nome']}",
                    get_current_user_id()
                );
            } else {
                throw new Exception('Falha no envio do email.');
            }
            
        } catch (Exception $e) {
            wp_die('Erro ao enviar mensagem: ' . $e->getMessage());
        }
    }
    
    /**
     * =========================================================================
     * RENDERIZADORES DE FORMULÁRIOS
     * =========================================================================
     */
    
    /**
     * Renderiza formulário de cadastro de cliente
     */
    public function render_cadastro_cliente_form($atts = array()) {
        if (!function_exists('acf_form')) {
            return '<p>ACF Pro é necessário para este formulário.</p>';
        }
        
        $form_config = $this->forms['cadastro_cliente'];
        
        ob_start();
        
        // Mostrar mensagem de sucesso
        if (isset($_GET['form_success']) && $_GET['form_success'] === 'cadastro_cliente') {
            echo '<div class="soda-form-success">' . esc_html($form_config['updated_message']) . '</div>';
        }
        ?>
        
        <div class="soda-form-wrapper">
            <?php
            acf_form(array(
                'id' => 'soda-form-cadastro-cliente',
                'post_id' => $form_config['post_id'],
                'field_groups' => $form_config['field_groups'],
                'form' => true,
                'return' => '',
                'submit_value' => $form_config['submit_value'],
                'updated_message' => false, // Nós controlamos a mensagem
                'html_before_fields' => '',
                'html_after_fields' => '',
                'instruction_placement' => 'field',
            ));
            ?>
        </div>
        
        <style>
        .soda-form-wrapper .acf-form {
            max-width: 100%;
        }
        .soda-form-wrapper .acf-field {
            padding: 10px 0;
        }
        .soda-form-success {
            background: #d4edda;
            color: #155724;
            padding: 12px;
            border-radius: 4px;
            margin-bottom: 20px;
            border: 1px solid #c3e6cb;
        }
        </style>
        <?php
        return ob_get_clean();
    }
    
    /**
     * Renderiza formulário de pedido
     */
    public function render_pedido_form($atts = array()) {
        if (!function_exists('acf_form')) {
            return '<p>ACF Pro é necessário para este formulário.</p>';
        }
        
        $user_id = get_current_user_id();
        $cliente_id = $this->get_client_id_by_user($user_id);
        
        if (!$cliente_id) {
            return '<div class="soda-form-error"><p>Você precisa estar vinculado a um cliente para fazer pedidos.</p></div>';
        }
        
        $form_config = $this->forms['solicitacao_pedido'];
        
        ob_start();
        
        // Mostrar mensagem de sucesso
        if (isset($_GET['form_success']) && $_GET['form_success'] === 'solicitacao_pedido') {
            echo '<div class="soda-form-success">' . esc_html($form_config['updated_message']) . '</div>';
        }
        ?>
        
        <div class="soda-form-wrapper">
            <div class="form-header">
                <h3>Novo Pedido de Xarope</h3>
                <p>Preço unitário: <strong>R$ 45,00</strong></p>
            </div>
            
            <?php
            acf_form(array(
                'id' => 'soda-form-pedido',
                'post_id' => $form_config['post_id'],
                'field_groups' => $form_config['field_groups'],
                'form' => true,
                'return' => '',
                'submit_value' => $form_config['submit_value'],
                'updated_message' => false,
                'html_before_fields' => '',
                'html_after_fields' => '',
            ));
            ?>
        </div>
        <?php
        return ob_get_clean();
    }
    
    /**
     * Renderiza formulário de contato
     */
    public function render_contato_form($atts = array()) {
        if (!function_exists('acf_form')) {
            return '<p>ACF Pro é necessário para este formulário.</p>';
        }
        
        $form_config = $this->forms['contato_suporte'];
        
        ob_start();
        
        // Mostrar mensagem de sucesso
        if (isset($_GET['form_success']) && $_GET['form_success'] === 'contato_suporte') {
            echo '<div class="soda-form-success">' . esc_html($form_config['updated_message']) . '</div>';
        }
        ?>
        
        <div class="soda-form-wrapper">
            <?php
            acf_form(array(
                'id' => 'soda-form-contato',
                'post_id' => $form_config['post_id'],
                'field_groups' => $form_config['field_groups'],
                'form' => true,
                'return' => '',
                'submit_value' => $form_config['submit_value'],
                'updated_message' => false,
                'html_before_fields' => '',
                'html_after_fields' => '',
            ));
            ?>
        </div>
        <?php
        return ob_get_clean();
    }
    
    /**
     * =========================================================================
     * MÉTODOS AUXILIARES
     * =========================================================================
     */
    
    /**
     * Mapeia campos do formulário para campos do cliente
     */
    private function map_form_fields_to_client($client_id, $data) {
        $field_mapping = array(
            'field_form_cliente_nome' => 'nome_estabelecimento', // Já salvo no título
            'field_form_cliente_cnpj' => 'cnpj',
            'field_form_cliente_email' => 'email',
            'field_form_cliente_telefone' => 'telefone',
            'field_form_cliente_endereco' => 'endereco',
            'field_form_cliente_segmento' => 'segmento',
            'field_form_cliente_volume_previsto' => 'volume_previsto',
            'field_form_cliente_observacoes' => 'observacoes_cadastro',
        );
        
        foreach ($field_mapping as $form_field => $client_field) {
            if (isset($data[$form_field])) {
                update_field($client_field, $data[$form_field], $client_id);
            }
        }
        
        // Campos padrão
        update_field('status', 'prospeccao', $client_id);
        update_field('status_financeiro', 'adimplente', $client_id);
        update_field('tier_atual', 'tier_1', $client_id);
        update_field('data_adesao', current_time('mysql'), $client_id);
    }
    
    /**
     * Salva campos do pedido
     */
    private function save_order_fields($pedido_id, $cliente_id, $distribuidor_id, $quantidade, $data) {
        $valor_unitario = 45.00;
        $valor_total = $quantidade * $valor_unitario;
        
        update_field('cliente_id', $cliente_id, $pedido_id);
        update_field('distribuidor_id', $distribuidor_id, $pedido_id);
        update_field('quantidade_garrafas', $quantidade, $pedido_id);
        update_field('valor_unitario', $valor_unitario, $pedido_id);
        update_field('valor_total', $valor_total, $pedido_id);
        update_field('status', 'solicitado', $pedido_id);
        update_field('data_pedido', current_time('mysql'), $pedido_id);
        
        if (!empty($data['field_form_pedido_observacoes'])) {
            update_field('observacoes', $data['field_form_pedido_observacoes'], $pedido_id);
        }
        
        if (!empty($data['field_form_pedido_data_entrega'])) {
            update_field('data_entrega_preferida', $data['field_form_pedido_data_entrega'], $pedido_id);
        }
        
        // Atualizar data do último pedido do cliente
        update_field('data_ultimo_pedido', current_time('mysql'), $cliente_id);
    }
    
    /**
     * Encontra cliente pelo CNPJ
     */
    private function find_client_by_cnpj($cnpj) {
        $cnpj_clean = preg_replace('/[^0-9]/', '', $cnpj);
        
        $args = array(
            'post_type' => 'sp_clientes',
            'posts_per_page' => 1,
            'meta_query' => array(
                array(
                    'key' => 'cnpj',
                    'value' => $cnpj_clean,
                    'compare' => '='
                )
            )
        );
        
        $clients = get_posts($args);
        return !empty($clients) ? $clients[0]->ID : false;
    }
    
    /**
     * Obtém ID do cliente vinculado ao usuário
     */
    private function get_client_id_by_user($user_id) {
        if (!$user_id) return false;
        
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
        
        $clients = get_posts($args);
        return !empty($clients) ? $clients[0]->ID : false;
    }
}