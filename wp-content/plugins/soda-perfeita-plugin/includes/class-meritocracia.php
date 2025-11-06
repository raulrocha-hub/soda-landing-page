<?php
/**
 * Meritocracy Management for Soda Perfeita Plugin
 * Integração com GamiPress
 * 
 * @package SodaPerfeita
 * @since 1.0.0
 */

// Prevenir acesso direto
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Classe principal para gerenciamento do sistema de meritocracia
 */
class SodaPerfeita_Meritocracia {
    
    private $tiers_config;
    private $points_config;
    private $achievements_config;
    
    public function __construct() {
        $this->setup_tiers();
        $this->setup_points_system();
        $this->setup_achievements();
        $this->register_hooks();
        $this->register_gamipress_elements();
    }
    
    /**
     * Configura os tiers do sistema
     */
    private function setup_tiers() {
        $this->tiers_config = array(
            'tier_1' => array(
                'name' => 'Valor Base',
                'min_points' => 0,
                'min_orders_90d' => 4,
                'min_financial_score' => 80,
                'benefits' => array(
                    'garrafas_inclusas' => 4,
                    'material_promocional' => '1 banner + 20 displays',
                    'suporte' => 'básico',
                    'workshops' => 'webinars online',
                    'amostras' => 'sabores padrão'
                )
            ),
            'tier_2' => array(
                'name' => 'Performance',
                'min_points' => 500,
                'min_orders_90d' => 12,
                'min_financial_score' => 95,
                'benefits' => array(
                    'garrafas_inclusas' => 12,
                    'material_promocional' => '1 banner + 30 displays',
                    'suporte' => 'trade marketing regional',
                    'workshops' => 'presenciais regionais',
                    'amostras' => 'novos sabores'
                )
            ),
            'tier_3' => array(
                'name' => 'Excelência',
                'min_points' => 1500,
                'min_orders_90d' => 25,
                'min_financial_score' => 100,
                'benefits' => array(
                    'garrafas_inclusas' => 25,
                    'material_promocional' => 'personalizado',
                    'suporte' => 'dedicado premium',
                    'workshops' => 'VIP nacionais',
                    'amostras' => 'antecipada premium',
                    'subsidio' => 90.00
                )
            )
        );
    }
    
    /**
     * Configura o sistema de pontos
     */
    private function setup_points_system() {
        $this->points_config = array(
            'pedido_aprovado' => array(
                'points' => 10,
                'points_type' => 'soda_points',
                'trigger' => 'Pedido aprovado pela Preshh'
            ),
            'pedido_faturado' => array(
                'points' => 15,
                'points_type' => 'soda_points',
                'trigger' => 'Pedido faturado pelo distribuidor'
            ),
            'pedido_entregue' => array(
                'points' => 25,
                'points_type' => 'soda_points',
                'trigger' => 'Pedido entregue ao cliente'
            ),
            'treinamento_concluido' => array(
                'points' => 100,
                'points_type' => 'soda_points',
                'trigger' => 'Treinamento Soda Perfeita concluído'
            ),
            'cliente_30_dias' => array(
                'points' => 50,
                'points_type' => 'soda_points',
                'trigger' => 'Cliente ativo por 30 dias'
            ),
            'cliente_90_dias' => array(
                'points' => 150,
                'points_type' => 'soda_points',
                'trigger' => 'Cliente ativo por 90 dias'
            ),
            'pagamento_pontual' => array(
                'points' => 20,
                'points_type' => 'financial_points',
                'trigger' => 'Pagamento realizado em dia'
            ),
            'feedback_positivo' => array(
                'points' => 30,
                'points_type' => 'community_points',
                'trigger' => 'Feedback positivo recebido'
            )
        );
    }
    
