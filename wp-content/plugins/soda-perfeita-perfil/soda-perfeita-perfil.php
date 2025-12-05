<?php
/**
 * Plugin Name: Soda Perfeita - Perfis
 * Description: Formulário multi-etapas + gravação em tabela própria + listagem DataTables.
 * Version: 1.0.0
 * Author: Raul
 */

if (!defined('ABSPATH')) {
    exit;
}

class Soda_Perfeita_Perfil {
    private $table;

    public function __construct() {
        global $wpdb;
        $this->table = $wpdb->prefix . 'soda_perfis';

        register_activation_hook(__FILE__, [$this, 'activate']);
        add_shortcode('soda_perfeita_perfil_form', [$this, 'render_form']);
        add_shortcode('soda_perfeita_perfil_list', [$this, 'render_list']);
        add_action('wp_enqueue_scripts', [$this, 'enqueue_assets']);
        add_action('admin_menu', [$this, 'register_admin_menu']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_assets']);
        add_action('wp_ajax_soda_perfeita_perfil_submit', [$this, 'handle_submit']);
        add_action('wp_ajax_nopriv_soda_perfeita_perfil_submit', [$this, 'handle_submit']);
        add_action('wp_ajax_soda_perfeita_perfil_list', [$this, 'handle_list']);
        add_action('wp_ajax_nopriv_soda_perfeita_perfil_list', [$this, 'handle_list']);
        add_action('wp_ajax_soda_perfeita_perfil_get', [$this, 'handle_get_profile']);
        add_action('wp_ajax_nopriv_soda_perfeita_perfil_get', [$this, 'handle_get_profile']);
        add_action('wp_ajax_soda_perfeita_perfil_update', [$this, 'handle_update_profile']);
        add_action('wp_ajax_nopriv_soda_perfeita_perfil_update', [$this, 'handle_update_profile']);
    }

    public function activate() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE {$this->table} (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            created_at DATETIME NOT NULL,
            nome_negocio VARCHAR(255) NOT NULL,
            cidade_estado VARCHAR(255) NOT NULL,
            tipo_negocio TEXT,
            segmento_clientes TEXT,
            atendimento_turnos TEXT NOT NULL,
            principal_diferencial TEXT,
            experiencia_bebidas TEXT,
            delivery VARCHAR(255),
            coqueteis TEXT,
            ticket_medio_drinks DECIMAL(10,2) NULL,
            mao_obra VARCHAR(255) NOT NULL,
            mao_obra_comentarios TEXT,
            gelo_operacao VARCHAR(255),
            frequencia_eventos TEXT,
            expectativas TEXT,
            satisfacao_geral TINYINT,
            maquina_gelo VARCHAR(255),
            consumo_semanal_gelo VARCHAR(255),
            valor_gasto_gelo VARCHAR(255),
            observacoes_finais TEXT,
            PRIMARY KEY (id)
        ) $charset_collate;";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta($sql);
    }

    public function enqueue_assets() {
        // Carrega assets apenas quando necessario: shortcode na página ou template dedicado.
        if (!is_singular()) {
            return;
        }

        $enqueue = false;
        $post = get_post();

        if ($post instanceof WP_Post) {
            $template_slug = get_page_template_slug($post->ID);
            $template_matches = in_array($template_slug, [
                'page-formulario-perfil.php',
                'templates/page-formulario-perfil.php',
                get_stylesheet_directory() . '/page-formulario-perfil.php',
                get_template_directory() . '/page-formulario-perfil.php',
            ], true);

            if (has_shortcode($post->post_content, 'soda_perfeita_perfil_form') || has_shortcode($post->post_content, 'soda_perfeita_perfil_list')) {
                $enqueue = true;
            }
            // A página usa o template especial que injeta o shortcode direto no PHP.
            if ($template_matches || is_page_template('page-formulario-perfil.php')) {
                $enqueue = true;
            }
        }

        if (!$enqueue) {
            return;
        }

        $this->enqueue_shared_assets();
    }

