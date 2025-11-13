<?php
/**
 * Helper Functions for Soda Perfeita Plugin
 * 
 * @package SodaPerfeita
 * @since 1.0.0
 */

// Prevenir acesso direto
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Verifica se o usuário atual tem um role específico
 * 
 * @param string|array $roles Role ou array de roles para verificar
 * @return bool
 */
function soda_perfeita_user_has_role($roles) {
    $user = wp_get_current_user();
    
    if (empty($user)) {
        return false;
    }
    
    $roles = (array) $roles;
    
    foreach ($roles as $role) {
        if (in_array($role, (array) $user->roles)) {
            return true;
        }
    }
    
    return false;
}

/**
 * Retorna o role do usuário atual de forma legível
 * 
 * @return string
 */
function soda_perfeita_get_current_user_role() {
    $user = wp_get_current_user();
    return !empty($user->roles[0]) ? $user->roles[0] : '';
}

/**
 * Verifica se o usuário atual é admin Preshh
 * 
 * @return bool
 */
function soda_perfeita_is_admin_preshh() {
    return soda_perfeita_user_has_role('admin_preshh');
}

/**
 * Verifica se o usuário atual é admin DVG
 * 
 * @return bool
 */
function soda_perfeita_is_admin_dvg() {
    return soda_perfeita_user_has_role('admin_dvg');
}

/**
 * Verifica se o usuário atual é franqueado Preshh
 * 
 * @return bool
 */
function soda_perfeita_is_franqueado() {
    return soda_perfeita_user_has_role('franqueado_preshh');
}

/**
 * Verifica se o usuário atual é distribuidor DVG
 * 
 * @return bool
 */
function soda_perfeita_is_distribuidor() {
    return soda_perfeita_user_has_role('distribuidor_dvg');
}

/**
 * Verifica se o usuário atual é cliente final
 * 
 * @return bool
 */
function soda_perfeita_is_cliente_final() {
    return soda_perfeita_user_has_role('cliente_final');
}

/**
 * Obtém dados de um cliente pelo ID
 * 
 * @param int $cliente_id
 * @return array
 */
function soda_perfeita_get_cliente_data($cliente_id) {
    $cliente_data = array(
        'id' => $cliente_id,
        'nome' => get_the_title($cliente_id),
        'cnpj' => get_field('cnpj', $cliente_id),
        'status' => get_field('status', $cliente_id),
        'tier' => get_field('tier_atual', $cliente_id),
        'franqueado' => get_field('franqueado_responsavel', $cliente_id),
        'distribuidor' => get_field('distribuidor_responsavel', $cliente_id),
        'data_adesao' => get_field('data_adesao', $cliente_id),
        'ultimo_pedido' => get_field('data_ultimo_pedido', $cliente_id),
    );
    
    return apply_filters('soda_perfeita_cliente_data', $cliente_data, $cliente_id);
}

/**
 * Calcula a média móvel de pedidos dos últimos 90 dias
 * 
 * @param int $cliente_id
 * @return float
 */
function soda_perfeita_calcular_media_pedidos_90_dias($cliente_id) {
    $data_inicio = date('Y-m-d', strtotime('-90 days'));
    $data_fim = date('Y-m-d');
    
    $args = array(
        'limit' => -1,
        'customer_id' => $cliente_id,
        'status' => 'completed',
        'date_created' => $data_inicio . '...' . $data_fim,
    );

    $pedidos = wc_get_orders($args);
    $total_itens = 0; // Renomeado para refletir que é a soma de itens
    
    foreach ($pedidos as $pedido) {
        // Usa o método nativo para pegar a quantidade total de itens no pedido
        $total_itens += $pedido->get_item_count();
    }
    
    // Calcula a média dividindo pelo número de períodos (90 dias = 3 meses)
    return $total_itens > 0 ? round($total_itens / 3, 2) : 0;
}

/**
 * Verifica se um cliente está adimplente
 * 
 * @param int $cliente_id
 * @return bool
 */
function soda_perfeita_cliente_adimplente($cliente_id) {
    $status_financeiro = get_field('status_financeiro', $cliente_id);
    return $status_financeiro === 'adimplente';
}

/**
 * Bloqueia pedidos de cliente inadimplente
 * 
 * @param int $cliente_id
 * @return bool
 */