    /**
     * Configura achievements e ranks
     */
    private function setup_achievements() {
        $this->achievements_config = array(
            'achievements' => array(
                'primeiro_pedido' => array(
                    'title' => 'Primeiro Pedido',
                    'description' => 'Realizou o primeiro pedido de xarope',
                    'points' => 50,
                    'trigger' => 'sp_first_order'
                ),
                'treinamento_mestre' => array(
                    'title' => 'Mestre do Soda',
                    'description' => 'Concluiu todos os treinamentos do programa',
                    'points' => 200,
                    'trigger' => 'sp_training_complete'
                ),
                'cliente_ouro' => array(
                    'title' => 'Cliente Ouro',
                    'description' => 'Manteve adimplência por 6 meses consecutivos',
                    'points' => 300,
                    'trigger' => 'sp_gold_client'
                ),
                'pedido_perfeito' => array(
                    'title' => 'Pedido Perfeito',
                    'description' => 'Realizou 10 pedidos consecutivos sem atrasos',
                    'points' => 150,
                    'trigger' => 'sp_perfect_orders'
                )
            ),
            'ranks' => array(
                'tier_1' => array(
                    'title' => 'Tier 1 - Valor Base',
                    'description' => 'Nível inicial do programa Soda Perfeita',
                    'points' => 0,
                    'requirement' => 'sp_tier_1_requirement'
                ),
                'tier_2' => array(
                    'title' => 'Tier 2 - Performance',
                    'description' => 'Cliente com performance consistente',
                    'points' => 500,
                    'requirement' => 'sp_tier_2_requirement'
                ),
                'tier_3' => array(
                    'title' => 'Tier 3 - Excelência',
                    'description' => 'Cliente de alta performance e excelência',
                    'points' => 1500,
                    'requirement' => 'sp_tier_3_requirement'
                )
            )
        );
    }
    
    /**
     * Registra os hooks do WordPress
     */
    private function register_hooks() {
        // Hooks para atualização de pontos
        add_action('soda_perfeita_pedido_aprovado', array($this, 'award_points_pedido_aprovado'), 10, 2);
        add_action('soda_perfeita_pedido_faturado', array($this, 'award_points_pedido_faturado'), 10, 2);
        add_action('soda_perfeita_pedido_entregue', array($this, 'award_points_pedido_entregue'), 10, 2);
        add_action('soda_perfeita_treinamento_concluido', array($this, 'award_points_treinamento_concluido'), 10, 2);
        add_action('soda_perfeita_pagamento_confirmado', array($this, 'award_points_pagamento_pontual'), 10, 2);
        
        // Hooks para verificação de tiers
        add_action('soda_perfeita_pedido_entregue', array($this, 'check_tier_evolution'), 20, 2);
        add_action('soda_perfeita_daily_maintenance', array($this, 'check_tiers_diarios'));
        
        // Shortcodes
        add_shortcode('soda_meritocracia_dashboard', array($this, 'render_meritocracia_dashboard'));
        add_shortcode('soda_tier_status', array($this, 'render_tier_status'));
        
        // AJAX handlers
        add_action('wp_ajax_get_meritocracia_data', array($this, 'get_meritocracia_data_ajax'));
    }
    
    /**
     * Registra elementos no GamiPress
     */
    private function register_gamipress_elements() {
        if (!function_exists('gamipress')) {
            return;
        }
        
        $this->register_points_types();
        $this->register_achievement_types();
        $this->register_rank_types();
    }
    
    /**
     * Registra tipos de pontos no GamiPress
     */
    private function register_points_types() {
        // Pontos principais do Soda Perfeita
        gamipress_add_points_type(array(
            'name' => 'soda_points',
            'singular_name' => 'Ponto Soda',
            'plural_name' => 'Pontos Soda',
            'before_amount' => '',
            'after_amount' => ' Pontos Soda',
            'position' => 'after'
        ));
        
        // Pontos financeiros (para adimplência)
        gamipress_add_points_type(array(
            'name' => 'financial_points',
            'singular_name' => 'Ponto Financeiro',
            'plural_name' => 'Pontos Financeiros',
            'before_amount' => '',
            'after_amount' => ' Pontos Financeiros',
            'position' => 'after'
        ));
        
        // Pontos de comunidade (feedback, etc)
        gamipress_add_points_type(array(
            'name' => 'community_points',
            'singular_name' => 'Ponto Comunidade',
            'plural_name' => 'Pontos Comunidade',
            'before_amount' => '',
            'after_amount' => ' Pontos Comunidade',
            'position' => 'after'
        ));
    }
    