    private function enqueue_shared_assets() {
        wp_enqueue_script('jquery');
        wp_enqueue_style('bootstrap-5', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css', [], '5.3.3');
        wp_enqueue_script('bootstrap-5', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js', [], '5.3.3', true);
        wp_enqueue_style('datatables', 'https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css', [], '1.13.8');
        wp_enqueue_script('datatables', 'https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js', ['jquery'], '1.13.8', true);
        wp_enqueue_script('datatables-bs', 'https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js', ['datatables'], '1.13.8', true);
        wp_enqueue_script('soda-perfil', plugin_dir_url(__FILE__) . 'soda-perfil.js', ['jquery'], '1.0.0', true);
        wp_localize_script('soda-perfil', 'SodaPerfil', [
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce'   => wp_create_nonce('soda_perfil_nonce'),
        ]);
    }

    public function register_admin_menu() {
        add_menu_page(
            'Perfis Soda Perfeita',
            'Perfis Soda',
            'edit_pages',
            'soda-perfeita-perfis',
            [$this, 'render_admin_page'],
            'dashicons-groups',
            26
        );
    }

    public function enqueue_admin_assets($hook) {
        if ($hook !== 'toplevel_page_soda-perfeita-perfis') {
            return;
        }
        $this->enqueue_shared_assets();
    }

    public function render_admin_page() {
        echo '<div class="wrap soda-perfis-wrap"><h1>Perfis Soda Perfeita</h1>';
        echo $this->render_list();
        echo '</div>';
    }

    public function handle_submit() {
        check_ajax_referer('soda_perfil_nonce', 'nonce');
        if (!isset($_POST['dados']) || !is_array($_POST['dados'])) {
            wp_send_json_error(['message' => 'Dados ausentes.']);
        }

        $d = $_POST['dados'];

        $sanitize_array = static function($key, $default = '') use ($d) {
            return isset($d[$key]) ? sanitize_text_field($d[$key]) : $default;
        };
        $sanitize_textarea = static function($key) use ($d) {
            return isset($d[$key]) ? sanitize_textarea_field($d[$key]) : '';
        };
        $sanitize_array_multi = static function($key) use ($d) {
            if (!isset($d[$key]) || !is_array($d[$key])) {
                return '';
            }
            return wp_json_encode(array_map('sanitize_text_field', $d[$key]));
        };

        $payload = [
            'created_at'            => current_time('mysql'),
            'nome_negocio'          => $sanitize_array('nome_negocio'),
            'cidade_estado'         => $sanitize_array('cidade_estado'),
            'tipo_negocio'          => $sanitize_array_multi('tipo_negocio'),
            'segmento_clientes'     => $sanitize_textarea('segmento_clientes'),
            'atendimento_turnos'    => $sanitize_textarea('atendimento_turnos'),
            'principal_diferencial' => $sanitize_textarea('principal_diferencial'),
            'experiencia_bebidas'   => $sanitize_textarea('experiencia_bebidas'),
            'delivery'              => $sanitize_array('delivery'),
            'coqueteis'             => $sanitize_array_multi('coqueteis'),
            'ticket_medio_drinks'   => isset($d['ticket_medio_drinks']) ? floatval($d['ticket_medio_drinks']) : null,
            'mao_obra'              => $sanitize_array('mao_obra'),
            'mao_obra_comentarios'  => $sanitize_textarea('mao_obra_comentarios'),
            'gelo_operacao'         => $sanitize_array('gelo_operacao'),
            'frequencia_eventos'    => $sanitize_array_multi('frequencia_eventos'),
            'expectativas'          => $sanitize_textarea('expectativas'),
            'satisfacao_geral'      => isset($d['satisfacao_geral']) ? intval($d['satisfacao_geral']) : null,
            'maquina_gelo'          => $sanitize_array('maquina_gelo'),
            'consumo_semanal_gelo'  => $sanitize_array('consumo_semanal_gelo'),
            'valor_gasto_gelo'      => $sanitize_array('valor_gasto_gelo'),
            'observacoes_finais'    => $sanitize_textarea('observacoes_finais'),
        ];

        foreach (['nome_negocio', 'cidade_estado', 'atendimento_turnos', 'mao_obra'] as $required) {
            if (empty($payload[$required])) {
                wp_send_json_error(['message' => 'Campos obrigatórios não preenchidos.']);
            }
        }

        global $wpdb;
        $inserted = $wpdb->insert($this->table, $payload);

        if (!$inserted) {
            wp_send_json_error(['message' => 'Erro ao salvar.']);
        }

        wp_send_json_success(['message' => 'Formulário enviado com sucesso!']);
    }

    public function handle_list() {
        check_ajax_referer('soda_perfil_nonce', 'nonce');

        global $wpdb;
        $rows = $wpdb->get_results("
            SELECT 
                id,
                created_at,
                nome_negocio,
                cidade_estado,
                tipo_negocio,
                segmento_clientes,
                atendimento_turnos,
                principal_diferencial,
                experiencia_bebidas,
                delivery,
                coqueteis,
                ticket_medio_drinks,
                mao_obra,
                mao_obra_comentarios,
                gelo_operacao,
                frequencia_eventos,
                expectativas,
                satisfacao_geral,
                maquina_gelo,
                consumo_semanal_gelo,
                valor_gasto_gelo,
                observacoes_finais
            FROM {$this->table}
            ORDER BY created_at DESC
        ");

        wp_send_json_success($rows);
    }

    public function handle_get_profile() {
        check_ajax_referer('soda_perfil_nonce', 'nonce');
        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
        if ($id <= 0) {
            wp_send_json_error(['message' => 'ID inválido.']);
        }

        global $wpdb;
        $row = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$this->table} WHERE id = %d", $id), ARRAY_A);

        if (!$row) {
            wp_send_json_error(['message' => 'Registro não encontrado.']);
        }

        // Decodifica campos JSON para arrays
        foreach (['tipo_negocio', 'coqueteis', 'frequencia_eventos'] as $jsonField) {
            if (!empty($row[$jsonField])) {
                $decoded = json_decode($row[$jsonField], true);
                $row[$jsonField] = is_array($decoded) ? $decoded : [];
            } else {
                $row[$jsonField] = [];
            }
        }

        wp_send_json_success($row);
    }

    public function handle_update_profile() {
        check_ajax_referer('soda_perfil_nonce', 'nonce');

        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
        if ($id <= 0) {
            wp_send_json_error(['message' => 'ID inválido.']);
        }

        if (!isset($_POST['dados']) || !is_array($_POST['dados'])) {
            wp_send_json_error(['message' => 'Dados ausentes.']);
        }

        global $wpdb;
        $current = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$this->table} WHERE id = %d", $id), ARRAY_A);
        if (!$current) {
            wp_send_json_error(['message' => 'Registro não encontrado.']);
        }

        $d = $_POST['dados'];

        $sanitize_array = static function($key, $default = '') use ($d, $current) {
            return array_key_exists($key, $d) ? sanitize_text_field($d[$key]) : (isset($current[$key]) ? $current[$key] : $default);
        };
        $sanitize_textarea = static function($key) use ($d, $current) {
            if (array_key_exists($key, $d)) {
                return sanitize_textarea_field($d[$key]);
            }
            return isset($current[$key]) ? $current[$key] : '';
        };
        $sanitize_array_multi = static function($key) use ($d, $current) {
            if (!array_key_exists($key, $d)) {
                return isset($current[$key]) ? $current[$key] : '';
            }
            if (!is_array($d[$key])) {
                return '';
            }
            return wp_json_encode(array_map('sanitize_text_field', $d[$key]));
        };

        $payload = [
            'nome_negocio'          => $sanitize_array('nome_negocio'),
            'cidade_estado'         => $sanitize_array('cidade_estado'),
            'tipo_negocio'          => $sanitize_array_multi('tipo_negocio'),
            'segmento_clientes'     => $sanitize_textarea('segmento_clientes'),
            'atendimento_turnos'    => $sanitize_textarea('atendimento_turnos'),
            'principal_diferencial' => $sanitize_textarea('principal_diferencial'),
            'experiencia_bebidas'   => $sanitize_textarea('experiencia_bebidas'),
            'delivery'              => $sanitize_array('delivery'),
            'coqueteis'             => $sanitize_array_multi('coqueteis'),
            'ticket_medio_drinks'   => array_key_exists('ticket_medio_drinks', $d) ? floatval($d['ticket_medio_drinks']) : (isset($current['ticket_medio_drinks']) ? $current['ticket_medio_drinks'] : null),
            'mao_obra'              => $sanitize_array('mao_obra'),
            'mao_obra_comentarios'  => $sanitize_textarea('mao_obra_comentarios'),
            'gelo_operacao'         => $sanitize_array('gelo_operacao'),
            'frequencia_eventos'    => $sanitize_array_multi('frequencia_eventos'),
            'expectativas'          => $sanitize_textarea('expectativas'),
            'satisfacao_geral'      => array_key_exists('satisfacao_geral', $d) ? intval($d['satisfacao_geral']) : (isset($current['satisfacao_geral']) ? $current['satisfacao_geral'] : null),
            'maquina_gelo'          => $sanitize_array('maquina_gelo'),
            'consumo_semanal_gelo'  => $sanitize_array('consumo_semanal_gelo'),
            'valor_gasto_gelo'      => $sanitize_array('valor_gasto_gelo'),
            'observacoes_finais'    => $sanitize_textarea('observacoes_finais'),
        ];

        foreach (['nome_negocio', 'cidade_estado', 'atendimento_turnos', 'mao_obra'] as $required) {
            if (empty($payload[$required])) {
                wp_send_json_error(['message' => 'Campos obrigatórios não preenchidos.']);
            }
        }

        $updated = $wpdb->update($this->table, $payload, ['id' => $id]);

        if ($updated === false) {
            wp_send_json_error(['message' => 'Erro ao atualizar.']);
        }

        wp_send_json_success(['message' => 'Registro atualizado com sucesso.']);
    }

    public function render_form() {
        ob_start();
        ?>
 <style>
        :root {
            --soda-blue:  #2558A4;
            --soda-pink:  #EC2790;
            --soda-yellow:#FFD050;
            --soda-cyan:  #01AFEF;
        }

        body {
            background: #f4f6fb;
            font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        }

        .soda-logo {
            max-height: 60px;
        }

        .hero-area {
            background: linear-gradient(135deg, var(--soda-blue), var(--soda-cyan));
            color: #fff;
            border-radius: 1.25rem;
            padding: 1.75rem 2rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 12px 30px rgba(0,0,0,0.12);
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: .4rem;
            padding: .2rem .75rem;
            border-radius: 999px;
            background: rgba(255,255,255,.12);
            font-size: .75rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .form-card {
            border-radius: 1.25rem;
            border: 0;
            box-shadow: 0 12px 30px rgba(0,0,0,0.08);
        }

        .form-card .card-header {
            border-radius: 1.25rem 1.25rem 0 0;
            background: #ffffff;
            border-bottom: 1px solid rgba(0,0,0,.05);
        }

        .step-indicators {
            display: flex;
            gap: .75rem;
            align-items: center;
        }

        .step-indicator {
            display: flex;
            align-items: center;
            gap: .35rem;
            padding: .4rem .75rem;
            border-radius: 999px;
            font-size: .8rem;
            border: 1px solid transparent;
            color: #6b7280;
            background: #f3f4f6;
        }

        .step-indicator.active {
            background: var(--soda-blue);
            color: #fff;
        }

        .step-indicator.done {
            background: #e0f7f9;
            color: #036666;
            border-color: #a5f3fc;
        }

        .step-indicator .step-number {
            width: 20px;
            height: 20px;
            border-radius: 999px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: .7rem;
            font-weight: 600;
            background: rgba(0,0,0,.08);
        }

        .step-indicator.active .step-number {
            background: rgba(255,255,255,.22);
        }

        .step-indicator.done .step-number {
            background: #0ea5e9;
            color: #fff;
        }

        .progress {
            height: 6px;
            border-radius: 999px;
            background: #e5e7eb;
        }

        .progress-bar {
            background: linear-gradient(90deg, var(--soda-blue), var(--soda-pink));
        }

        .form-step {
            display: none;
        }

        .form-step.active {
            display: block;
        }

        .question-title {
            font-weight: 600;
            margin-bottom: .2rem;
        }

        .question-helper {
            font-size: .8rem;
            color: #6b7280;
        }

        .required-badge {
            color: var(--soda-pink);
            margin-left: .15rem;
        }

        .btn-soda-primary {
            background-color: var(--soda-blue);
            border-color: var(--soda-blue);
            color: #fff;
        }

        .btn-soda-primary:hover {
            background-color: #1d447f;
            border-color: #1d447f;
        }

        .btn-soda-outline {
            border-color: var(--soda-blue);
            color: var(--soda-blue);
            background: #fff;
        }

        .btn-soda-outline:hover {
            background-color: rgba(37,88,164,0.06);
        }

        .soda-chip {
            display: inline-flex;
            align-items: center;
            gap: .3rem;
            padding: .2rem .7rem;
            border-radius: 999px;
            background: #f3f4f6;
            font-size: .75rem;
            color: #4b5563;
        }

        @media (max-width: 767.98px) {
            .hero-area {
                padding: 1.25rem 1.25rem;
            }
            .step-indicators {
                flex-wrap: wrap;
            }
        }

        /* Reset total dos checkboxes/radios na página Soda Perfeita */
.soda-form input[type="checkbox"],
.soda-form input[type="radio"] {
    margin: 0 6px 0 0 !important;
    padding: 0 !important;
    transform: none !important;
    position: relative;
    top: 0 !important;
    left: 0 !important;
}

/* Força alinhamento horizontal e vertical */
.soda-form label {
    display: flex !important;
    align-items: center !important;
    gap: 6px;
    margin: 4px 0 !important;
}

/* Remove qualquer indentação herdada */
.soda-form .form-check,
.soda-form .checkbox-group,
.soda-form .radio-group {
    margin: 0 !important;
    padding: 0 !important;
}

    </style>      

<div class="container py-4 py-md-5">

    <!-- topo com logo + texto -->
    <div class="hero-area">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-3">
            <div class="d-flex align-items-center gap-3">
                <img src="/wp-content/themes/soda-perfeita/logo-sodaperfeita.webp" class="soda-logo" alt="Soda Perfeita">
                <div>
                    <div class="hero-badge">
                        <span>Plano Piloto</span>
                        <span style="width:5px;height:5px;border-radius:999px;background:#22c55e;"></span>
                        <span>Formulário</span>
                    </div>
                    <h1 class="h4 mt-2 mb-0">Avaliação para plano piloto Soda Perfeita</h1>
                    <p class="mb-0" style="font-size:.9rem;max-width:460px;">
                        As respostas abaixo nos ajudam a entender melhor sua operação de bebidas e
                        desenhar um plano piloto sob medida para o seu negócio.
                    </p>
                </div>
            </div>
            <div class="text-md-end">
                <span class="soda-chip">
                    <span style="width:8px;height:8px;border-radius:999px;background:var(--soda-yellow);"></span>
                    <span>Demora aproximada: 8–10 min</span>
                </span>
            </div>
        </div>

        <div class="progress mt-3">
            <div class="progress-bar" role="progressbar" aria-valuemin="0" aria-valuemax="100"></div>
        </div>
    </div>

    <!-- cartão principal -->
    <div class="card form-card mb-4">
        <div class="card-header">
            <div class="step-indicators">
                <div class="step-indicator active" data-step-indicator="1">
                    <span class="step-number">1</span>
                    <span>Negócio</span>
                </div>
                <div class="step-indicator" data-step-indicator="2">
                    <span class="step-number">2</span>
                    <span>Operação</span>
                </div>
                <div class="step-indicator" data-step-indicator="3">
                    <span class="step-number">3</span>
                    <span>Bebidas & Coquetéis</span>
                </div>
                <div class="step-indicator" data-step-indicator="4">
                    <span class="step-number">4</span>
                    <span>Equipe & Estrutura</span>
                </div>
                <div class="step-indicator" data-step-indicator="5">
                    <span class="step-number">5</span>
                    <span>Expectativas & Gelo</span>
                </div>
            </div>
        </div>

        <div class="card-body">
            <form id="pilot-form" class="needs-validation soda-form" novalidate>
                <input type="hidden" name="action" value="soda_perfeita_perfil_submit">
                <input type="hidden" name="nonce" value="<?php echo esc_attr(wp_create_nonce('soda_perfil_nonce')); ?>">
                
                <!-- STEP 1 -->
                <div class="form-step active" data-step="1">
                    <h2 class="h5 mb-3">1. Sobre o seu negócio</h2>

                    <div class="mb-3">
                        <label class="question-title" for="nome_negocio">
                            Qual o nome do seu negócio?<span class="required-badge">*</span>
                        </label>
                        <input type="text" class="form-control" id="nome_negocio" name="nome_negocio" required>
                    </div>

                    <div class="mb-3">
                        <label class="question-title" for="cidade_estado">
                            Qual a cidade e estado do seu empreendimento?<span class="required-badge">*</span>
                        </label>
                        <input type="text" class="form-control" id="cidade_estado" name="cidade_estado"
                               placeholder="Ex.: São Paulo – SP" required>
                    </div>

                    <div class="mb-3">
                        <div class="question-title">
                            Qual o tipo de negócio?<span class="required-badge">*</span>
                        </div>
                        <p class="question-helper mb-2">
                            Marque as opções que melhor representam o seu estabelecimento.
                        </p>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="tipo_bar_baladas" name="tipo_negocio[]" value="Bar - baladas">
                                    <label class="form-check-label" for="tipo_bar_baladas">Bar - baladas</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="tipo_bar_sinuca" name="tipo_negocio[]" value="Bar com sinuca">
                                    <label class="form-check-label" for="tipo_bar_sinuca">Bar com sinuca</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="tipo_bar_volume" name="tipo_negocio[]" value="Bar de alto volume">
                                    <label class="form-check-label" for="tipo_bar_volume">Bar de alto volume</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="tipo_bar_coquetelaria" name="tipo_negocio[]" value="Bar de coquetelaria">
                                    <label class="form-check-label" for="tipo_bar_coquetelaria">Bar de coquetelaria</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="tipo_restaurante_popular" name="tipo_negocio[]" value="Restaurante popular/almoço">
                                    <label class="form-check-label" for="tipo_restaurante_popular">Restaurante popular / almoço</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="tipo_restaurante_executivo" name="tipo_negocio[]" value="Restaurante executivo">
                                    <label class="form-check-label" for="tipo_restaurante_executivo">Restaurante executivo</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="tipo_cafeteria" name="tipo_negocio[]" value="Cafeteria">
                                    <label class="form-check-label" for="tipo_cafeteria">Cafeteria</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="tipo_quiosque" name="tipo_negocio[]" value="Quiosque">
                                    <label class="form-check-label" for="tipo_quiosque">Quiosque / quiosque de praia</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="tipo_clube" name="tipo_negocio[]" value="Clube">
                                    <label class="form-check-label" for="tipo_clube">Clube (golfe, tênis, etc.)</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="tipo_hotel" name="tipo_negocio[]" value="Hotel / pousada">
                                    <label class="form-check-label" for="tipo_hotel">Hotel / pousada</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="tipo_eventos" name="tipo_negocio[]" value="Eventos e festas">
                                    <label class="form-check-label" for="tipo_eventos">Eventos, casas de festas, buffets</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="tipo_loja_bebidas" name="tipo_negocio[]" value="Loja de bebidas">
                                    <label class="form-check-label" for="tipo_loja_bebidas">Loja ou adega de bebidas</label>
                                </div>
                                <div class="form-check d-flex align-items-center gap-2 mt-2">
                                    <input class="form-check-input" type="checkbox" id="tipo_outro" name="tipo_negocio[]" value="Outro">
                                    <label class="form-check-label" for="tipo_outro">Outro:</label>
                                    <input type="text" class="form-control form-control-sm" name="tipo_negocio_outro" placeholder="Descreva">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="question-title" for="segmento_clientes">
                            Segmento de clientes: quem são os principais clientes que frequentam o seu bar? Qual perfil?
                        </label>
                        <textarea class="form-control" id="segmento_clientes" name="segmento_clientes" rows="3"
                                  placeholder="Ex.: jovens universitários, casais, famílias, público corporativo, turistas..."></textarea>
                    </div>
                </div>

                <!-- STEP 2 -->
                <div class="form-step" data-step="2">
                    <h2 class="h5 mb-3">2. Operação e atendimento</h2>

                    <div class="mb-3">
                        <label class="question-title" for="atendimento_turnos">
                            Atendimento em quais turnos?<span class="required-badge">*</span>
                        </label>
                        <p class="question-helper mb-2">
                            Exemplos: abre à tarde mas pico é à noite, finais de semana, almoço executivo, brunch, etc.
                        </p>
                        <textarea class="form-control" id="atendimento_turnos" name="atendimento_turnos" rows="3" required></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="question-title" for="principal_diferencial">
                            O que considera ser o principal diferencial do seu estabelecimento?
                        </label>
                        <textarea class="form-control" id="principal_diferencial" name="principal_diferencial" rows="3"></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="question-title" for="experiencia_bebidas">
                            Descreva, em linhas gerais, como é hoje a experiência de bebidas no seu negócio.
                        </label>
                        <p class="question-helper mb-2">
                            Padrão de serviço, apresentação dos drinks, tempo de espera, sugestões do time de bar, etc.
                        </p>
                        <textarea class="form-control" id="experiencia_bebidas" name="experiencia_bebidas" rows="3"></textarea>
                    </div>

                    <div class="mb-3">
                        <div class="question-title">
                            Trabalha com delivery atualmente?<span class="required-badge">*</span>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="delivery" id="delivery_sim_coquetel" value="Sim, inclusive coquetéis" required>
                            <label class="form-check-label" for="delivery_sim_coquetel">
                                Sim, inclusive coquetéis / bebidas especiais
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="delivery" id="delivery_sim_somente_comida" value="Sim, mas somente comida ou outros produtos">
                            <label class="form-check-label" for="delivery_sim_somente_comida">
                                Sim, mas somente comida ou outros produtos
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="delivery" id="delivery_nao" value="Não trabalha com delivery">
                            <label class="form-check-label" for="delivery_nao">
                                Não trabalha com delivery
                            </label>
                        </div>
                    </div>
                </div>

                <!-- STEP 3 -->
                <div class="form-step" data-step="3">
                    <h2 class="h5 mb-3">3. Mix de bebidas e coquetéis</h2>

                    <div class="mb-3">
                        <label class="question-title" for="bebidas_mais_vendidas">
                            Quais são as bebidas mais vendidas em sua operação?
                        </label>
                        <p class="question-helper mb-2">
                            Liste, se possível, até 10 itens (cerveja, chope, caipirinhas, drinks autorais,
                            long drinks, shots, refrigerantes, água, etc).
                        </p>
                        <textarea class="form-control" id="bebidas_mais_vendidas" name="bebidas_mais_vendidas" rows="4"></textarea>
                    </div>

                    <div class="mb-3">
                        <div class="question-title">
                            Quais tipos de coquetéis quer oferecer ou fortalecer na operação?
                        </div>
                        <p class="question-helper mb-2">
                            Marque tudo o que fizer sentido hoje ou como objetivo.
                        </p>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="coquetel_classicos_iba" name="coqueteis[]" value="Clássicos IBA">
                                    <label class="form-check-label" for="coquetel_classicos_iba">Clássicos IBA</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="coquetel_autorais" name="coqueteis[]" value="Autorais da casa">
                                    <label class="form-check-label" for="coquetel_autorais">Drinks autorais da casa</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="coquetel_sem_alcool" name="coqueteis[]" value="Sem álcool / mocktails">
                                    <label class="form-check-label" for="coquetel_sem_alcool">Mocktails / sem álcool</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="coquetel_shots" name="coqueteis[]" value="Shots e rituais">
                                    <label class="form-check-label" for="coquetel_shots">Shots e rituais</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="coquetel_highball" name="coqueteis[]" value="Highballs / bebidas gaseificadas">
                                    <label class="form-check-label" for="coquetel_highball">Highballs / bebidas gaseificadas</label>
                                </div>
                                <div class="form-check d-flex align-items-center gap-2 mt-2">
                                    <input class="form-check-input" type="checkbox" id="coquetel_outros" name="coqueteis[]" value="Outros">
                                    <label class="form-check-label" for="coquetel_outros">Outros:</label>
                                    <input type="text" class="form-control form-control-sm" name="coqueteis_outros" placeholder="Descreva">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="question-title" for="ticket_medio_drinks">
                            Qual o ticket médio aproximado dos drinks/coquetéis hoje?
                        </label>
                        <div class="input-group">
                            <span class="input-group-text">R$</span>
                            <input type="number" class="form-control" id="ticket_medio_drinks" name="ticket_medio_drinks" min="0" step="0.01">
                        </div>
                    </div>
                </div>

                <!-- STEP 4 -->
                <div class="form-step" data-step="4">
                    <h2 class="h5"> Mao de obra e estrutura de bar</h2>

                    <div class="mb-3">
                        <div class="question-title">
                            Mao de obra: como você classifica o time de bar hoje?<span class="required-badge">*</span>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="mao_obra" id="mao_obra_fraca" value="Necessita bastante treinamento" required>
                            <label class="form-check-label" for="mao_obra_fraca">
                                Time ainda fraco, necessita bastante treinamento e suporte.
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="mao_obra" id="mao_obra_media" value="Boa, mas falta padrão">
                            <label class="form-check-label" for="mao_obra_media">
                                Boa, mas falta padronização e consistência nos drinks.
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="mao_obra" id="mao_obra_excellente" value="Excelente e padronizada">
                            <label class="form-check-label" for="mao_obra_excellente">
                                Excelente e padronizada – foco maior em novos produtos.
                            </label>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="question-title" for="mao_obra_comentarios">
                            Se quiser, descreva como imagina que a Soda Perfeita pode ajudar na capacitação/rotina do bar.
                        </label>
                        <textarea class="form-control" id="mao_obra_comentarios" name="mao_obra_comentarios" rows="3"></textarea>
                    </div>

                    

                    <div class="mb-3">
                        <div class="question-title">
                            Existe alguma frequência / evento que queira aprimorar com o plano piloto?
                        </div>
                        <p class="question-helper mb-2">
                            Marque o que fizer sentido (opcional).
                        </p>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="freq_luau" name="frequencia_eventos[]" value="Eventos temáticos (luau, noites especiais, etc.)">
                            <label class="form-check-label" for="freq_luau">
                                Eventos temáticos (luau, noites especiais, harmonizações, etc.)
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="freq_corporativo" name="frequencia_eventos[]" value="Atendimento a eventos corporativos">
                            <label class="form-check-label" for="freq_corporativo">
                                Eventos e reservas corporativas
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="freq_happyhour" name="frequencia_eventos[]" value="Happy hour">
                            <label class="form-check-label" for="freq_happyhour">
                                Happy hour / pós-expediente
                            </label>
                        </div>
                        <div class="form-check d-flex align-items-center gap-2 mt-2">
                            <input class="form-check-input" type="checkbox" id="freq_outros" name="frequencia_eventos[]" value="Outros">
                            <label class="form-check-label" for="freq_outros">Outros:</label>
                            <input type="text" class="form-control form-control-sm" name="frequencia_eventos_outros" placeholder="Descreva">
                        </div>
                    </div>
                </div>

                <!-- STEP 5 -->
                <div class="form-step" data-step="5">
                    <h2 class="h5 mb-3">5. Expectativas com a Soda Perfeita e consumo de gelo</h2>

                    <div class="mb-3">
                        <label class="question-title" for="expectativas">
                            Quais são suas expectativas com a contratação do sistema Soda Perfeita?
                        </label>
                        <p class="question-helper mb-2">
                            Como acredita que podemos agregar valor à sua operação? O que seria sucesso para você nesse projeto?
                        </p>
                        <textarea class="form-control" id="expectativas" name="expectativas" rows="4"></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="question-title" for="satisfacao_geral">
                            Em uma escala de 1 a 5, qual o nível de Satisfacao atual com a operação de bebidas?
                        </label>
                        <select class="form-select" id="satisfacao_geral" name="satisfacao_geral">
                            <option value="">Selecione...</option>
                            <option value="1">1 - Muito insatisfeito(a)</option>
                            <option value="2">2 - Insatisfeito(a)</option>
                            <option value="3">3 - Neutro(a)</option>
                            <option value="4">4 - Satisfeito(a)</option>
                            <option value="5">5 - Muito satisfeito(a)</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <div class="question-title">
                            Atualmente possui máquina de gelo? Se sim, está satisfeito?
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="maquina_gelo" id="maquina_gelo_sim_satisfeito" value="Sim, satisfeito(a)">
                            <label class="form-check-label" for="maquina_gelo_sim_satisfeito">
                                Sim, e estou satisfeito(a) com a capacidade/resultado.
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="maquina_gelo" id="maquina_gelo_sim_insatisfeito" value="Sim, mas não supre bem a demanda">
                            <label class="form-check-label" for="maquina_gelo_sim_insatisfeito">
                                Sim, mas não supre bem a demanda.
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="maquina_gelo" id="maquina_gelo_nao" value="Não possuo máquina de gelo">
                            <label class="form-check-label" for="maquina_gelo_nao">
                                Não possuo máquina de gelo.
                            </label>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="question-title" for="consumo_semanal_gelo">
                            Se não, qual o consumo semanal de gelo aproximado?
                        </label>
                        <input type="text" class="form-control" id="consumo_semanal_gelo" name="consumo_semanal_gelo"
                               placeholder="Ex.: 40 sacos de 10kg por semana">
                    </div>

                    <div class="mb-3">
                        <label class="question-title" for="valor_gasto_gelo">
                            E qual o valor aproximado gasto com gelo por semana ou mês?
                        </label>
                        <input type="text" class="form-control" id="valor_gasto_gelo" name="valor_gasto_gelo"
                               placeholder="Ex.: R$ 2.500/mês">
                    </div>
<div class="mb-3">
                        <div class="question-title">
                            Compra de gelo e limite operacional de estocagem
                        </div>
                        <div class="question-helper mb-2">
                            Escolha a opção que melhor representa sua realidade atual.
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="gelo_operacao" id="gelo_sacado_pequeno" value="Somente gelo sacado, pouco espaço em estoque">
                            <label class="form-check-label" for="gelo_sacado_pequeno">
                                Uso apenas gelo sacado, com pouco espaço de armazenamento.
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="gelo_operacao" id="gelo_sacado_grande" value="Consumo alto de gelo sacado">
                            <label class="form-check-label" for="gelo_sacado_grande">
                                Consumo alto de gelo sacado (custo relevante na operação).
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="gelo_operacao" id="gelo_maquina" value="Possui máquina de gelo própria">
                            <label class="form-check-label" for="gelo_maquina">
                                Possui máquina de gelo própria.
                            </label>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="question-title" for="observacoes_finais">
                            Observacoes finais que considere importantes:
                        </label>
                        <textarea class="form-control" id="observacoes_finais" name="observacoes_finais" rows="3"></textarea>
                    </div>
                </div>

                <!-- navegação -->
                <div class="d-flex justify-content-between pt-3 border-top mt-3">
                    <button type="button" class="btn btn-soda-outline" id="prev-btn" disabled>Voltar</button>
                    <button type="button" class="btn btn-soda-primary" id="next-btn">
                        Próximo
                    </button>
                    <button type="submit" class="btn btn-soda-primary d-none" id="submit-btn">
                        Enviar formulário
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>

<script>
    (function () {
        const form = document.getElementById('pilot-form');
        const steps = Array.from(document.querySelectorAll('.form-step'));
        const indicators = Array.from(document.querySelectorAll('.step-indicator'));
        const progressBar = document.querySelector('.progress-bar');
        const prevBtn = document.getElementById('prev-btn');
        const nextBtn = document.getElementById('next-btn');
        const submitBtn = document.getElementById('submit-btn');

        let currentStep = 0;

        function updateUI() {
            steps.forEach((step, index) => {
                step.classList.toggle('active', index === currentStep);
            });

            indicators.forEach((ind, index) => {
                ind.classList.remove('active', 'done');
                if (index < currentStep) {
                    ind.classList.add('done');
                } else if (index === currentStep) {
                    ind.classList.add('active');
                }
            });

            const progress = ((currentStep) / (steps.length - 1)) * 100;
            progressBar.style.width = progress + '%';
            progressBar.setAttribute('aria-valuenow', progress.toString());

            prevBtn.disabled = currentStep === 0;

            if (currentStep === steps.length - 1) {
                nextBtn.classList.add('d-none');
                submitBtn.classList.remove('d-none');
            } else {
                nextBtn.classList.remove('d-none');
                submitBtn.classList.add('d-none');
            }
        }

        function validateCurrentStep() {
            const currentFields = steps[currentStep].querySelectorAll('input, select, textarea');
            for (const field of currentFields) {
                if (!field.checkValidity()) {
                    field.reportValidity();
                    return false;
                }
            }
            return true;
        }

        nextBtn.addEventListener('click', () => {
            if (!validateCurrentStep()) return;
            if (currentStep < steps.length - 1) {
                currentStep++;
                updateUI();
            }
        });

        prevBtn.addEventListener('click', () => {
            if (currentStep > 0) {
                currentStep--;
                updateUI();
            }
        });

        form.addEventListener('submit', (e) => {
            if (!validateCurrentStep()) {
                e.preventDefault();
            } else {
                // aqui depois você liga com o processamento (WordPress, PHP, etc.)
                // por enquanto deixa submit normal.
            }
        });

        updateUI();
    })();
</script>


        <?php
        return ob_get_clean();
    }

    public function render_list() {
        ob_start();
        ?>
        <style>
            /* Ajuste de alinhamento da DataTable */
            #soda-perfil-table_wrapper .row { margin-bottom: 0.5rem; }
            /* Largura total no desktop */
            .soda-perfis-wrap { max-width: 100% !important; }
            .soda-perfis-wrap .card,
            .soda-perfis-wrap .card-body { width: 100% !important; }
            #soda-perfil-table_wrapper,
            #soda-perfil-table { width: 100% !important; }
            .soda-perfis-wrap .table-responsive { width: 100%; overflow-x: auto; }
            @media print {
                #btnToggleEdit, #btnPrintPerfil, .btn-edit-perfil { display: none !important; }
            }
        </style>
        <div class="card soda-perfis-wrap">
            <div class="card-body soda-perfis-wrap">
                <div class="table-responsive soda-perfis-wrap">
                    <table id="soda-perfil-table" class="table table-striped table-bordered align-middle w-100">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Data</th>
                            <th>Negocio</th>
                            <th>Cidade/Estado</th>
                            <th>Tipo de negocio</th>
                            <th>Segmento clientes</th>
                            <th>Atendimento/Turnos</th>
                            <th>Principal diferencial</th>
                            <th>Experiencia bebidas</th>
                            <th>Delivery</th>
                            <th>Coqueteis</th>
                            <th>Ticket medio drinks</th>
                            <th>Mao de obra</th>
                            <th>Comentarios mao de obra</th>
                            <th>Gelo operacao</th>
                            <th>Frequencia eventos</th>
                            <th>Expectativas</th>
                            <th>Satisfacao</th>
                            <th>Maquina gelo</th>
                            <th>Consumo semanal gelo</th>
                            <th>Valor gasto gelo</th>
                            <th>Observacoes finais</th>
                            <th>Acoes</th>
                        </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Modal de visualização/edição -->
        <div class="modal fade" id="perfilModal" tabindex="-1" aria-labelledby="perfilModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="perfilModalLabel">Perfil</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="d-flex justify-content-end mb-3">
                            <button type="button" class="btn btn-secondary btn-sm me-2" id="btnPrintPerfil">Imprimir</button>
                            <button type="button" class="btn btn-primary btn-sm" id="btnToggleEdit" aria-label="Editar">Editar</button>
                        </div>
                        <div id="perfilView">
                            <dl class="row mb-0" id="perfilViewContent"></dl>
                        </div>
                        <form id="perfilEditForm" class="d-none mt-3">
                            <input type="hidden" name="id" id="perfilEditId">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Nome do negocio</label>
                                    <input type="text" class="form-control" name="nome_negocio" id="edit_nome_negocio" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Cidade/Estado</label>
                                    <input type="text" class="form-control" name="cidade_estado" id="edit_cidade_estado" required>
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label">Atendimento / turnos</label>
                                    <textarea class="form-control" name="atendimento_turnos" id="edit_atendimento_turnos" rows="2" required></textarea>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Delivery</label>
                                    <input type="text" class="form-control" name="delivery" id="edit_delivery">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Mao de obra</label>
                                    <input type="text" class="form-control" name="mao_obra" id="edit_mao_obra" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Satisfacao (1-5)</label>
                                    <input type="number" min="1" max="5" class="form-control" name="satisfacao_geral" id="edit_satisfacao_geral">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Consumo semanal de gelo</label>
                                    <input type="text" class="form-control" name="consumo_semanal_gelo" id="edit_consumo_semanal_gelo">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Valor gasto com gelo</label>
                                    <input type="text" class="form-control" name="valor_gasto_gelo" id="edit_valor_gasto_gelo">
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label">Expectativas</label>
                                    <textarea class="form-control" name="expectativas" id="edit_expectativas" rows="2"></textarea>
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label">Observacoes finais</label>
                                    <textarea class="form-control" name="observacoes_finais" id="edit_observacoes_finais" rows="2"></textarea>
                                </div>
                            </div>
                            <div class="mt-3 d-flex justify-content-end">
                                <button type="button" class="btn btn-light me-2" id="btnCancelEdit">Cancelar</button>
                                <button type="submit" class="btn btn-success">Salvar alteracoes</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
}

new Soda_Perfeita_Perfil();



