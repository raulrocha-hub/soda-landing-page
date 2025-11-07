<?php
/**
 * Automation Management for Soda Perfeita Plugin
 * 
 * @package SodaPerfeita
 * @since 1.0.0
 */

// Prevenir acesso direto
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Classe principal para gerenciamento de automações
 */
class SodaPerfeita_Automations {
    
    private $triggers;
    private $actions;
    
    public function __construct() {
        $this->setup_triggers();
        $this->setup_actions();

        $this->register_hooks();
    }

    public function init() {
        // Método mantido para compatibilidade
    }
    
    /**
     * Configura os gatilhos de automação
     */
    private function setup_triggers() {
        $this->triggers = array(
            // Cadastro e Onboarding
            'cliente_cadastrado' => array(
                'name' => 'Cliente Cadastrado no Sistema',
                'description' => 'Disparado quando um novo cliente é cadastrado no pipeline'
            ),
            'cliente_aprovado' => array(
                'name' => 'Cliente Aprovado pela Preshh',
                'description' => 'Disparado quando a Preshh aprova um cliente no sistema'
            ),
            'contrato_assinado' => array(
                'name' => 'Contrato Assinado',
                'description' => 'Disparado quando o cliente assina o contrato digital'
            ),
            'pagamento_confirmado' => array(
                'name' => 'Pagamento da Adesão Confirmado',
                'description' => 'Disparado quando o pagamento inicial é confirmado'
            ),
            
            // Pedidos e Financeiro
            'pedido_solicitado' => array(
                'name' => 'Pedido de Xarope Solicitado',
                'description' => 'Disparado quando um cliente solicita pedido de xarope'
            ),
            'pedido_aprovado' => array(
                'name' => 'Pedido Aprovado pela Preshh',
                'description' => 'Disparado quando a Preshh aprova um pedido'
            ),
            'pedido_faturado' => array(
                'name' => 'Pedido Faturado pelo Distribuidor',
                'description' => 'Disparado quando o distribuidor marca pedido como faturado'
            ),
            'pedido_entregue' => array(
                'name' => 'Pedido Entregue ao Cliente',
                'description' => 'Disparado quando o pedido é entregue ao cliente'
            ),
            'cliente_inadimplente' => array(
                'name' => 'Cliente Marcado como Inadimplente',
                'description' => 'Disparado quando cliente tem pendência financeira'
            ),
            'cliente_regularizado' => array(
                'name' => 'Cliente Regularizado Financeiramente',
                'description' => 'Disparado quando cliente quita pendências'
            ),
            
            // Treinamentos e Meritocracia
            'treinamento_concluido' => array(
                'name' => 'Treinamento Concluído',
                'description' => 'Disparado quando cliente conclui treinamento no LearnDash'
            ),
            'tier_atualizado' => array(
                'name' => 'Tier do Cliente Atualizado',
                'description' => 'Disparado quando cliente sobe ou desce de tier'
            ),
            'tier3_atingido' => array(
                'name' => 'Tier 3 (Excelência) Atingido',
                'description' => 'Disparado quando cliente atinge o tier máximo'
            ),
            
            // Sistema e Manutenção
            'checkpoint_30_dias' => array(
                'name' => 'Checkpoint 30 Dias',
                'description' => 'Disparado 30 dias após ativação do cliente'
            ),
            'checkpoint_90_dias' => array(
                'name' => 'Checkpoint 90 Dias',
                'description' => 'Disparado a cada 90 dias para avaliação'
            ),
            'checkpoint_180_dias' => array(
                'name' => 'Checkpoint 180 Dias',
                'description' => 'Disparado a cada 180 dias para revisão completa'
            )
        );
    }
    