function soda_perfeita_bloquear_cliente_inadimplente($cliente_id) {
    if (!soda_perfeita_cliente_adimplente($cliente_id)) {
        update_field('pedidos_bloqueados', true, $cliente_id);
        return true;
    }
    
    update_field('pedidos_bloqueados', false, $cliente_id);
    return false;
}

/**
 * Obtém o tier atual de um cliente baseado na performance
 * 
 * @param int $cliente_id
 * @return string
 */
function soda_perfeita_get_tier_cliente($cliente_id) {
    $media_pedidos = soda_perfeita_calcular_media_pedidos_90_dias($cliente_id);
    $adimplente = soda_perfeita_cliente_adimplente($cliente_id);
    $treinamento_concluido = get_field('treinamento_concluido', $cliente_id);
    
    if (!$treinamento_concluido || !$adimplente) {
        return 'tier_0'; // Não elegível
    }
    
    if ($media_pedidos >= 25 && $adimplente) {
        return 'tier_3'; // Excelência
    } elseif ($media_pedidos >= 12 && $adimplente) {
        return 'tier_2'; // Performance
    } else {
        return 'tier_1'; // Valor Base
    }
}

/**
 * Retorna os benefícios de cada tier
 * 
 * @param string $tier
 * @return array
 */
function soda_perfeita_get_beneficios_tier($tier) {
    $beneficios = array(
        'tier_1' => array(
            'garrafas_inclusas' => 4,
            'material_promocional' => '1 banner + 20 displays',
            'suporte' => 'básico',
            'workshops' => 'webinars online',
            'amostras' => 'sabores padrão'
        ),
        'tier_2' => array(
            'garrafas_inclusas' => 12,
            'material_promocional' => '1 banner + 30 displays',
            'suporte' => 'trade marketing regional',
            'workshops' => 'presenciais regionais',
            'amostras' => 'novos sabores'
        ),
        'tier_3' => array(
            'garrafas_inclusas' => 25,
            'material_promocional' => 'personalizado',
            'suporte' => 'dedicado premium',
            'workshops' => 'VIP nacionais',
            'amostras' => 'antecipada premium',
            'subsidio' => 90.00
        )
    );
    
    return isset($beneficios[$tier]) ? $beneficios[$tier] : array();
}

/**
 * Formata valor monetário para exibição
 * 
 * @param float $valor
 * @return string
 */
function soda_perfeita_format_currency($valor) {
    return 'R$ ' . number_format($valor, 2, ',', '.');
}

/**
 * Formata data para o padrão brasileiro
 * 
 * @param string $data
 * @return string
 */
function soda_perfeita_format_date($data) {
    return date('d/m/Y', strtotime($data));
}

/**
 * Calcula diferença em dias entre duas datas
 * 
 * @param string $data_inicio
 * @param string $data_fim
 * @return int
 */
function soda_perfeita_calcular_dias_diferenca($data_inicio, $data_fim = null) {
    $data_fim = $data_fim ?: current_time('mysql');
    
    $datetime1 = new DateTime($data_inicio);
    $datetime2 = new DateTime($data_fim);
    $interval = $datetime1->diff($datetime2);
    
    return $interval->days;
}

/**
 * Envia notificação por email
 * 
 * @param string $to
 * @param string $subject
 * @param string $message
 * @param array $headers
 * @return bool
 */
function soda_perfeita_send_email($to, $subject, $message, $headers = array()) {
    $default_headers = array('Content-Type: text/html; charset=UTF-8');
    $headers = array_merge($default_headers, $headers);
    
    return wp_mail($to, $subject, $message, $headers);
}

/**
 * Gera token único para identificação
 * 
 * @param int $length
 * @return string
 */
function soda_perfeita_generate_token($length = 16) {
    return bin2hex(random_bytes($length));
}

/**
 * Sanitiza dados de formulário
 * 
 * @param mixed $data
 * @return mixed
 */
function soda_perfeita_sanitize_data($data) {
    if (is_array($data)) {
        return array_map('soda_perfeita_sanitize_data', $data);
    }
    
    return sanitize_text_field(stripslashes($data));
}

/**
 * Valida CNPJ
 * 
 * @param string $cnpj
 * @return bool
 */
