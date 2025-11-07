<?php
/**
 * Dashboard Management for Soda Perfeita Plugin
 * 
 * @package SodaPerfeita
 * @since 1.0.0
 */

// Prevenir acesso direto
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Classe principal para gerenciamento do Dashboard
 */
class SodaPerfeita_Dashboard {
    
    private $kpi_data;
    private $chart_data;
    
    public function __construct() {
        add_action('wp_ajax_get_dashboard_data', array($this, 'get_dashboard_data_ajax'));
        add_action('wp_ajax_get_chart_data', array($this, 'get_chart_data_ajax'));
    }

    public function init() {
        // Método mantido para compatibilidade
    }
    
    /**
     * Renderiza o dashboard principal
     */
    public function render_dashboard($atts = array()) {
        $user_id = get_current_user_id();
        $user_role = soda_perfeita_get_current_user_role();
        
        // Verificar permissões
        if (!$this->user_can_access_dashboard($user_role)) {
            return $this->render_access_denied();
        }
        
        // Carregar dados específicos por role
        $dashboard_data = $this->get_dashboard_data($user_id, $user_role);
        
        ob_start();
        ?>
        <div class="soda-perfeita-dashboard" id="soda-perfeita-dashboard">
            <div class="dashboard-header">
                <h2><?php echo esc_html($this->get_dashboard_title($user_role)); ?></h2>
                <div class="dashboard-actions">
                    <button class="btn-refresh" onclick="sodaPerfeitaRefreshDashboard()">
                        <span class="dashicons dashicons-update"></span>
                        Atualizar
                    </button>
                    <select id="time-filter" onchange="sodaPerfeitaFilterDashboard(this.value)">
                        <option value="7d">Últimos 7 dias</option>
                        <option value="30d" selected>Últimos 30 dias</option>
                        <option value="90d">Últimos 90 dias</option>
                        <option value="1y">Último ano</option>
                    </select>
                </div>
            </div>
            
            <!-- KPIs Cards -->
            <div class="kpi-cards-grid">
                <?php $this->render_kpi_cards($dashboard_data['kpis']); ?>
            </div>
            
            <!-- Gráficos -->
            <div class="charts-grid">
                <div class="chart-container">
                    <div class="chart-header">
                        <h3>Volume de Pedidos</h3>
                    </div>
                    <div id="chart-pedidos-volume" class="chart-wrapper"></div>
                </div>
                
                <div class="chart-container">
                    <div class="chart-header">
                        <h3>Evolução de Clientes</h3>
                    </div>
                    <div id="chart-clientes-evolucao" class="chart-wrapper"></div>
                </div>
                
                <div class="chart-container">
                    <div class="chart-header">
                        <h3>Distribuição por Tier</h3>
                    </div>
                    <div id="chart-tiers-distribuicao" class="chart-wrapper"></div>
                </div>
                
                <div class="chart-container">
                    <div class="chart-header">
                        <h3>Performance Regional</h3>
                    </div>
                    <div id="chart-performance-regional" class="chart-wrapper"></div>
                </div>
            </div>
            
            <!-- Tabela de Dados Recentes -->
            <div class="recent-data-section">
                <?php $this->render_recent_data_table($dashboard_data['recent_data']); ?>
            </div>
        </div>
        
        <script>
        // Inicializar dashboard quando o DOM estiver pronto
        jQuery(document).ready(function() {
            sodaPerfeitaInitDashboard();
        });
        </script>
        <?php
        
        return ob_get_clean();
    }
    
    /**
     * Verifica se o usuário pode acessar o dashboard
     */
    private function user_can_access_dashboard($user_role) {
        $allowed_roles = array(
            'admin_preshh',
            'admin_dvg', 
            'franqueado_preshh',
            'distribuidor_dvg',
            'cliente_final'
        );
        
        return in_array($user_role, $allowed_roles);
    }
    