    /**
     * Configura as ações de automação
     */
    private function setup_actions() {
        $this->actions = array(
            // Notificações por Email
            'enviar_email_admin_preshh' => array(
                'name' => 'Enviar Email para Admin Preshh',
                'callback' => array($this, 'send_email_admin_preshh')
            ),
            'enviar_email_admin_dvg' => array(
                'name' => 'Enviar Email para Admin DVG',
                'callback' => array($this, 'send_email_admin_dvg')
            ),
            'enviar_email_franqueado' => array(
                'name' => 'Enviar Email para Franqueado',
                'callback' => array($this, 'send_email_franqueado')
            ),
            'enviar_email_distribuidor' => array(
                'name' => 'Enviar Email para Distribuidor',
                'callback' => array($this, 'send_email_distribuidor')
            ),
            'enviar_email_cliente' => array(
                'name' => 'Enviar Email para Cliente',
                'callback' => array($this, 'send_email_cliente')
            ),
            
            // Atualizações de Sistema
            'atualizar_status_cliente' => array(
                'name' => 'Atualizar Status do Cliente',
                'callback' => array($this, 'update_cliente_status')
            ),
            'bloquear_pedidos_cliente' => array(
                'name' => 'Bloquear Pedidos do Cliente',
                'callback' => array($this, 'block_cliente_orders')
            ),
            'liberar_pedidos_cliente' => array(
                'name' => 'Liberar Pedidos do Cliente',
                'callback' => array($this, 'release_cliente_orders')
            ),
            'atualizar_tier_cliente' => array(
                'name' => 'Atualizar Tier do Cliente',
                'callback' => array($this, 'update_cliente_tier')
            ),
            
            // Integrações e Notificações
            'notificar_distribuidor_pedido' => array(
                'name' => 'Notificar Distribuidor sobre Pedido',
                'callback' => array($this, 'notify_distributor_order')
            ),
            'criar_tarefa_admin' => array(
                'name' => 'Criar Tarefa para Administração',
                'callback' => array($this, 'create_admin_task')
            ),
            'registrar_log_sistema' => array(
                'name' => 'Registrar Log do Sistema',
                'callback' => array($this, 'log_system_event')
            ),
            'atualizar_dashboard_kpis' => array(
                'name' => 'Atualizar KPIs do Dashboard',
                'callback' => array($this, 'update_dashboard_kpis')
            )
        );
    }
    
    /**
     * Registra os hooks do WordPress
     */
    private function register_hooks() {
        // Hooks de Clientes
        add_action('soda_perfeita_cliente_cadastrado', array($this, 'handle_cliente_cadastrado'), 10, 2);
        add_action('soda_perfeita_cliente_aprovado', array($this, 'handle_cliente_aprovado'), 10, 2);
        add_action('soda_perfeita_contrato_assinado', array($this, 'handle_contrato_assinado'), 10, 2);
        add_action('soda_perfeita_pagamento_confirmado', array($this, 'handle_pagamento_confirmado'), 10, 2);
        
        // Hooks de Pedidos
        add_action('soda_perfeita_pedido_solicitado', array($this, 'handle_pedido_solicitado'), 10, 2);
        add_action('soda_perfeita_pedido_aprovado', array($this, 'handle_pedido_aprovado'), 10, 2);
        add_action('soda_perfeita_pedido_faturado', array($this, 'handle_pedido_faturado'), 10, 2);
        add_action('soda_perfeita_pedido_entregue', array($this, 'handle_pedido_entregue'), 10, 2);
        
        // Hooks Financeiros
        add_action('soda_perfeita_cliente_inadimplente', array($this, 'handle_cliente_inadimplente'), 10, 2);
        add_action('soda_perfeita_cliente_regularizado', array($this, 'handle_cliente_regularizado'), 10, 2);
        
        // Hooks de Treinamento e Meritocracia
        add_action('soda_perfeita_treinamento_concluido', array($this, 'handle_treinamento_concluido'), 10, 2);
        add_action('soda_perfeita_tier_atualizado', array($this, 'handle_tier_atualizado'), 10, 3);
        
        // Hooks de Checkpoints
        add_action('soda_perfeita_checkpoint_30_dias', array($this, 'handle_checkpoint_30_dias'), 10, 1);
        add_action('soda_perfeita_checkpoint_90_dias', array($this, 'handle_checkpoint_90_dias'), 10, 1);
        add_action('soda_perfeita_checkpoint_180_dias', array($this, 'handle_checkpoint_180_dias'), 10, 1);
        
        // Cron Jobs
        add_action('soda_perfeita_daily_automations', array($this, 'run_daily_automations'));
        add_action('init', array($this, 'schedule_cron_jobs'));
    }
    