    /**
     * Registra tipos de achievement no GamiPress
     */
    private function register_achievement_types() {
        gamipress_add_achievement_type(array(
            'name' => 'soda_achievements',
            'singular_name' => 'Conquista Soda',
            'plural_name' => 'Conquistas Soda',
            'show_in_menu' => true,
        ));
    }
    
    /**
     * Registra tipos de rank no GamiPress
     */
    private function register_rank_types() {
        gamipress_add_rank_type(array(
            'name' => 'soda_ranks',
            'singular_name' => 'Tier Soda',
            'plural_name' => 'Tiers Soda',
            'show_in_menu' => true,
        ));
    }
    
    /**
     * =========================================================================
     * SISTEMA DE PONTUAÇÃO
     * =========================================================================
     */
    
    /**
     * Atribui pontos por pedido aprovado
     */
    public function award_points_pedido_aprovado($pedido_id, $dados_pedido) {
        $cliente_id = $dados_pedido['cliente_id'];
        $user_id = $this->get_user_id_by_cliente($cliente_id);
        
        if ($user_id) {
            $this->award_points_to_user(
                $user_id,
                'pedido_aprovado',
                $this->points_config['pedido_aprovado']['points'],
                $this->points_config['pedido_aprovado']['points_type'],
                "Pedido #{$pedido_id} aprovado"
            );
        }
    }
    
    /**
     * Atribui pontos por pedido faturado
     */
    public function award_points_pedido_faturado($pedido_id, $dados_pedido) {
        $cliente_id = $dados_pedido['cliente_id'];
        $user_id = $this->get_user_id_by_cliente($cliente_id);
        
        if ($user_id) {
            $this->award_points_to_user(
                $user_id,
                'pedido_faturado',
                $this->points_config['pedido_faturado']['points'],
                $this->points_config['pedido_faturado']['points_type'],
                "Pedido #{$pedido_id} faturado"
            );
        }
    }
    
    /**
     * Atribui pontos por pedido entregue
     */
    public function award_points_pedido_entregue($pedido_id, $dados_pedido) {
        $cliente_id = $dados_pedido['cliente_id'];
        $user_id = $this->get_user_id_by_cliente($cliente_id);
        
        if ($user_id) {
            $points = $this->calculate_dynamic_points($dados_pedido);
            
            $this->award_points_to_user(
                $user_id,
                'pedido_entregue',
                $points,
                $this->points_config['pedido_entregue']['points_type'],
                "Pedido #{$pedido_id} entregue"
            );
            
            // Verificar achievements
            $this->check_order_achievements($user_id, $cliente_id);
        }
    }
    
    /**
     * Atribui pontos por treinamento concluído
     */
    public function award_points_treinamento_concluido($cliente_id, $curso_id) {
        $user_id = $this->get_user_id_by_cliente($cliente_id);
        
        if ($user_id) {
            $this->award_points_to_user(
                $user_id,
                'treinamento_concluido',
                $this->points_config['treinamento_concluido']['points'],
                $this->points_config['treinamento_concluido']['points_type'],
                "Treinamento concluído"
            );
            
            // Conquista de treinamento completo
            $this->award_achievement($user_id, 'treinamento_mestre');
        }
    }
    
    /**
     * Atribui pontos por pagamento pontual
     */
    public function award_points_pagamento_pontual($cliente_id, $dados_pagamento) {
        $user_id = $this->get_user_id_by_cliente($cliente_id);
        
        if ($user_id) {
            $this->award_points_to_user(
                $user_id,
                'pagamento_pontual',
                $this->points_config['pagamento_pontual']['points'],
                $this->points_config['pagamento_pontual']['points_type'],
                "Pagamento realizado em dia"
            );
        }
    }
    