    /**
     * Retorna o título do dashboard baseado no role
     */
    private function get_dashboard_title($user_role) {
        $titles = array(
            'admin_preshh' => 'Dashboard Administrativo - Preshh',
            'admin_dvg' => 'Dashboard Comercial - DVG',
            'franqueado_preshh' => 'Meu Performance - Franqueado',
            'distribuidor_dvg' => 'Dashboard de Entregas - Distribuidor',
            'cliente_final' => 'Minha Performance - Cliente'
        );
        
        return $titles[$user_role] ?? 'Dashboard Soda Perfeita';
    }
    
    /**
     * Renderiza os cards de KPI
     */
    private function render_kpi_cards($kpis) {
        foreach ($kpis as $kpi) {
            ?>
            <div class="kpi-card <?php echo esc_attr($kpi['trend_class']); ?>">
                <div class="kpi-icon">
                    <span class="dashicons <?php echo esc_attr($kpi['icon']); ?>"></span>
                </div>
                <div class="kpi-content">
                    <div class="kpi-value"><?php echo esc_html($kpi['value']); ?></div>
                    <div class="kpi-label"><?php echo esc_html($kpi['label']); ?></div>
                    <div class="kpi-trend">
                        <span class="trend-indicator"></span>
                        <span class="trend-value"><?php echo esc_html($kpi['trend']); ?></span>
                    </div>
                </div>
            </div>
            <?php
        }
    }
    
    /**
     * Obtém dados do dashboard
     */
    public function get_dashboard_data($user_id = null, $user_role = null) {
        $user_id = $user_id ?: get_current_user_id();
        $user_role = $user_role ?: soda_perfeita_get_current_user_role();
        
        $data = array(
            'kpis' => $this->get_kpis_data($user_id, $user_role),
            'charts' => $this->get_charts_data($user_id, $user_role),
            'recent_data' => $this->get_recent_data($user_id, $user_role)
        );
        
        return apply_filters('soda_perfeita_dashboard_data', $data, $user_id, $user_role);
    }
    
    /**
     * Obtém dados para os KPIs
     */
    private function get_kpis_data($user_id, $user_role) {
        $kpis = array();
        
        switch ($user_role) {
            case 'admin_preshh':
                $kpis = $this->get_admin_preshh_kpis();
                break;
                
            case 'admin_dvg':
                $kpis = $this->get_admin_dvg_kpis();
                break;
                
            case 'franqueado_preshh':
                $kpis = $this->get_franqueado_kpis($user_id);
                break;
                
            case 'distribuidor_dvg':
                $kpis = $this->get_distribuidor_kpis($user_id);
                break;
                
            case 'cliente_final':
                $kpis = $this->get_cliente_kpis($user_id);
                break;
        }
        
        return $kpis;
    }
    
    /**
     * KPIs para Admin Preshh
     */
    private function get_admin_preshh_kpis() {
        return array(
            array(
                'icon' => 'dashicons-admin-users',
                'value' => $this->count_active_contracts(),
                'label' => 'Contratos Ativos',
                'trend' => '+5%',
                'trend_class' => 'trend-up'
            ),
            array(
                'icon' => 'dashicons-chart-line',
                'value' => $this->get_total_monthly_orders(),
                'label' => 'Pedidos/Mês',
                'trend' => '+12%',
                'trend_class' => 'trend-up'
            ),
            array(
                'icon' => 'dashicons-money',
                'value' => soda_perfeita_format_currency($this->calculate_average_ticket()),
                'label' => 'Ticket Médio',
                'trend' => '+3%',
                'trend_class' => 'trend-up'
            ),
            array(
                'icon' => 'dashicons-warning',
                'value' => $this->count_blocked_clients(),
                'label' => 'Clientes Bloqueados',
                'trend' => '-2%',
                'trend_class' => 'trend-down'
            )
        );
    }
    