    /**
     * =========================================================================
     * HANDLERS PARA EVENTOS PRINCIPAIS
     * =========================================================================
     */
    
    /**
     * Handler para cliente cadastrado
     */
    public function handle_cliente_cadastrado($cliente_id, $dados_cliente) {
        soda_perfeita_log_activity('cliente_cadastrado', "Cliente {$cliente_id} cadastrado no sistema", get_current_user_id());
        
        // Notificar Admin Preshh
        $this->execute_action('enviar_email_admin_preshh', array(
            'cliente_id' => $cliente_id,
            'assunto' => 'Novo Cliente Cadastrado - Requer Aprovação',
            'template' => 'cliente_cadastrado'
        ));
        
        // Criar tarefa de follow-up
        $this->execute_action('criar_tarefa_admin', array(
            'titulo' => 'Revisar cadastro do cliente ' . $dados_cliente['nome'],
            'descricao' => 'Novo cliente cadastrado aguardando aprovação',
            'prioridade' => 'media',
            'cliente_id' => $cliente_id
        ));
    }
    
    /**
     * Handler para cliente aprovado
     */
    public function handle_cliente_aprovado($cliente_id, $dados_cliente) {
        soda_perfeita_log_activity('cliente_aprovado', "Cliente {$cliente_id} aprovado pela Preshh", get_current_user_id());
        
        // Notificar distribuidor DVG
        $this->execute_action('enviar_email_distribuidor', array(
            'cliente_id' => $cliente_id,
            'assunto' => 'Novo Cliente Aprovado na Sua Região',
            'template' => 'cliente_aprovado'
        ));
        
        // Ativar acesso ao treinamento
        $this->activate_training_access($cliente_id);
    }
    
    /**
     * Handler para contrato assinado
     */
    public function handle_contrato_assinado($cliente_id, $contrato_id) {
        soda_perfeita_log_activity('contrato_assinado', "Contrato assinado para cliente {$cliente_id}", get_current_user_id());
        
        // Atualizar status do cliente
        $this->execute_action('atualizar_status_cliente', array(
            'cliente_id' => $cliente_id,
            'status' => 'contrato_assinado'
        ));
        
        // Notificar franqueado
        $this->execute_action('enviar_email_franqueado', array(
            'cliente_id' => $cliente_id,
            'assunto' => 'Contrato Assinado - Agendar Instalação',
            'template' => 'contrato_assinado'
        ));
    }
    
    /**
     * Handler para pagamento confirmado
     */
    public function handle_pagamento_confirmado($cliente_id, $dados_pagamento) {
        soda_perfeita_log_activity('pagamento_confirmado', "Pagamento confirmado para cliente {$cliente_id}", get_current_user_id());
        
        // Liberar pedidos do cliente
        $this->execute_action('liberar_pedidos_cliente', array(
            'cliente_id' => $cliente_id
        ));
        
        // Atualizar status para ativo
        $this->execute_action('atualizar_status_cliente', array(
            'cliente_id' => $cliente_id,
            'status' => 'ativo'
        ));
        
        // Agendar checkpoint de 30 dias
        $this->schedule_single_event(
            strtotime('+30 days'),
            'soda_perfeita_checkpoint_30_dias',
            array($cliente_id)
        );
    }
    
