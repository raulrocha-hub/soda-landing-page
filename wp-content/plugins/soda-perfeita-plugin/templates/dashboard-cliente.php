<?php
/**
 * Dashboard Template for Cliente Final
 * 
 * @package SodaPerfeita
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="soda-dashboard-cliente">
    <div class="dashboard-header">
        <h2>Meu Dashboard - <?php echo esc_html(get_the_title($cliente_id)); ?></h2>
        <div class="dashboard-actions">
            <button class="btn btn-primary" onclick="sodaSolicitarPedido()">
                <span class="dashicons dashicons-cart"></span>
                Solicitar Pedido
            </button>
        </div>
    </div>

    <div class="dashboard-grid">
        <!-- Status do Tier -->
        <div class="dashboard-card tier-status">
            <div class="card-header">
                <h3>Meu Tier Atual</h3>
            </div>
            <div class="card-body">
                <div class="tier-badge tier-<?php echo esc_attr($tier_atual); ?>">
                    <span class="tier-name"><?php echo esc_html($tier_config['name']); ?></span>
                    <span class="tier-level"><?php echo str_replace('tier_', 'Tier ', $tier_atual); ?></span>
                </div>
                <div class="tier-progress">
                    <div class="progress-info">
                        <span><?php echo esc_html($pontuacao_total); ?> pontos</span>
                        <?php if ($proximo_tier): ?>
                        <span><?php echo esc_html($proximo_tier['min_points'] - $pontuacao_total); ?> pontos para o próximo nível</span>
                        <?php endif; ?>
                    </div>
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: <?php echo esc_attr($progresso); ?>%"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Próximos Benefícios -->
        <div class="dashboard-card next-benefits">
            <div class="card-header">
                <h3>Próximos Benefícios</h3>
            </div>
            <div class="card-body">
                <?php if ($proximo_tier): ?>
                <ul class="benefits-list">
                    <?php foreach ($proximo_tier['benefits'] as $beneficio => $valor): ?>
                    <li>
                        <span class="benefit-icon">➤</span>
                        <span class="benefit-text"><?php echo esc_html($valor); ?></span>
                    </li>
                    <?php endforeach; ?>
                </ul>
                <?php else: ?>
                <p>Você atingiu o nível máximo! 🎉</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Estatísticas Rápidas -->
        <div class="dashboard-card quick-stats">
            <div class="card-header">
                <h3>Minhas Estatísticas</h3>
            </div>
            <div class="card-body">
                <div class="stats-grid">
                    <div class="stat-item">
                        <span class="stat-value"><?php echo esc_html($total_pedidos); ?></span>
                        <span class="stat-label">Pedidos Realizados</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-value"><?php echo esc_html($media_mensal); ?></span>
                        <span class="stat-label">Média Mensal</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-value"><?php echo esc_html($dias_ativo); ?></span>
                        <span class="stat-label">Dias Ativo</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pedidos Recentes -->
        <div class="dashboard-card recent-orders">
            <div class="card-header">
                <h3>Pedidos Recentes</h3>
                <a href="<?php echo esc_url($pedidos_page); ?>" class="view-all">Ver Todos</a>
            </div>
            <div class="card-body">
                <?php if (!empty($pedidos_recentes)): ?>
                <div class="orders-list">
                    <?php foreach ($pedidos_recentes as $pedido): ?>
                    <div class="order-item">
                        <div class="order-info">
                            <span class="order-id">#<?php echo esc_html($pedido['id']); ?></span>
                            <span class="order-date"><?php echo esc_html($pedido['data']); ?></span>
                            <span class="order-quantity"><?php echo esc_html($pedido['quantidade']); ?> unidades</span>
                        </div>
                        <span class="order-status status-<?php echo esc_attr($pedido['status']); ?>">
                            <?php echo esc_html($pedido['status_label']); ?>
                        </span>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php else: ?>
                <p class="no-orders">Nenhum pedido realizado ainda.</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Conquistas -->
        <div class="dashboard-card achievements">
            <div class="card-header">
                <h3>Minhas Conquistas</h3>
            </div>
            <div class="card-body">
                <div class="achievements-grid">
                    <?php foreach ($conquistas as $conquista): ?>
                    <div class="achievement-item <?php echo $conquista['concluida'] ? 'completed' : 'locked'; ?>">
                        <div class="achievement-icon">
                            <?php if ($conquista['concluida']): ?>
                            <span class="dashicons dashicons-yes-alt"></span>
                            <?php else: ?>
                            <span class="dashicons dashicons-lock"></span>
                            <?php endif; ?>
                        </div>
                        <div class="achievement-info">
                            <strong><?php echo esc_html($conquista['titulo']); ?></strong>
                            <p><?php echo esc_html($conquista['descricao']); ?></p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Notificações -->
        <div class="dashboard-card notifications">
            <div class="card-header">
                <h3>Notificações</h3>
            </div>
            <div class="card-body">
                <?php if (!empty($notificacoes)): ?>
                <div class="notifications-list">
                    <?php foreach ($notificacoes as $notificacao): ?>
                    <div class="notification-item <?php echo esc_attr($notificacao['tipo']); ?>">
                        <div class="notification-icon">
                            <?php switch($notificacao['tipo']):
                                case 'info': ?>
                                    <span class="dashicons dashicons-info"></span>
                                    <?php break;
                                case 'success': ?>
                                    <span class="dashicons dashicons-yes"></span>
                                    <?php break;
                                case 'warning': ?>
                                    <span class="dashicons dashicons-warning"></span>
                                    <?php break;
                                default: ?>
                                    <span class="dashicons dashicons-bell"></span>
                            <?php endswitch; ?>
                        </div>
                        <div class="notification-content">
                            <p><?php echo esc_html($notificacao['mensagem']); ?></p>
                            <small><?php echo esc_html($notificacao['data']); ?></small>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php else: ?>
                <p class="no-notifications">Nenhuma notificação no momento.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
function sodaSolicitarPedido() {
    window.location.href = '<?php echo esc_url($form_pedido_url); ?>';
}
</script>

<style>
.soda-dashboard-cliente {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
}

.dashboard-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
    padding-bottom: 20px;
    border-bottom: 1px solid #e0e0e0;
}

.dashboard-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.dashboard-card {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    overflow: hidden;
}

.card-header {
    background: #f8f9fa;
    padding: 15px 20px;
    border-bottom: 1px solid #e9ecef;
}

.card-header h3 {
    margin: 0;
    font-size: 16px;
    color: #333;
}

.card-body {
    padding: 20px;
}

.tier-badge {
    display: inline-flex;
    flex-direction: column;
    align-items: center;
    padding: 15px;
    border-radius: 8px;
    color: white;
    margin-bottom: 15px;
}

.tier-badge.tier_1 { background: linear-gradient(135deg, #95a5a6, #7f8c8d); }
.tier-badge.tier_2 { background: linear-gradient(135deg, #3498db, #2980b9); }
.tier-badge.tier_3 { background: linear-gradient(135deg, #f39c12, #e67e22); }

.tier-name {
    font-size: 18px;
    font-weight: bold;
}

.tier-level {
    font-size: 14px;
    opacity: 0.9;
}

.progress-bar {
    background: #ecf0f1;
    border-radius: 10px;
    height: 8px;
    margin: 10px 0;
    overflow: hidden;
}

.progress-fill {
    background: #2ecc71;
    height: 100%;
    transition: width 0.3s ease;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 15px;
}

.stat-item {
    text-align: center;
    padding: 10px;
}

.stat-value {
    display: block;
    font-size: 24px;
    font-weight: bold;
    color: #2c3e50;
}

.stat-label {
    font-size: 12px;
    color: #7f8c8d;
    text-transform: uppercase;
}

.orders-list {
    space-y: 10px;
}

.order-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 0;
    border-bottom: 1px solid #f1f1f1;
}

.order-info {
    display: flex;
    gap: 15px;
    align-items: center;
}

.order-status {
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: bold;
}

.status-aprovado { background: #d4edda; color: #155724; }
.status-faturado { background: #fff3cd; color: #856404; }
.status-entregue { background: #d1ecf1; color: #0c5460; }

.achievements-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 15px;
}

.achievement-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px;
    border-radius: 6px;
    border: 1px solid #e0e0e0;
}

.achievement-item.completed {
    background: #f8fff8;
    border-color: #2ecc71;
}

.achievement-item.locked {
    opacity: 0.6;
    background: #f9f9f9;
}

.notifications-list {
    space-y: 10px;
}

.notification-item {
    display: flex;
    align-items: flex-start;
    gap: 10px;
    padding: 10px;
    border-radius: 6px;
    border-left: 4px solid #3498db;
}

.notification-item.success {
    border-left-color: #2ecc71;
    background: #f8fff8;
}

.notification-item.warning {
    border-left-color: #f39c12;
    background: #fffbf0;
}

.notification-item.info {
    border-left-color: #3498db;
    background: #f0f8ff;
}

.btn {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 10px 20px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    text-decoration: none;
    font-size: 14px;
    transition: all 0.3s ease;
}

.btn-primary {
    background: #3498db;
    color: white;
}

.btn-primary:hover {
    background: #2980b9;
}

.view-all {
    font-size: 12px;
    color: #3498db;
    text-decoration: none;
}

.no-orders,
.no-notifications {
    text-align: center;
    color: #7f8c8d;
    font-style: italic;
    padding: 20px;
}
</style>