    /**
     * KPIs para Admin DVG
     */
    private function get_admin_dvg_kpis() {
        return array(
            array(
                'icon' => 'dashicons-cart',
                'value' => $this->get_syrup_orders_volume(),
                'label' => 'Xaropes Vendidos',
                'trend' => '+8%',
                'trend_class' => 'trend-up'
            ),
            array(
                'icon' => 'dashicons-groups',
                'value' => $this->count_active_dvg_clients(),
                'label' => 'Clientes Ativos DVG',
                'trend' => '+15%',
                'trend_class' => 'trend-up'
            ),
            array(
                'icon' => 'dashicons-chart-pie',
                'value' => $this->calculate_market_penetration(),
                'label' => 'Penetração de Mercado',
                'trend' => '+4%',
                'trend_class' => 'trend-up'
            ),
            array(
                'icon' => 'dashicons-admin-multisite',
                'value' => $this->count_distributors_active(),
                'label' => 'Distribuidores Ativos',
                'trend' => '+2%',
                'trend_class' => 'trend-up'
            )
        );
    }
    
    /**
     * KPIs para Franqueado
     */
    private function get_franqueado_kpis($franqueado_id) {
        return array(
            array(
                'icon' => 'dashicons-portfolio',
                'value' => $this->count_franqueado_clients($franqueado_id),
                'label' => 'Meus Clientes',
                'trend' => '+3%',
                'trend_class' => 'trend-up'
            ),
            array(
                'icon' => 'dashicons-chart-line',
                'value' => $this->get_franqueado_monthly_volume($franqueado_id),
                'label' => 'Volume Mensal',
                'trend' => '+10%',
                'trend_class' => 'trend-up'
            ),
            array(
                'icon' => 'dashicons-awards',
                'value' => $this->get_franqueado_conversion_rate($franqueado_id),
                'label' => 'Taxa de Conversão',
                'trend' => '+5%',
                'trend_class' => 'trend-up'
            ),
            array(
                'icon' => 'dashicons-star-filled',
                'value' => $this->get_franqueado_tier_average($franqueado_id),
                'label' => 'Tier Médio',
                'trend' => 'Nova',
                'trend_class' => 'trend-neutral'
            )
        );
    }
    
    /**
     * Obtém dados para os gráficos
     */
    private function get_charts_data($user_id, $user_role) {
        $charts = array(
            'pedidos_volume' => $this->get_orders_volume_data($user_id, $user_role),
            'clientes_evolucao' => $this->get_clients_evolution_data($user_id, $user_role),
            'tiers_distribuicao' => $this->get_tiers_distribution_data($user_id, $user_role),
            'performance_regional' => $this->get_regional_performance_data($user_id, $user_role)
        );
        
        return $charts;
    }
    
    /**
     * Dados para gráfico de volume de pedidos
     */
    private function get_orders_volume_data($user_id, $user_role) {
        // Dados mockados - implementar com dados reais
        return array(
            'categories' => ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun'],
            'series' => array(
                array(
                    'name' => 'Xaropes Vendidos',
                    'data' => [30, 40, 35, 50, 49, 60]
                ),
                array(
                    'name' => 'Novos Clientes',
                    'data' => [15, 22, 18, 25, 30, 28]
                )
            )
        );
    }
    