    /**
     * Handler para pedido solicitado
     */
    public function handle_pedido_solicitado($pedido_id, $dados_pedido) {
        $cliente_id = $dados_pedido['cliente_id'];
        
        soda_perfeita_log_activity('pedido_solicitado', "Pedido {$pedido_id} solicitado por cliente {$cliente_id}", get_current_user_id());
        
        // Verificar status financeiro automaticamente
        if (!soda_perfeita_cliente_adimplente($cliente_id)) {
            $this->execute_action('bloquear_pedidos_cliente', array(
                'cliente_id' => $cliente_id,
                'pedido_id' => $pedido_id
            ));
            return;
        }
        
        // Notificar Admin Preshh para aprovação
        $this->execute_action('enviar_email_admin_preshh', array(
            'cliente_id' => $cliente_id,
            'pedido_id' => $pedido_id,
            'assunto' => 'Pedido de Xarope Solicitado - Requer Aprovação',
            'template' => 'pedido_solicitado'
        ));
    }
    
    /**
     * Handler para pedido aprovado
     */
    public function handle_pedido_aprovado($pedido_id, $dados_pedido) {
        soda_perfeita_log_activity('pedido_aprovado', "Pedido {$pedido_id} aprovado pela Preshh", get_current_user_id());
        
        // Notificar distribuidor DVG
        $this->execute_action('notificar_distribuidor_pedido', array(
            'pedido_id' => $pedido_id,
            'distribuidor_id' => $dados_pedido['distribuidor_id']
        ));
        
        // Atualizar pontos de meritocracia
        $this->update_meritocracy_points($dados_pedido['cliente_id'], 'pedido_aprovado', 10);
    }
    
    /**
     * Handler para pedido faturado
     */
    public function handle_pedido_faturado($pedido_id, $dados_pedido) {
        soda_perfeita_log_activity('pedido_faturado', "Pedido {$pedido_id} faturado pelo distribuidor", get_current_user_id());
        
        // Atualizar dashboard e KPIs
        $this->execute_action('atualizar_dashboard_kpis', array(
            'pedido_id' => $pedido_id,
            'cliente_id' => $dados_pedido['cliente_id']
        ));
        
        // Adicionar pontos de meritocracia
        $this->update_meritocracy_points($dados_pedido['cliente_id'], 'pedido_faturado', 15);
        
        // Verificar e atualizar tier se necessário
        $this->check_and_update_tier($dados_pedido['cliente_id']);
    }
    
    /**
     * Handler para cliente inadimplente
     */
    public function handle_cliente_inadimplente($cliente_id, $dados_inadimplencia) {
        soda_perfeita_log_activity('cliente_inadimplente', "Cliente {$cliente_id} marcado como inadimplente", get_current_user_id());
        
        // Bloquear pedidos
        $this->execute_action('bloquear_pedidos_cliente', array(
            'cliente_id' => $cliente_id
        ));
        
        // Notificar cliente
        $this->execute_action('enviar_email_cliente', array(
            'cliente_id' => $cliente_id,
            'assunto' => 'Atenção: Pendência Financeira Detectada',
            'template' => 'cliente_inadimplente'
        ));
        
        // Notificar admin Preshh
        $this->execute_action('enviar_email_admin_preshh', array(
            'cliente_id' => $cliente_id,
            'assunto' => 'Cliente Inadimplente - Ação Requerida',
            'template' => 'cliente_inadimplente_admin'
        ));
    }
    
    /**
     * Handler para treinamento concluído
     */
    public function handle_treinamento_concluido($cliente_id, $curso_id) {
        soda_perfeita_log_activity('treinamento_concluido', "Cliente {$cliente_id} concluiu treinamento {$curso_id}", get_current_user_id());
        
        // Atualizar registro de treinamento
        update_field('treinamento_concluido', true, $cliente_id);
        update_field('data_conclusao_treinamento', current_time('mysql'), $cliente_id);
        
        // Adicionar pontos de meritocracia
        $this->update_meritocracy_points($cliente_id, 'treinamento_concluido', 25);
        
        // Notificar franqueado
        $this->execute_action('enviar_email_franqueado', array(
            'cliente_id' => $cliente_id,
            'assunto' => 'Cliente Concluiu Treinamento - Pronto para Ativação',
            'template' => 'treinamento_concluido'
        ));
    }
    
