<?php
/**
 * Admin Panel Template
 * 
 * @package SodaPerfeita
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap soda-admin-panel">
    <h1 class="wp-heading-inline">
        <span class="dashicons dashicons-admin-settings"></span>
        Soda Perfeita - Painel Administrativo
    </h1>

    <div class="soda-admin-header">
        <div class="header-stats">
            <div class="stat-card">
                <div class="stat-icon clients">
                    <span class="dashicons dashicons-groups"></span>
                </div>
                <div class="stat-info">
                    <span class="stat-number"><?php echo esc_html($total_clientes); ?></span>
                    <span class="stat-label">Clientes Ativos</span>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon orders">
                    <span class="dashicons dashicons-cart"></span>
                </div>
                <div class="stat-info">
                    <span class="stat-number"><?php echo esc_html($pedidos_mes); ?></span>
                    <span class="stat-label">Pedidos este Mês</span>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon revenue">
                    <span class="dashicons dashicons-money"></span>
                </div>
                <div class="stat-info">
                    <span class="stat-number">R$ <?php echo number_format($receita_mes, 0, ',', '.'); ?></span>
                    <span class="stat-label">Receita Mensal</span>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon pending">
                    <span class="dashicons dashicons-warning"></span>
                </div>
                <div class="stat-info">
                    <span class="stat-number"><?php echo esc_html($pendentes); ?></span>
                    <span class="stat-label">Aprovações Pendentes</span>
                </div>
            </div>
        </div>
    </div>

    <div class="soda-admin-content">
        <div class="admin-columns">
            <!-- Coluna Principal -->
            <div class="main-column">
                <!-- Ações Rápidas -->
                <div class="admin-card quick-actions">
                    <h3>Ações Rápidas</h3>
                    <div class="actions-grid">
                        <a href="<?php echo esc_url($url_clientes); ?>" class="action-card">
                            <span class="dashicons dashicons-admin-users"></span>
                            <span>Gerenciar Clientes</span>
                        </a>
                        <a href="<?php echo esc_url($url_pedidos); ?>" class="action-card">
                            <span class="dashicons dashicons-cart"></span>
                            <span>Ver Pedidos</span>
                        </a>
                        <a href="<?php echo esc_url($url_relatorios); ?>" class="action-card">
                            <span class="dashicons dashicons-chart-bar"></span>
                            <span>Relatórios</span>
                        </a>
                        <a href="<?php echo esc_url($url_config); ?>" class="action-card">
                            <span class="dashicons dashicons-admin-generic"></span>
                            <span>Configurações</span>
                        </a>
                    </div>
                </div>

                <!-- Pedidos Recentes -->
                <div class="admin-card recent-orders">
                    <div class="card-header">
                        <h3>Pedidos Recentes</h3>
                        <a href="<?php echo esc_url($url_pedidos); ?>" class="view-all">Ver Todos</a>
                    </div>
                    <div class="card-body">
                        <table class="wp-list-table widefat fixed striped">
                            <thead>
                                <tr>
                                    <th>Pedido</th>
                                    <th>Cliente</th>
                                    <th>Data</th>
                                    <th>Valor</th>
                                    <th>Status</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($pedidos_recentes as $pedido): ?>
                                <tr>
                                    <td>#<?php echo esc_html($pedido['id']); ?></td>
                                    <td><?php echo esc_html($pedido['cliente']); ?></td>
                                    <td><?php echo esc_html($pedido['data']); ?></td>
                                    <td>R$ <?php echo number_format($pedido['valor'], 2, ',', '.'); ?></td>
                                    <td>
                                        <span class="status-badge status-<?php echo esc_attr($pedido['status']); ?>">
                                            <?php echo esc_html($pedido['status_label']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="row-actions">
                                            <a href="<?php echo esc_url($pedido['link_editar']); ?>">Editar</a>
                                            <?php if ($pedido['pode_aprovar']): ?>
                                            | <a href="#" onclick="sodaAprovarPedido(<?php echo $pedido['id']; ?>)">Aprovar</a>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Clientes para Aprovação -->
                <?php if (!empty($clientes_pendentes)): ?>
                <div class="admin-card pending-clients">
                    <div class="card-header">
                        <h3>Clientes Aguardando Aprovação</h3>
                    </div>
                    <div class="card-body">
                        <table class="wp-list-table widefat fixed striped">
                            <thead>
                                <tr>
                                    <th>Cliente</th>
                                    <th>Data Cadastro</th>
                                    <th>Segmento</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($clientes_pendentes as $cliente): ?>
                                <tr>
                                    <td><?php echo esc_html($cliente['nome']); ?></td>
                                    <td><?php echo esc_html($cliente['data_cadastro']); ?></td>
                                    <td><?php echo esc_html($cliente['segmento']); ?></td>
                                    <td>
                                        <div class="row-actions">
                                            <a href="<?php echo esc_url($cliente['link_editar']); ?>">Ver Detalhes</a>
                                            | <a href="#" onclick="sodaAprovarCliente(<?php echo $cliente['id']; ?>)">Aprovar</a>
                                            | <a href="#" onclick="sodaRejeitarCliente(<?php echo $cliente['id']; ?>)" class="delete">Rejeitar</a>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <!-- Sidebar -->
            <div class="sidebar-column">
                <!-- Alertas do Sistema -->
                <div class="admin-card system-alerts">
                    <h3>Alertas do Sistema</h3>
                    <div class="alerts-list">
                        <?php foreach ($alertas as $alerta): ?>
                        <div class="alert-item alert-<?php echo esc_attr($alerta['tipo']); ?>">
                            <span class="dashicons dashicons-<?php echo esc_attr($alerta['icone']); ?>"></span>
                            <div class="alert-content">
                                <strong><?php echo esc_html($alerta['titulo']); ?></strong>
                                <p><?php echo esc_html($alerta['mensagem']); ?></p>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Estatísticas de Tiers -->
                <div class="admin-card tiers-stats">
                    <h3>Distribuição por Tier</h3>
                    <div class="tiers-list">
                        <?php foreach ($distribuicao_tiers as $tier): ?>
                        <div class="tier-item">
                            <span class="tier-name"><?php echo esc_html($tier['nome']); ?></span>
                            <span class="tier-count"><?php echo esc_html($tier['quantidade']); ?></span>
                            <div class="tier-bar">
                                <div class="tier-fill" style="width: <?php echo esc_attr($tier['percentual']); ?>%"></div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Atividade Recente -->
                <div class="admin-card recent-activity">
                    <h3>Atividade Recente</h3>
                    <div class="activity-list">
                        <?php foreach ($atividades as $atividade): ?>
                        <div class="activity-item">
                            <div class="activity-icon">
                                <span class="dashicons dashicons-<?php echo esc_attr($atividade['icone']); ?>"></span>
                            </div>
                            <div class="activity-content">
                                <p><?php echo esc_html($atividade['mensagem']); ?></p>
                                <small><?php echo esc_html($atividade['tempo']); ?></small>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function sodaAprovarPedido(pedidoId) {
    if (confirm('Tem certeza que deseja aprovar este pedido?')) {
        // Implementar AJAX para aprovar pedido
        console.log('Aprovar pedido:', pedidoId);
    }
}

function sodaAprovarCliente(clienteId) {
    if (confirm('Tem certeza que deseja aprovar este cliente?')) {
        // Implementar AJAX para aprovar cliente
        console.log('Aprovar cliente:', clienteId);
    }
}

function sodaRejeitarCliente(clienteId) {
    if (confirm('Tem certeza que deseja rejeitar este cliente?')) {
        // Implementar AJAX para rejeitar cliente
        console.log('Rejeitar cliente:', clienteId);
    }
}
</script>

<style>
.soda-admin-panel {
    max-width: 1400px;
}

.soda-admin-header {
    margin: 20px 0 30px 0;
}

.header-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.stat-card {
    background: white;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    display: flex;
    align-items: center;
    gap: 15px;
}

.stat-icon {
    width: 60px;
    height: 60px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    color: white;
}

.stat-icon.clients { background: #3498db; }
.stat-icon.orders { background: #2ecc71; }
.stat-icon.revenue { background: #f39c12; }
.stat-icon.pending { background: #e74c3c; }

.stat-number {
    display: block;
    font-size: 24px;
    font-weight: bold;
    color: #2c3e50;
}

.stat-label {
    font-size: 14px;
    color: #7f8c8d;
}

.admin-columns {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 30px;
}

@media (max-width: 1200px) {
    .admin-columns {
        grid-template-columns: 1fr;
    }
}

.admin-card {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    margin-bottom: 30px;
    overflow: hidden;
}

.admin-card .card-header {
    background: #f8f9fa;
    padding: 15px 20px;
    border-bottom: 1px solid #e9ecef;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.admin-card .card-header h3 {
    margin: 0;
    font-size: 16px;
    color: #2c3e50;
}

.admin-card .card-body {
    padding: 20px;
}

.actions-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 15px;
}

.action-card {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 10px;
    padding: 20px;
    text-align: center;
    text-decoration: none;
    color: #2c3e50;
    border: 1px solid #e0e0e0;
    border-radius: 6px;
    transition: all 0.3s ease;
}

.action-card:hover {
    background: #f8f9fa;
    border-color: #3498db;
    transform: translateY(-2px);
}

.action-card .dashicons {
    font-size: 32px;
    width: 32px;
    height: 32px;
}

.status-badge {
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: bold;
}

.status-solicitado { background: #e3f2fd; color: #1976d2; }
.status-aprovado { background: #e8f5e8; color: #2e7d32; }
.status-faturado { background: #fff3e0; color: #f57c00; }
.status-entregue { background: #e8eaf6; color: #3f51b5; }

.row-actions {
    font-size: 12px;
}

.row-actions a {
    text-decoration: none;
}

.row-actions a.delete {
    color: #e74c3c;
}

.alerts-list {
    space-y: 10px;
}

.alert-item {
    display: flex;
    align-items: flex-start;
    gap: 10px;
    padding: 10px;
    border-radius: 6px;
    border-left: 4px solid #3498db;
}

.alert-item.alert-success {
    border-left-color: #2ecc71;
    background: #f8fff8;
}

.alert-item.alert-warning {
    border-left-color: #f39c12;
    background: #fffbf0;
}

.alert-item.alert-error {
    border-left-color: #e74c3c;
    background: #fdf2f2;
}

.tiers-list {
    space-y: 10px;
}

.tier-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 8px 0;
}

.tier-name {
    flex: 1;
    font-size: 14px;
}

.tier-count {
    font-weight: bold;
    color: #2c3e50;
}

.tier-bar {
    flex: 2;
    background: #ecf0f1;
    border-radius: 10px;
    height: 6px;
    overflow: hidden;
}

.tier-fill {
    height: 100%;
    background: #3498db;
    transition: width 0.3s ease;
}

.activity-list {
    space-y: 15px;
}

.activity-item {
    display: flex;
    align-items: flex-start;
    gap: 10px;
    padding-bottom: 15px;
    border-bottom: 1px solid #f1f1f1;
}

.activity-item:last-child {
    border-bottom: none;
    padding-bottom: 0;
}

.activity-icon {
    width: 32px;
    height: 32px;
    background: #f8f9fa;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #3498db;
}

.activity-content p {
    margin: 0 0 5px 0;
    font-size: 14px;
}

.activity-content small {
    color: #7f8c8d;
    font-size: 12px;
}

.view-all {
    font-size: 12px;
    color: #3498db;
    text-decoration: none;
}

.wp-list-table {
    margin: 0;
}

.wp-list-table th {
    font-weight: 600;
}
</style>