    /**
     * Atribui pontos a um usuário
     */
    private function award_points_to_user($user_id, $event, $points, $points_type, $log_message = '') {
        if (!function_exists('gamipress')) {
            return false;
        }
        
        // Atribuir pontos via GamiPress
        gamipress_award_points_to_user($user_id, $points, $points_type, array(
            'reason' => $log_message,
            'log_type' => 'event_trigger'
        ));
        
        // Registrar no log do sistema
        soda_perfeita_log_activity(
            'points_awarded',
            "{$points} pontos do tipo '{$points_type}' atribuídos ao usuário {$user_id} para evento: {$event}",
            $user_id
        );
        
        return true;
    }
    
    /**
     * Calcula pontos dinâmicos baseados em critérios específicos
     */
    private function calculate_dynamic_points($dados_pedido) {
        $base_points = $this->points_config['pedido_entregue']['points'];
        $multiplier = 1.0;
        
        // Bônus por quantidade
        $quantidade = $dados_pedido['quantidade'] ?? 0;
        if ($quantidade > 20) {
            $multiplier += 0.5;
        } elseif ($quantidade > 10) {
            $multiplier += 0.25;
        }
        
        // Bônus por pontualidade (se aplicável)
        if (isset($dados_pedido['entregue_no_prazo']) && $dados_pedido['entregue_no_prazo']) {
            $multiplier += 0.2;
        }
        
        return intval($base_points * $multiplier);
    }
    
    /**
     * =========================================================================
     * SISTEMA DE TIERS
     * =========================================================================
     */
    
    /**
     * Calcula o tier atual de um cliente
     */
    public function calcular_tier_cliente($cliente_id) {
        $pontuacao_total = $this->get_total_points_by_cliente($cliente_id);
        $media_pedidos = soda_perfeita_calcular_media_pedidos_90_dias($cliente_id);
        $score_financeiro = $this->calcular_score_financeiro($cliente_id);
        $treinamento_concluido = get_field('treinamento_concluido', $cliente_id);
        
        // Verificar requisitos mínimos
        if (!$treinamento_concluido || $score_financeiro < 50) {
            return 'tier_0'; // Não elegível
        }
        
        // Verificar Tier 3
        if ($pontuacao_total >= $this->tiers_config['tier_3']['min_points'] &&
            $media_pedidos >= $this->tiers_config['tier_3']['min_orders_90d'] &&
            $score_financeiro >= $this->tiers_config['tier_3']['min_financial_score']) {
            return 'tier_3';
        }
        
        // Verificar Tier 2
        if ($pontuacao_total >= $this->tiers_config['tier_2']['min_points'] &&
            $media_pedidos >= $this->tiers_config['tier_2']['min_orders_90d'] &&
            $score_financeiro >= $this->tiers_config['tier_2']['min_financial_score']) {
            return 'tier_2';
        }
        
        // Tier 1 (base)
        return 'tier_1';
    }
    
    /**
     * Verifica e atualiza tier do cliente
     */
    public function check_tier_evolution($pedido_id = null, $dados_pedido = null) {
        $cliente_id = $dados_pedido['cliente_id'] ?? null;
        
        if (!$cliente_id) {
            return;
        }
        
        $tier_atual = get_field('tier_atual', $cliente_id);
        $novo_tier = $this->calcular_tier_cliente($cliente_id);
        
        if ($tier_atual !== $novo_tier) {
            $this->atualizar_tier_cliente($cliente_id, $novo_tier, $tier_atual);
        }
    }
    