    /**
     * Handler para atualização de tier
     */
    public function handle_tier_atualizado($cliente_id, $novo_tier, $tier_anterior) {
        soda_perfeita_log_activity('tier_atualizado', "Cliente {$cliente_id} atualizado de {$tier_anterior} para {$novo_tier}", get_current_user_id());
        
        // Se atingiu Tier 3, disparar evento especial
        if ($novo_tier === 'tier_3') {
            do_action('soda_perfeita_tier3_atingido', $cliente_id, $novo_tier);
        }
        
        // Notificar cliente sobre novo tier
        $this->execute_action('enviar_email_cliente', array(
            'cliente_id' => $cliente_id,
            'assunto' => 'Parabéns! Seu Tier foi Atualizado',
            'template' => 'tier_atualizado',
            'dados_adicionais' => array(
                'novo_tier' => $novo_tier,
                'tier_anterior' => $tier_anterior
            )
        ));
        
        // Atualizar benefícios do tier
        $this->update_tier_benefits($cliente_id, $novo_tier);
    }
    
    /**
     * =========================================================================
     * HANDLERS PARA CHECKPOINTS
     * =========================================================================
     */
    
    public function handle_checkpoint_30_dias($cliente_id) {
        soda_perfeita_log_activity('checkpoint_30_dias', "Checkpoint 30 dias para cliente {$cliente_id}");
        
        // Verificar uso e consumo
        $media_pedidos = soda_perfeita_calcular_media_pedidos_90_dias($cliente_id);
        $treinamento_concluido = get_field('treinamento_concluido', $cliente_id);
        
        // Se performance baixa, notificar franqueado
        if ($media_pedidos < 4 || !$treinamento_concluido) {
            $this->execute_action('enviar_email_franqueado', array(
                'cliente_id' => $cliente_id,
                'assunto' => 'Atenção: Cliente com Baixa Performance em 30 Dias',
                'template' => 'checkpoint_30_dias_alerta'
            ));
        }
    }
    
    public function handle_checkpoint_90_dias($cliente_id) {
        soda_perfeita_log_activity('checkpoint_90_dias', "Checkpoint 90 dias para cliente {$cliente_id}");
        
        // Analisar volume e recorrência
        $this->check_and_update_tier($cliente_id);
        
        // Agendar próximo checkpoint
        $this->schedule_single_event(
            strtotime('+90 days'),
            'soda_perfeita_checkpoint_90_dias',
            array($cliente_id)
        );
    }
    
    public function handle_checkpoint_180_dias($cliente_id) {
        soda_perfeita_log_activity('checkpoint_180_dias', "Checkpoint 180 dias para cliente {$cliente_id}");
        
        // Avaliação completa DVG + Preshh
        $this->execute_action('enviar_email_admin_preshh', array(
            'cliente_id' => $cliente_id,
            'assunto' => 'Avaliação Semestral do Cliente - Requer Análise',
            'template' => 'checkpoint_180_dias'
        ));
        
        $this->execute_action('enviar_email_admin_dvg', array(
            'cliente_id' => $cliente_id,
            'assunto' => 'Avaliação Semestral do Cliente - Requer Análise',
            'template' => 'checkpoint_180_dias'
        ));
    }
    
    /**
     * =========================================================================
     * AÇÕES EXECUTÁVEIS
     * =========================================================================
     */
    
    public function send_email_admin_preshh($params) {
        $admin_email = soda_perfeita_get_option('email_admin_preshh', get_option('admin_email'));
        $assunto = $params['assunto'] ?? 'Notificação do Sistema Soda Perfeita';
        
        $message = $this->build_email_template($params['template'], $params);
        
        return soda_perfeita_send_email($admin_email, $assunto, $message);
    }
    
    public function send_email_admin_dvg($params) {
        $admin_email = soda_perfeita_get_option('email_admin_dvg', '');
        if (empty($admin_email)) return false;
        
        $assunto = $params['assunto'] ?? 'Notificação do Sistema Soda Perfeita';
        $message = $this->build_email_template($params['template'], $params);
        
        return soda_perfeita_send_email($admin_email, $assunto, $message);
    }
    