function soda_perfeita_validar_cnpj($cnpj) {
    $cnpj = preg_replace('/[^0-9]/', '', $cnpj);
    
    if (strlen($cnpj) != 14) {
        return false;
    }
    
    // Evita CNPJs inválidos conhecidos
    if (preg_match('/(\d)\1{13}/', $cnpj)) {
        return false;
    }
    
    // Validação do dígito verificador
    for ($t = 12; $t < 14; $t++) {
        $d = 0;
        $c = 0;
        for ($m = $t - 7; $m >= 2; $m--, $c++) {
            $d += $cnpj[$c] * $m;
        }
        for ($m = 9; $m >= 2; $m--, $c++) {
            $d += $cnpj[$c] * $m;
        }
        $d = ((10 * $d) % 11) % 10;
        if ($cnpj[$c] != $d) {
            return false;
        }
    }
    
    return true;
}

/**
 * Obtém distribuidores por região
 * 
 * @param string $regiao
 * @return array
 */
function soda_perfeita_get_distribuidores_por_regiao($regiao = '') {
    $args = array(
        'post_type' => 'distribuidor',
        'posts_per_page' => -1,
        'post_status' => 'publish'
    );
    
    if (!empty($regiao)) {
        $args['meta_query'] = array(
            array(
                'key' => 'regiao',
                'value' => $regiao,
                'compare' => '='
            )
        );
    }
    
    $distribuidores = get_posts($args);
    $result = array();
    
    foreach ($distribuidores as $distribuidor) {
        $result[] = array(
            'id' => $distribuidor->ID,
            'nome' => $distribuidor->post_title,
            'regiao' => get_field('regiao', $distribuidor->ID),
            'contato' => get_field('contato', $distribuidor->ID),
            'email' => get_field('email', $distribuidor->ID)
        );
    }
    
    return $result;
}

/**
 * Log de atividades do sistema
 * 
 * @param string $acao
 * @param string $detalhes
 * @param int $usuario_id
 * @return void
 */
function soda_perfeita_log_activity($acao, $detalhes = '', $usuario_id = null) {
    $usuario_id = $usuario_id ?: get_current_user_id();
    
    $log_entry = array(
        'acao' => $acao,
        'detalhes' => $detalhes,
        'usuario_id' => $usuario_id,
        'data' => current_time('mysql'),
        'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
    );
    
    // Adiciona ao log do sistema
    SodaPerfeita::log("Ação: {$acao} | Detalhes: {$detalhes} | Usuário: {$usuario_id}");
    
    // Também salva como post type para relatórios
    $log_id = wp_insert_post(array(
        'post_type' => 'sp_logs',
        'post_title' => $acao,
        'post_content' => $detalhes,
        'post_status' => 'publish',
        'post_author' => $usuario_id
    ));
    
    if ($log_id && !is_wp_error($log_id)) {
        update_field('tipo_acao', $acao, $log_id);
        update_field('detalhes_completos', $detalhes, $log_id);
        update_field('ip_usuario', $log_entry['ip'], $log_id);
    }
}

/**
 * Retorna opções do plugin
 * 
 * @param string $key
 * @param mixed $default
 * @return mixed
 */
function soda_perfeita_get_option($key, $default = '') {
    return get_option('soda_perfeita_' . $key, $default);
}

/**
 * Verifica se é uma requisição AJAX
 * 
 * @return bool
 */
function soda_perfeita_is_ajax() {
    return defined('DOING_AJAX') && DOING_AJAX;
}

/**
 * Retorna a URL do dashboard baseado no role do usuário
 * 
 * @return string
 */
function soda_perfeita_get_dashboard_url() {
    $base_url = get_permalink(get_page_by_path('dashboard-soda-perfeita'));
    
    if (soda_perfeita_is_admin_preshh()) {
        return $base_url . '?view=admin';
    } elseif (soda_perfeita_is_admin_dvg()) {
        return $base_url . '?view=dvg';
    } elseif (soda_perfeita_is_franqueado()) {
        return $base_url . '?view=franqueado';
    } elseif (soda_perfeita_is_distribuidor()) {
        return $base_url . '?view=distribuidor';
    } else {
        return $base_url . '?view=cliente';
    }
}

/**
 * Debug function - apenas em desenvolvimento
 * 
 * @param mixed $data
 * @param bool $die
 */
function soda_perfeita_debug($data, $die = true) {
    if (defined('WP_DEBUG') && WP_DEBUG) {
        echo '<pre>';
        print_r($data);
        echo '</pre>';
        
        if ($die) {
            die();
        }
    }
}