    /**
     * Atualiza o tier do cliente e dispara ações
     */
    public function atualizar_tier_cliente($cliente_id, $novo_tier, $tier_anterior = null) {
        $user_id = $this->get_user_id_by_cliente($cliente_id);
        
        // Atualizar campo ACF
        update_field('tier_atual', $novo_tier, $cliente_id);
        update_field('data_atualizacao_tier', current_time('mysql'), $cliente_id);
        
        // Atualizar rank no GamiPress
        if ($user_id && function_exists('gamipress')) {
            $this->update_gamipress_rank($user_id, $novo_tier);
        }
        
        // Aplicar benefícios do novo tier
        $this->aplicar_beneficios_tier($cliente_id, $novo_tier);
        
        // Disparar evento de tier atualizado
        do_action('soda_perfeita_tier_atualizado', $cliente_id, $novo_tier, $tier_anterior);
        
        // Log da atividade
        soda_perfeita_log_activity(
            'tier_updated',
            "Cliente {$cliente_id} atualizado de {$tier_anterior} para {$novo_tier}",
            $user_id
        );
        
        // Se subiu para Tier 3, disparar evento especial
        if ($novo_tier === 'tier_3') {
            do_action('soda_perfeita_tier3_atingido', $cliente_id, $novo_tier);
        }
    }
    