    public function send_email_franqueado($params) {
        $cliente_id = $params['cliente_id'];
        $franqueado_id = get_field('franqueado_responsavel', $cliente_id);
        
        if (!$franqueado_id) return false;
        
        $franqueado_email = get_field('email', $franqueado_id);
        $assunto = $params['assunto'] ?? 'Notificação do Sistema Soda Perfeita';
        $message = $this->build_email_template($params['template'], $params);
        
        return soda_perfeita_send_email($franqueado_email, $assunto, $message);
    }
    
    public function send_email_distribuidor($params) {
        $cliente_id = $params['cliente_id'];
        $distribuidor_id = get_field('distribuidor_responsavel', $cliente_id);
        
        if (!$distribuidor_id) return false;
        
        $distribuidor_email = get_field('email', $distribuidor_id);
        $assunto = $params['assunto'] ?? 'Notificação do Sistema Soda Perfeita';
        $message = $this->build_email_template($params['template'], $params);
        
        return soda_perfeita_send_email($distribuidor_email, $assunto, $message);
    }
    
    public function send_email_cliente($params) {
        $cliente_id = $params['cliente_id'];
        $cliente_email = get_field('email', $cliente_id);
        
        if (!$cliente_email) return false;
        
        $assunto = $params['assunto'] ?? 'Notificação do Sistema Soda Perfeita';
        $message = $this->build_email_template($params['template'], $params);
        
        return soda_perfeita_send_email($cliente_email, $assunto, $message);
    }
    
    public function update_cliente_status($params) {
        $cliente_id = $params['cliente_id'];
        $novo_status = $params['status'];
        
        return update_field('status', $novo_status, $cliente_id);
    }
    
    public function block_cliente_orders($params) {
        $cliente_id = $params['cliente_id'];
        
        update_field('pedidos_bloqueados', true, $cliente_id);
        update_field('motivo_bloqueio', 'inadimplencia', $cliente_id);
        update_field('data_bloqueio', current_time('mysql'), $cliente_id);
        
        return true;
    }
    
    public function release_cliente_orders($params) {
        $cliente_id = $params['cliente_id'];
        
        update_field('pedidos_bloqueados', false, $cliente_id);
        update_field('motivo_bloqueio', '', $cliente_id);
        update_field('data_liberacao', current_time('mysql'), $cliente_id);
        
        return true;
    }
    
    public function update_cliente_tier($params) {
        $cliente_id = $params['cliente_id'];
        $novo_tier = $params['novo_tier'];
        
        $tier_anterior = get_field('tier_atual', $cliente_id);
        
        update_field('tier_atual', $novo_tier, $cliente_id);
        update_field('data_atualizacao_tier', current_time('mysql'), $cliente_id);
        
        // Disparar evento de tier atualizado
        if ($tier_anterior !== $novo_tier) {
            do_action('soda_perfeita_tier_atualizado', $cliente_id, $novo_tier, $tier_anterior);
        }
        
        return true;
    }
    
    public function notify_distributor_order($params) {
        $pedido_id = $params['pedido_id'];
        $distribuidor_id = $params['distribuidor_id'];
        
        // Implementar notificação específica para distribuidor
        soda_perfeita_log_activity('distribuidor_notificado', "Distribuidor {$distribuidor_id} notificado sobre pedido {$pedido_id}");
        
        return true;
    }
    
    public function create_admin_task($params) {
        // Criar CPT de tarefas administrativas
        $task_id = wp_insert_post(array(
            'post_type' => 'sp_tarefas',
            'post_title' => $params['titulo'],
            'post_content' => $params['descricao'],
            'post_status' => 'publish',
            'post_author' => 1
        ));
        
        if ($task_id && !is_wp_error($task_id)) {
            update_field('prioridade', $params['prioridade'], $task_id);
            update_field('cliente_id', $params['cliente_id'], $task_id);
            update_field('status', 'pendente', $task_id);
            update_field('data_criacao', current_time('mysql'), $task_id);
        }
        
        return $task_id;
    }
    