    /**
     * Renderiza tabela de dados recentes
     */
    private function render_recent_data_table($recent_data) {
        if (empty($recent_data)) {
            echo '<p>Nenhum dado recente disponível.</p>';
            return;
        }
        ?>
        <div class="recent-data-table">
            <h3>Atividades Recentes</h3>
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th>Data</th>
                        <th>Cliente</th>
                        <th>Tipo</th>
                        <th>Valor</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recent_data as $item): ?>
                    <tr>
                        <td><?php echo esc_html($item['date']); ?></td>
                        <td><?php echo esc_html($item['client']); ?></td>
                        <td><?php echo esc_html($item['type']); ?></td>
                        <td><?php echo esc_html($item['value']); ?></td>
                        <td><span class="status-badge status-<?php echo esc_attr($item['status']); ?>">
                            <?php echo esc_html($item['status']); ?>
                        </span></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php
    }
    
    /**
     * Obtém dados recentes para a tabela
     */
    private function get_recent_data($user_id, $user_role) {
        // Implementar com dados reais do banco
        return array(
            array(
                'date' => '2024-01-15',
                'client' => 'Cafeteria Central',
                'type' => 'Pedido Xarope',
                'value' => '45 unidades',
                'status' => 'entregue'
            ),
            array(
                'date' => '2024-01-14',
                'client' => 'Bar do Zé',
                'type' => 'Nova Adesão',
                'value' => 'Tier 1',
                'status' => 'ativo'
            ),
            array(
                'date' => '2024-01-13',
                'client' => 'Restaurante Sabor',
                'type' => 'Upgrade Tier',
                'value' => 'Tier 2 → Tier 3',
                'status' => 'concluído'
            )
        );
    }
    
    /**
     * Handlers AJAX
     */
    public function get_dashboard_data_ajax() {
        check_ajax_referer('soda_perfeita_nonce', 'nonce');
        
        $user_id = get_current_user_id();
        $user_role = soda_perfeita_get_current_user_role();
        
        $data = $this->get_dashboard_data($user_id, $user_role);
        
        wp_send_json_success($data);
    }
    
    public function get_chart_data_ajax() {
        check_ajax_referer('soda_perfeita_nonce', 'nonce');
        
        $chart_type = sanitize_text_field($_POST['chart_type']);
        $time_range = sanitize_text_field($_POST['time_range']);
        $user_id = get_current_user_id();
        $user_role = soda_perfeita_get_current_user_role();
        
        $chart_data = $this->get_chart_data_by_type($chart_type, $time_range, $user_id, $user_role);
        
        wp_send_json_success($chart_data);
    }
    
    /**
     * Métodos auxiliares para cálculos de métricas
     */
    private function count_active_contracts() {
        $args = array(
            'post_type' => 'sp_clientes',
            'posts_per_page' => -1,
            'meta_query' => array(
                array(
                    'key' => 'status',
                    'value' => 'ativo',
                    'compare' => '='
                )
            )
        );
        
        $query = new WP_Query($args);
        return $query->found_posts;
    }
    
    private function get_total_monthly_orders() {
        // Implementar com dados reais
        return '1,234';
    }
    
    private function calculate_average_ticket() {
        // Implementar com dados reais
        return 2450.00;
    }
    
    private function count_blocked_clients() {
        $args = array(
            'post_type' => 'sp_clientes',
            'posts_per_page' => -1,
            'meta_query' => array(
                array(
                    'key' => 'status_financeiro',
                    'value' => 'inadimplente',
                    'compare' => '='
                )
            )
        );
        
        $query = new WP_Query($args);
        return $query->found_posts;
    }
    
    private function render_access_denied() {
        return '<div class="soda-perfeita-error"><p>Você não tem permissão para acessar este dashboard.</p></div>';
    }
    
    /**
     * Outros métodos auxiliares para cálculos de métricas
     * (Implementações simplificadas para exemplo)
     */
    private function get_syrup_orders_volume() {
        return '2,567';
    }
    
    private function count_active_dvg_clients() {
        return '189';
    }
    
    private function calculate_market_penetration() {
        return '34%';
    }
    
    private function count_distributors_active() {
        return '28';
    }
    
    private function count_franqueado_clients($franqueado_id) {
        return '15';
    }
    
    private function get_franqueado_monthly_volume($franqueado_id) {
        return '345';
    }
    
    private function get_franqueado_conversion_rate($franqueado_id) {
        return '42%';
    }
    
    private function get_franqueado_tier_average($franqueado_id) {
        return 'Tier 2.1';
    }
    
    private function get_chart_data_by_type($chart_type, $time_range, $user_id, $user_role) {
        // Implementar baseado no tipo de gráfico e período
        return array();
    }
}