    /**
     * Verificação diária de tiers
     */
    public function check_tiers_diarios() {
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
            $this->check_tier_evolution(null, array('cliente_id' => $cliente->ID));
        }
    }
    
    /**
     * Aplica benefícios do tier
     */
    private function aplicar_beneficios_tier($cliente_id, $tier) {
        $beneficios = $this->tiers_config[$tier]['benefits'];
        
        update_field('beneficios_ativos', $beneficios, $cliente_id);
        update_field('data_aplicacao_beneficios', current_time('mysql'), $cliente_id);
        
        // Disparar ação específica para aplicação de benefícios
        do_action('soda_perfeita_beneficios_aplicados', $cliente_id, $tier, $beneficios);
    }
    
    /**
     * =========================================================================
     * SISTEMA DE CONQUISTAS
     * =========================================================================
     */
    
    /**
     * Verifica conquistas relacionadas a pedidos
     */
    private function check_order_achievements($user_id, $cliente_id) {
        $total_pedidos = $this->count_pedidos_entregues($cliente_id);
        
        // Primeiro pedido
        if ($total_pedidos === 1) {
            $this->award_achievement($user_id, 'primeiro_pedido');
        }
        
        // Pedidos consecutivos (verificar lógica específica)
        $pedidos_consecutivos = $this->check_pedidos_consecutivos($cliente_id);
        if ($pedidos_consecutivos >= 10) {
            $this->award_achievement($user_id, 'pedido_perfeito');
        }
    }
    
    /**
     * Atribui uma conquista ao usuário
     */
    private function award_achievement($user_id, $achievement_slug) {
        if (!function_exists('gamipress')) {
            return false;
        }
        
        $achievement_config = $this->achievements_config['achievements'][$achievement_slug];
        
        // Verificar se o usuário já tem a conquista
        if (!gamipress_get_user_achievements(array(
            'user_id' => $user_id,
            'achievement_type' => 'soda_achievements',
            'achievement_post' => $achievement_slug
        ))) {
            
            // Atribuir conquista
            gamipress_award_achievement_to_user($achievement_slug, $user_id);
            
            soda_perfeita_log_activity(
                'achievement_awarded',
                "Conquista '{$achievement_config['title']}' atribuída ao usuário {$user_id}",
                $user_id
            );
            
            return true;
        }
        
        return false;
    }
    
    /**
     * =========================================================================
     * INTEGRAÇÃO COM GAMIPRESS
     * =========================================================================
     */
    
    /**
     * Atualiza rank no GamiPress
     */
    private function update_gamipress_rank($user_id, $tier) {
        if (!function_exists('gamipress_update_user_rank')) {
            return false;
        }
        
        // Mapear tier para rank ID do GamiPress
        $rank_id = $this->get_gamipress_rank_id($tier);
        
        if ($rank_id) {
            gamipress_update_user_rank($user_id, $rank_id);
            return true;
        }
        
        return false;
    }
    
    /**
     * Obtém ID do rank no GamiPress
     */
    private function get_gamipress_rank_id($tier) {
        // Esta função precisa ser implementada baseada na configuração do GamiPress
        // Retorna o post_id do rank correspondente ao tier
        $rank = get_posts(array(
            'post_type' => 'soda_ranks',
            'name' => $tier,
            'posts_per_page' => 1
        ));
        
        return $rank ? $rank[0]->ID : false;
    }
    
    /**
     * =========================================================================
     * MÉTODOS AUXILIARES
     * =========================================================================
     */
    
    /**
     * Obtém usuário vinculado ao cliente
     */
    private function get_user_id_by_cliente($cliente_id) {
        return get_field('usuario_vinculado', $cliente_id);
    }
    
    /**
     * Calcula score financeiro do cliente (0-100)
     */
    private function calcular_score_financeiro($cliente_id) {
        $status_financeiro = get_field('status_financeiro', $cliente_id);
        $historico_pagamentos = get_field('historico_pagamentos', $cliente_id);
        
        $score = 100; // Base
        
        // Penalidade por inadimplência atual
        if ($status_financeiro === 'inadimplente') {
            $score -= 50;
        }
        
        // Bônus por histórico (implementar lógica mais sofisticada)
        if (is_array($historico_pagamentos)) {
            $pagamentos_pontuais = array_filter($historico_pagamentos, function($pagamento) {
                return $pagamento['pontual'] === true;
            });
            
            $percentual_pontual = count($pagamentos_pontuais) / max(1, count($historico_pagamentos));
            $score += ($percentual_pontual * 20); // Bônus de até 20 pontos
        }
        
        return max(0, min(100, $score));
    }
    
    /**
     * Obtém total de pontos do cliente
     */
    private function get_total_points_by_cliente($cliente_id) {
        $user_id = $this->get_user_id_by_cliente($cliente_id);
        
        if (!$user_id || !function_exists('gamipress_get_user_points')) {
            return 0;
        }
        
        $points = gamipress_get_user_points($user_id, 'soda_points');
        return intval($points);
    }
    
    /**
     * Conta pedidos entregues do cliente
     */
    private function count_pedidos_entregues($cliente_id) {
        $args = array(
            'post_type' => 'sp_pedidos',
            'posts_per_page' => -1,
            'meta_query' => array(
                array(
                    'key' => 'cliente_id',
                    'value' => $cliente_id,
                ),
                array(
                    'key' => 'status',
                    'value' => 'entregue',
                )
            )
        );
        
        $pedidos = get_posts($args);
        return count($pedidos);
    }
    
    /**
     * Verifica pedidos consecutivos sem atraso
     */
    private function check_pedidos_consecutivos($cliente_id) {
        // Implementar lógica para verificar sequência de pedidos sem atrasos
        // Retorna o número de pedidos consecutivos no prazo
        return 0; // Placeholder
    }
    
    /**
     * =========================================================================
     * RENDERIZAÇÃO FRONT-END
     * =========================================================================
     */
    
    /**
     * Renderiza dashboard de meritocracia
     */
    public function render_meritocracia_dashboard($atts = array()) {
        $user_id = get_current_user_id();
        $cliente_id = $this->get_cliente_id_by_user($user_id);
        
        if (!$cliente_id) {
            return '<p>Você precisa estar vinculado a um cliente para acessar a meritocracia.</p>';
        }
        
        $tier_atual = get_field('tier_atual', $cliente_id);
        $pontuacao_total = $this->get_total_points_by_cliente($cliente_id);
        $proximo_tier = $this->get_proximo_tier($tier_atual);
        
        ob_start();
        ?>
        <div class="soda-meritocracia-dashboard">
            <div class="meritocracia-header">
                <h2>Minha Meritocracia</h2>
                <p>Acompanhe seu progresso no programa Soda Perfeita</p>
            </div>
            
            <!-- Status do Tier Atual -->
            <div class="tier-status-card">
                <div class="tier-badge tier-<?php echo esc_attr($tier_atual); ?>">
                    <span class="tier-name"><?php echo esc_html($this->tiers_config[$tier_atual]['name']); ?></span>
                    <span class="tier-level"><?php echo str_replace('tier_', 'Tier ', $tier_atual); ?></span>
                </div>
                
                <div class="tier-progress">
                    <div class="progress-info">
                        <span class="current-points"><?php echo esc_html($pontuacao_total); ?> pontos</span>
                        <?php if ($proximo_tier): ?>
                        <span class="next-tier-points"><?php echo esc_html($proximo_tier['min_points']); ?> pontos para o próximo tier</span>
                        <?php endif; ?>
                    </div>
                    <?php if ($proximo_tier): ?>
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: <?php echo $this->calculate_progress_percentage($pontuacao_total, $proximo_tier['min_points']); ?>%"></div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Benefícios Ativos -->
            <div class="benefits-section">
                <h3>Benefícios Ativos</h3>
                <div class="benefits-grid">
                    <?php foreach ($this->tiers_config[$tier_atual]['benefits'] as $beneficio => $valor): ?>
                    <div class="benefit-card">
                        <span class="benefit-icon">✓</span>
                        <div class="benefit-content">
                            <strong><?php echo esc_html($this->format_benefit_name($beneficio)); ?></strong>
                            <span><?php echo esc_html($valor); ?></span>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <!-- Próximos Tiers -->
            <div class="next-tiers-section">
                <h3>Próximos Níveis</h3>
                <div class="tiers-timeline">
                    <?php foreach ($this->tiers_config as $tier_key => $tier_config): ?>
                        <?php if ($tier_key !== $tier_atual): ?>
                        <div class="tier-future <?php echo $this->is_tier_accessible($tier_key, $cliente_id) ? 'accessible' : 'locked'; ?>">
                            <div class="tier-indicator"></div>
                            <div class="tier-info">
                                <h4><?php echo esc_html($tier_config['name']); ?></h4>
                                <p><?php echo esc_html($tier_config['min_points']); ?> pontos mínimos</p>
                                <p><?php echo esc_html($tier_config['min_orders_90d']); ?> pedidos/mês (média 90 dias)</p>
                            </div>
                        </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <!-- Conquistas -->
            <?php if (function_exists('gamipress')): ?>
            <div class="achievements-section">
                <h3>Minhas Conquistas</h3>
                <div class="achievements-grid">
                    <?php
                    $conquistas = gamipress_get_user_achievements(array(
                        'user_id' => $user_id,
                        'achievement_type' => 'soda_achievements'
                    ));
                    
                    foreach ($conquistas as $conquista): ?>
                    <div class="achievement-card">
                        <div class="achievement-icon">🏆</div>
                        <div class="achievement-info">
                            <strong><?php echo esc_html($conquista->post_title); ?></strong>
                            <p><?php echo esc_html($conquista->post_excerpt); ?></p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
        
        <style>
        .soda-meritocracia-dashboard {
            max-width: 800px;
            margin: 0 auto;
        }
        .tier-status-card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .tier-badge {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 10px 20px;
            border-radius: 25px;
            color: white;
            font-weight: bold;
        }
        .tier-badge.tier_1 { background: #95a5a6; }
        .tier-badge.tier_2 { background: #3498db; }
        .tier-badge.tier_3 { background: #f39c12; }
        .progress-bar {
            background: #ecf0f1;
            border-radius: 10px;
            height: 10px;
            margin: 10px 0;
        }
        .progress-fill {
            background: #2ecc71;
            height: 100%;
            border-radius: 10px;
            transition: width 0.3s ease;
        }
        .benefits-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 15px;
            margin-top: 15px;
        }
        .benefit-card {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
            border-left: 4px solid #2ecc71;
        }
        </style>
        <?php
        return ob_get_clean();
    }
    
    /**
     * Renderiza status do tier
     */
    public function render_tier_status($atts = array()) {
        $user_id = get_current_user_id();
        $cliente_id = $this->get_cliente_id_by_user($user_id);
        
        if (!$cliente_id) {
            return '';
        }
        
        $tier_atual = get_field('tier_atual', $cliente_id);
        $tier_config = $this->tiers_config[$tier_atual];
        
        ob_start();
        ?>
        <div class="soda-tier-status-widget">
            <div class="tier-badge-small tier-<?php echo esc_attr($tier_atual); ?>">
                <span class="tier-name"><?php echo esc_html($tier_config['name']); ?></span>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
    
    /**
     * =========================================================================
     * MÉTODOS AUXILIARES DE RENDERIZAÇÃO
     * =========================================================================
     */
    
    /**
     * Obtém ID do cliente pelo usuário
     */
    private function get_cliente_id_by_user($user_id) {
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
     * Obtém próximo tier
     */
    private function get_proximo_tier($tier_atual) {
        $tiers = array_keys($this->tiers_config);
        $current_index = array_search($tier_atual, $tiers);
        
        if ($current_index !== false && isset($tiers[$current_index + 1])) {
            $next_tier = $tiers[$current_index + 1];
            return $this->tiers_config[$next_tier];
        }
        
        return null;
    }
    
    /**
     * Calcula porcentagem de progresso
     */
    private function calculate_progress_percentage($current_points, $next_tier_points) {
        if ($next_tier_points <= 0) return 0;
        return min(100, ($current_points / $next_tier_points) * 100);
    }
    
    /**
     * Formata nome do benefício
     */
    private function format_benefit_name($benefit_key) {
        $names = array(
            'garrafas_inclusas' => 'Garrafas Inclusas',
            'material_promocional' => 'Material Promocional',
            'suporte' => 'Suporte',
            'workshops' => 'Workshops',
            'amostras' => 'Amostras',
            'subsidio' => 'Subsídio Mensal'
        );
        
        return $names[$benefit_key] ?? $benefit_key;
    }
    
    /**
     * Verifica se tier é acessível
     */
    private function is_tier_accessible($tier_key, $cliente_id) {
        $pontuacao_total = $this->get_total_points_by_cliente($cliente_id);
        $media_pedidos = soda_perfeita_calcular_media_pedidos_90_dias($cliente_id);
        $score_financeiro = $this->calcular_score_financeiro($cliente_id);
        
        $tier_config = $this->tiers_config[$tier_key];
        
        return $pontuacao_total >= $tier_config['min_points'] &&
               $media_pedidos >= $tier_config['min_orders_90d'] &&
               $score_financeiro >= $tier_config['min_financial_score'];
    }
    
    /**
     * Handler AJAX para dados de meritocracia
     */
    public function get_meritocracia_data_ajax() {
        check_ajax_referer('soda_perfeita_nonce', 'nonce');
        
        $user_id = get_current_user_id();
        $cliente_id = $this->get_cliente_id_by_user($user_id);
        
        if (!$cliente_id) {
            wp_send_json_error('Cliente não encontrado.');
        }
        
        $data = array(
            'tier_atual' => get_field('tier_atual', $cliente_id),
            'pontuacao_total' => $this->get_total_points_by_cliente($cliente_id),
            'media_pedidos' => soda_perfeita_calcular_media_pedidos_90_dias($cliente_id),
            'score_financeiro' => $this->calcular_score_financeiro($cliente_id),
            'beneficios' => get_field('beneficios_ativos', $cliente_id),
            'proximo_tier' => $this->get_proximo_tier(get_field('tier_atual', $cliente_id))
        );
        
        wp_send_json_success($data);
    }
    
    /**
     * Retorna configuração dos tiers
     */
    public function get_tiers_config() {
        return $this->tiers_config;
    }
    
    /**
     * Retorna configuração de pontos
     */
    public function get_points_config() {
        return $this->points_config;
    }
}