    public function log_system_event($params) {
        return soda_perfeita_log_activity(
            $params['acao'],
            $params['detalhes'],
            $params['usuario_id'] ?? get_current_user_id()
        );
    }
    
    public function update_dashboard_kpis($params) {
        // Forçar atualização dos caches do dashboard
        delete_transient('soda_perfeita_dashboard_data');
        return true;
    }
    
    /**
     * =========================================================================
     * MÉTODOS AUXILIARES
     * =========================================================================
     */
    
    /**
     * Executa uma ação específica
     */
    private function execute_action($action_slug, $params = array()) {
        if (!isset($this->actions[$action_slug])) {
            return false;
        }
        
        $action = $this->actions[$action_slug];
        return call_user_func($action['callback'], $params);
    }
    
    /**
     * Constrói template de email
     */
    private function build_email_template($template, $data) {
        $templates = array(
            'cliente_cadastrado' => "
                <h2>Novo Cliente Cadastrado</h2>
                <p>Um novo cliente foi cadastrado no sistema e aguarda sua aprovação.</p>
                <p><strong>Cliente:</strong> " . get_the_title($data['cliente_id']) . "</p>
                <p><strong>Data:</strong> " . current_time('d/m/Y H:i') . "</p>
                <p><a href='" . admin_url('post.php?post=' . $data['cliente_id'] . '&action=edit') . "'>Ver Detalhes do Cliente</a></p>
            ",
            
            'cliente_inadimplente' => "
                <h2>Atenção: Pendência Financeira</h2>
                <p>Identificamos uma pendência financeira em sua conta.</p>
                <p>Para regularizar sua situação e continuar usufruindo dos benefícios do programa Soda Perfeita, entre em contato conosco.</p>
            ",
            
            'pedido_solicitado' => "
                <h2>Novo Pedido Solicitado</h2>
                <p>Um cliente solicitou um novo pedido de xarope.</p>
                <p><strong>Pedido ID:</strong> {$data['pedido_id']}</p>
                <p><strong>Cliente:</strong> " . get_the_title($data['cliente_id']) . "</p>
                <p><a href='" . admin_url('post.php?post=' . $data['pedido_id'] . '&action=edit') . "'>Aprovar ou Rejeitar Pedido</a></p>
            "
        );
        
        $template_content = $templates[$template] ?? "<p>Notificação do sistema Soda Perfeita.</p>";
        
        return "
            <!DOCTYPE html>
            <html>
            <head>
                <style>
                    body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                    .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                    .header { background: #2c3e50; color: white; padding: 20px; text-align: center; }
                    .content { background: #f9f9f9; padding: 20px; }
                    .footer { background: #34495e; color: white; padding: 10px; text-align: center; font-size: 12px; }
                </style>
            </head>
            <body>
                <div class='container'>
                    <div class='header'>
                        <h1>Soda Perfeita</h1>
                    </div>
                    <div class='content'>
                        {$template_content}
                    </div>
                    <div class='footer'>
                        <p>Este é um email automático do Sistema Soda Perfeita. Por favor, não responda.</p>
                    </div>
                </div>
            </body>
            </html>
        ";
    }
    
    /**
     * Agenda cron jobs
     */
    public function schedule_cron_jobs() {
        if (!wp_next_scheduled('soda_perfeita_daily_automations')) {
            wp_schedule_event(time(), 'daily', 'soda_perfeita_daily_automations');
        }
    }
    
    /**
     * Automações diárias
     */
    public function run_daily_automations() {
        // Verificar clientes inadimplentes
        $this->check_inadimplentes();
        
        // Atualizar tiers baseado na performance
        $this->update_tiers_automatically();
        
        // Verificar e agendar checkpoints
        $this->schedule_checkpoints();
        
        soda_perfeita_log_activity('daily_automations', 'Automações diárias executadas');
    }
    
    /**
     * Verifica clientes inadimplentes
     */
    private function check_inadimplentes() {
        $clientes_inadimplentes = get_posts(array(
            'post_type' => 'sp_clientes',
            'posts_per_page' => -1,
            'meta_query' => array(
                array(
                    'key' => 'status_financeiro',
                    'value' => 'inadimplente',
                    'compare' => '='
                )
            )
        ));
        
        foreach ($clientes_inadimplentes as $cliente) {
            // Verificar se já está bloqueado
            $ja_bloqueado = get_field('pedidos_bloqueados', $cliente->ID);
            
            if (!$ja_bloqueado) {
                do_action('soda_perfeita_cliente_inadimplente', $cliente->ID, array(
                    'cliente_nome' => $cliente->post_title
                ));
            }
        }
    }
    
    /**
     * Atualiza tiers automaticamente
     */
    private function update_tiers_automatically() {
        $clientes_ativos = get_posts(array(
            'post_type' => 'sp_clientes',
            'posts_per_page' => -1,
            'meta_query' => array(
                array(
                    'key' => 'status',
                    'value' => 'ativo',
                    'compare' => '='
                )
            )
        ));
        
        foreach ($clientes_ativos as $cliente) {
            $this->check_and_update_tier($cliente->ID);
        }
    }
    
    /**
     * Agenda checkpoints
     */
    private function schedule_checkpoints() {
        // Implementar lógica de agendamento de checkpoints
    }
    
    /**
     * Ativa acesso ao treinamento
     */
    private function activate_training_access($cliente_id) {
        $user_id = get_field('usuario_vinculado', $cliente_id);
        
        if ($user_id) {
            // Adicionar usuário ao curso no LearnDash
            ld_update_course_access($user_id, $this->get_training_course_id());
        }
    }
    
    /**
     * Atualiza pontos de meritocracia
     */
    private function update_meritocracy_points($cliente_id, $action, $points) {
        $pontos_atuais = get_field('pontos_meritocracia', $cliente_id) ?: 0;
        $novos_pontos = $pontos_atuais + $points;
        
        update_field('pontos_meritocracia', $novos_pontos, $cliente_id);
        
        // Registrar histórico de pontos
        $this->add_points_history($cliente_id, $action, $points);
    }
    
    /**
     * Adiciona histórico de pontos
     */
    private function add_points_history($cliente_id, $action, $points) {
        $historico = get_field('historico_pontos', $cliente_id) ?: array();
        
        $historico[] = array(
            'data' => current_time('mysql'),
            'acao' => $action,
            'pontos' => $points,
            'total' => get_field('pontos_meritocracia', $cliente_id)
        );
        
        update_field('historico_pontos', $historico, $cliente_id);
    }
    
    /**
     * Verifica e atualiza tier
     */
    private function check_and_update_tier($cliente_id) {
        $novo_tier = soda_perfeita_get_tier_cliente($cliente_id);
        $tier_atual = get_field('tier_atual', $cliente_id);
        
        if ($tier_atual !== $novo_tier) {
            $this->execute_action('atualizar_tier_cliente', array(
                'cliente_id' => $cliente_id,
                'novo_tier' => $novo_tier
            ));
        }
    }
    
    /**
     * Atualiza benefícios do tier
     */
    private function update_tier_benefits($cliente_id, $tier) {
        $beneficios = soda_perfeita_get_beneficios_tier($tier);
        update_field('beneficios_ativos', $beneficios, $cliente_id);
    }
    
    /**
     * Agenda evento único
     */
    private function schedule_single_event($timestamp, $hook, $args = array()) {
        if (!wp_next_scheduled($hook, $args)) {
            wp_schedule_single_event($timestamp, $hook, $args);
        }
    }
    
    /**
     * Retorna ID do curso de treinamento
     */
    private function get_training_course_id() {
        return soda_perfeita_get_option('curso_treinamento_id', 0);
    }
    
    /**
     * Retorna lista de triggers disponíveis
     */
    public function get_available_triggers() {
        return $this->triggers;
    }
    
    /**
     * Retorna lista de ações disponíveis
     */
    public function get_available_actions() {
        return $this->actions;
    }
}