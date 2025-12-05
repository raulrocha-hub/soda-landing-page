<?php
/**
 * Plugin Name: Soda Cashback Wallet
 * Description: Carteira de cashback com visão no admin e no painel do cliente WooCommerce.
 * Author: Raul / Soda
 * Version: 0.1.0
 * Text Domain: soda-cashback-wallet
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Soda_Cashback_Wallet {

    /** @var Soda_Cashback_Wallet */
    private static $instance;

    /** @var string */
    private $wallet_table;

    /** @var string */
    private $transactions_table;

    /** @var string */
    private $endpoint = 'cashback-wallet';

    /**
     * Singleton
     */
    public static function instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        global $wpdb;

        $this->wallet_table       = $wpdb->prefix . 'soda_cashback_wallets';
        $this->transactions_table = $wpdb->prefix . 'soda_cashback_transactions';

        // Hooks principais
        add_action( 'admin_menu', [ $this, 'register_admin_menu' ] );

        // Endpoint "Minha Conta" WooCommerce
        add_action( 'init', [ $this, 'add_wc_endpoint' ] );
        add_filter( 'woocommerce_account_menu_items', [ $this, 'add_wc_account_link' ] );
        add_action( 'woocommerce_account_' . $this->endpoint . '_endpoint', [ $this, 'render_wc_wallet_page' ] );

        // Ajax para extrato no admin (modal)
        add_action( 'wp_ajax_soda_cashback_get_statement', [ $this, 'ajax_get_statement' ] );
        // (somente admin, não precisamos de wp_ajax_nopriv aqui)

        // Assets admin
        add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_admin_assets' ] );
    }

    /**
     * Criação das tabelas na ativação
     */
    public static function activate() {
        global $wpdb;

        $charset_collate = $wpdb->get_charset_collate();

        $wallet_table       = $wpdb->prefix . 'soda_cashback_wallets';
        $transactions_table = $wpdb->prefix . 'soda_cashback_transactions';

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';

        // Tabela de carteiras
        $sql_wallet = "CREATE TABLE {$wallet_table} (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            user_id BIGINT(20) UNSIGNED NOT NULL,
            balance DECIMAL(10,2) NOT NULL DEFAULT 0.00,
            frozen TINYINT(1) NOT NULL DEFAULT 0,
            updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY user_wallet (user_id),
            KEY user_id (user_id)
        ) {$charset_collate};";

        // Tabela de transações
        $sql_transactions = "CREATE TABLE {$transactions_table} (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            user_id BIGINT(20) UNSIGNED NOT NULL,
            type VARCHAR(20) NOT NULL,
            amount DECIMAL(10,2) NOT NULL,
            description VARCHAR(255) NULL,
            order_id BIGINT(20) UNSIGNED NULL,
            campaign_id BIGINT(20) UNSIGNED NULL,
            status VARCHAR(20) NOT NULL DEFAULT 'active',
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            expires_at DATETIME NULL,
            PRIMARY KEY (id),
            KEY user_id (user_id),
            KEY order_id (order_id),
            KEY campaign_id (campaign_id)
        ) {$charset_collate};";

        dbDelta( $sql_wallet );
        dbDelta( $sql_transactions );
    }

    /**
     * Admin menu: página de carteiras
     */
    public function register_admin_menu() {

        // Se você quiser como submenu de "Config Cashback", altere o parent_slug
        // Para agora, vou colocar como menu próprio "Cashback Wallet"
        add_menu_page(
            __( 'Carteiras de Cashback', 'soda-cashback-wallet' ),
            __( 'Carteiras Cashback', 'soda-cashback-wallet' ),
            'manage_woocommerce',
            'soda-cashback-wallet',
            [ $this, 'render_admin_wallet_list' ],
            'dashicons-tickets-alt',
            56
        );
    }

    /**
     * Lista de carteiras no admin
     */
    public function render_admin_wallet_list() {
        if ( ! current_user_can( 'manage_woocommerce' ) ) {
            wp_die( __( 'Sem permissão.', 'soda-cashback-wallet' ) );
        }

        global $wpdb;

        // Busca clientes (role customer) + saldo da carteira
        $wallet_table = $this->wallet_table;

        // Simples: pega todos users com role customer e faz LEFT JOIN com wallet
        $args = [
            'role__in' => [ 'customer' ], // aqui você adapta para o role "bar" se for custom
            'number'   => 200,            // depois podemos paginar
            'paged'    => 1,
        ];

        $users = get_users( $args );

        ?>
        <div class="wrap">
            <h1><?php esc_html_e( 'Carteiras de Cashback', 'soda-cashback-wallet' ); ?></h1>

            <table class="widefat fixed striped">
                <thead>
                    <tr>
                        <th><?php esc_html_e( 'Cliente', 'soda-cashback-wallet' ); ?></th>
                        <th><?php esc_html_e( 'E-mail', 'soda-cashback-wallet' ); ?></th>
                        <th><?php esc_html_e( 'Saldo', 'soda-cashback-wallet' ); ?></th>
                        <th><?php esc_html_e( 'Status', 'soda-cashback-wallet' ); ?></th>
                        <th><?php esc_html_e( 'Ações', 'soda-cashback-wallet' ); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ( ! empty( $users ) ) : ?>
                        <?php foreach ( $users as $user ) : 
                            $user_id = $user->ID;

                            // Pega carteira do usuário
                            $wallet = $wpdb->get_row(
                                $wpdb->prepare(
                                    "SELECT * FROM {$wallet_table} WHERE user_id = %d",
                                    $user_id
                                )
                            );

                            $balance = $wallet ? floatval( $wallet->balance ) : 0;
                            $frozen  = $wallet ? intval( $wallet->frozen ) : 0;
                            ?>
                            <tr>
                                <td><?php echo esc_html( $user->display_name ); ?></td>
                                <td><?php echo esc_html( $user->user_email ); ?></td>
                                <td><?php echo wc_price( $balance ); ?></td>
                                <td>
                                    <?php 
                                    if ( $frozen ) {
                                        esc_html_e( 'Congelada', 'soda-cashback-wallet' );
                                    } else {
                                        esc_html_e( 'Ativa', 'soda-cashback-wallet' );
                                    }
                                    ?>
                                </td>
                                <td>
                                    <button 
                                        class="button button-primary soda-cashback-view-statement" 
                                        data-user-id="<?php echo esc_attr( $user_id ); ?>">
                                        <?php esc_html_e( 'Ver extrato', 'soda-cashback-wallet' ); ?>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr>
                            <td colspan="5"><?php esc_html_e( 'Nenhum cliente encontrado.', 'soda-cashback-wallet' ); ?></td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Modal simples para extrato -->
        <div id="soda-cashback-modal" style="display:none;">
            <div class="soda-cashback-modal-backdrop"></div>
            <div class="soda-cashback-modal-content">
                <button class="soda-cashback-modal-close">&times;</button>
                <div id="soda-cashback-modal-body">
                    <!-- extrato vem via AJAX -->
                </div>
            </div>
        </div>
        <?php
    }

    /**
     * Assets admin para o modal / ajax
     */
    public function enqueue_admin_assets( $hook ) {
        // Carrega somente na página do plugin
        if ( $hook !== 'toplevel_page_soda-cashback-wallet' ) {
            return;
        }

        wp_enqueue_style(
            'soda-cashback-admin',
            plugin_dir_url( __FILE__ ) . 'assets/admin.css',
            [],
            '0.1.0'
        );

        wp_enqueue_script(
            'soda-cashback-admin',
            plugin_dir_url( __FILE__ ) . 'assets/admin.js',
            [ 'jquery' ],
            '0.1.0',
            true
        );

        wp_localize_script(
            'soda-cashback-admin',
            'SodaCashbackWallet',
            [
                'ajax_url' => admin_url( 'admin-ajax.php' ),
                'nonce'    => wp_create_nonce( 'soda_cashback_wallet_nonce' ),
            ]
        );
    }

    /**
     * AJAX: retorna extrato (admin)
     */
    public function ajax_get_statement() {
        check_ajax_referer( 'soda_cashback_wallet_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_woocommerce' ) ) {
            wp_send_json_error( [ 'message' => __( 'Sem permissão.', 'soda-cashback-wallet' ) ] );
        }

        $user_id = isset( $_POST['user_id'] ) ? absint( $_POST['user_id'] ) : 0;
        if ( ! $user_id ) {
            wp_send_json_error( [ 'message' => __( 'Usuário inválido.', 'soda-cashback-wallet' ) ] );
        }

        global $wpdb;
        $transactions_table = $this->transactions_table;

        $transactions = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM {$transactions_table} WHERE user_id = %d ORDER BY created_at DESC LIMIT 100",
                $user_id
            )
        );

        ob_start();
        ?>
        <h2><?php printf( esc_html__( 'Extrato do cliente #%d', 'soda-cashback-wallet' ), $user_id ); ?></h2>
        <table class="widefat fixed striped">
            <thead>
                <tr>
                    <th><?php esc_html_e( 'Data', 'soda-cashback-wallet' ); ?></th>
                    <th><?php esc_html_e( 'Tipo', 'soda-cashback-wallet' ); ?></th>
                    <th><?php esc_html_e( 'Descrição', 'soda-cashback-wallet' ); ?></th>
                    <th><?php esc_html_e( 'Valor', 'soda-cashback-wallet' ); ?></th>
                    <th><?php esc_html_e( 'Status', 'soda-cashback-wallet' ); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php if ( ! empty( $transactions ) ) : ?>
                    <?php foreach ( $transactions as $t ) : ?>
                        <tr>
                            <td><?php echo esc_html( $t->created_at ); ?></td>
                            <td><?php echo esc_html( $t->type ); ?></td>
                            <td><?php echo esc_html( $t->description ); ?></td>
                            <td><?php echo wc_price( $t->amount ); ?></td>
                            <td><?php echo esc_html( $t->status ); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else : ?>
                    <tr>
                        <td colspan="5"><?php esc_html_e( 'Nenhuma transação encontrada.', 'soda-cashback-wallet' ); ?></td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        <?php

        $html = ob_get_clean();

        wp_send_json_success( [ 'html' => $html ] );
    }

    /**
     * Endpoint WooCommerce "Minha Conta"
     */
    public function add_wc_endpoint() {
        add_rewrite_endpoint( $this->endpoint, EP_ROOT | EP_PAGES );
    }

    /**
     * Adiciona item no menu "Minha Conta" do Woo
     */
    public function add_wc_account_link( $items ) {
        // Você pode ajustar a posição conforme preferir
        $new_items = [];

        foreach ( $items as $key => $label ) {
            $new_items[ $key ] = $label;

            if ( 'edit-account' === $key ) {
                $new_items[ $this->endpoint ] = __( 'Minha Carteira', 'soda-cashback-wallet' );
            }
        }

        return $new_items;
    }

    /**
     * Render da página "Minha Carteira" no front (cliente)
     */
    public function render_wc_wallet_page() {
        if ( ! is_user_logged_in() ) {
            echo '<p>' . esc_html__( 'Você precisa estar logado para ver sua carteira.', 'soda-cashback-wallet' ) . '</p>';
            return;
        }

        $user_id = get_current_user_id();

        global $wpdb;
        $wallet_table       = $this->wallet_table;
        $transactions_table = $this->transactions_table;

        $wallet = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM {$wallet_table} WHERE user_id = %d",
                $user_id
            )
        );

        $balance = $wallet ? floatval( $wallet->balance ) : 0;
        $frozen  = $wallet ? intval( $wallet->frozen ) : 0;

        $transactions = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM {$transactions_table} WHERE user_id = %d ORDER BY created_at DESC LIMIT 50",
                $user_id
            )
        );

        ?>
        <h2><?php esc_html_e( 'Minha Carteira de Cashback', 'soda-cashback-wallet' ); ?></h2>

        <p>
            <strong><?php esc_html_e( 'Saldo disponível:', 'soda-cashback-wallet' ); ?></strong>
            <?php echo wc_price( $balance ); ?>
        </p>

        <?php if ( $frozen ) : ?>
            <p style="color:#cc0000;">
                <?php esc_html_e( 'Sua carteira está congelada devido a pendências na assinatura. Regularize para voltar a usar o saldo.', 'soda-cashback-wallet' ); ?>
            </p>
        <?php endif; ?>

        <h3><?php esc_html_e( 'Últimas transações', 'soda-cashback-wallet' ); ?></h3>

        <table class="shop_table shop_table_responsive my_account_cashback">
            <thead>
                <tr>
                    <th><?php esc_html_e( 'Data', 'soda-cashback-wallet' ); ?></th>
                    <th><?php esc_html_e( 'Tipo', 'soda-cashback-wallet' ); ?></th>
                    <th><?php esc_html_e( 'Descrição', 'soda-cashback-wallet' ); ?></th>
                    <th><?php esc_html_e( 'Valor', 'soda-cashback-wallet' ); ?></th>
                    <th><?php esc_html_e( 'Status', 'soda-cashback-wallet' ); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php if ( ! empty( $transactions ) ) : ?>
                    <?php foreach ( $transactions as $t ) : ?>
                        <tr>
                            <td><?php echo esc_html( wc_format_datetime( wc_string_to_datetime( $t->created_at ) ) ); ?></td>
                            <td><?php echo esc_html( $t->type ); ?></td>
                            <td><?php echo esc_html( $t->description ); ?></td>
                            <td><?php echo wc_price( $t->amount ); ?></td>
                            <td><?php echo esc_html( $t->status ); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else : ?>
                    <tr>
                        <td colspan="5"><?php esc_html_e( 'Nenhuma transação ainda.', 'soda-cashback-wallet' ); ?></td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        <?php
    }
    //inicio dos novos scripts
        /**
     * Helper para buscar configurações de cashback (ACF options).
     */
    public function get_config( $key, $default = null ) {
        // Se você usa ACF Options
        if ( function_exists( 'get_field' ) ) {
            $value = get_field( $key, 'option' );
            if ( $value !== null && $value !== '' ) {
                return $value;
            }
        }

        return $default;
    }
    /**
     * Retorna saldo e status da carteira do usuário.
     *
     * @param int $user_id
     * @return array [ 'balance' => float, 'frozen' => bool ]
     */
    public function get_cashback_balance( $user_id ) {
        global $wpdb;

        $wallet = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM {$this->wallet_table} WHERE user_id = %d",
                $user_id
            )
        );

        if ( ! $wallet ) {
            // Se não existir, cria zerada
            $wpdb->insert(
                $this->wallet_table,
                [
                    'user_id' => $user_id,
                    'balance' => 0,
                    'frozen'  => 0,
                ],
                [ '%d', '%f', '%d' ]
            );

            return [
                'balance' => 0.0,
                'frozen'  => false,
            ];
        }

        return [
            'balance' => floatval( $wallet->balance ),
            'frozen'  => (bool) $wallet->frozen,
        ];
    }
    /**
     * Registra uma transação de cashback e atualiza o saldo.
     *
     * @param int    $user_id
     * @param string $type        credit | debit | expire | adjust
     * @param float  $amount      sempre valor positivo
     * @param string $description
     * @param array  $extras      [ 'order_id' => int, 'campaign_id' => int, 'status' => string, 'expires_at' => string ]
     *
     * @return int|false ID da transação ou false em erro
     */
    public function add_cashback_transaction( $user_id, $type, $amount, $description = '', $extras = [] ) {
        global $wpdb;

        $user_id = absint( $user_id );
        $amount  = floatval( $amount );
        $type    = sanitize_text_field( $type );
        $description = sanitize_text_field( $description );

        if ( $user_id <= 0 || $amount <= 0 ) {
            return false;
        }

        $data = [
            'user_id'     => $user_id,
            'type'        => $type,
            'amount'      => $amount,
            'description' => $description,
            'status'      => isset( $extras['status'] ) ? sanitize_text_field( $extras['status'] ) : 'active',
        ];

        if ( ! empty( $extras['order_id'] ) ) {
            $data['order_id'] = absint( $extras['order_id'] );
        }

        if ( ! empty( $extras['campaign_id'] ) ) {
            $data['campaign_id'] = absint( $extras['campaign_id'] );
        }

        if ( ! empty( $extras['expires_at'] ) ) {
            $data['expires_at'] = sanitize_text_field( $extras['expires_at'] );
        }

        $inserted = $wpdb->insert(
            $this->transactions_table,
            $data,
            [
                '%d',  // user_id
                '%s',  // type
                '%f',  // amount
                '%s',  // description
                '%s',  // status
                '%d',  // order_id
                '%d',  // campaign_id
                '%s',  // expires_at
            ]
        );

        if ( ! $inserted ) {
            return false;
        }

        // Atualiza saldo incrementalmente
        $this->update_wallet_balance_incremental( $user_id, $type, $amount );

        return $wpdb->insert_id;
    }

    /**
     * Atualiza o saldo da carteira de forma incremental.
     */
    protected function update_wallet_balance_incremental( $user_id, $type, $amount ) {
        global $wpdb;

        $current = $this->get_cashback_balance( $user_id );
        $balance = $current['balance'];

        // credit aumenta, debit e expire diminuem
        if ( in_array( $type, [ 'credit', 'adjust_plus' ], true ) ) {
            $balance += $amount;
        } elseif ( in_array( $type, [ 'debit', 'expire', 'adjust_minus' ], true ) ) {
            $balance -= $amount;
        }

        if ( $balance < 0 ) {
            $balance = 0;
        }

        $wpdb->update(
            $this->wallet_table,
            [ 'balance' => $balance ],
            [ 'user_id' => $user_id ],
            [ '%f' ],
            [ '%d' ]
        );
    }
    /**
     * Recalcula o saldo da carteira a partir do histórico de transações.
     *
     * credits ativos - debits - expires
     */
    public function recalculate_cashback_wallet( $user_id ) {
        global $wpdb;

        $user_id = absint( $user_id );

        if ( $user_id <= 0 ) {
            return false;
        }

        // Créditos ativos
        $credits = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT SUM(amount) FROM {$this->transactions_table}
                 WHERE user_id = %d AND type = 'credit' AND status = 'active'",
                $user_id
            )
        );

        // Débitos (inclui expire)
        $debits = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT SUM(amount) FROM {$this->transactions_table}
                 WHERE user_id = %d AND type IN ('debit','expire')",
                $user_id
            )
        );

        $credits = $credits ? floatval( $credits ) : 0.0;
        $debits  = $debits ? floatval( $debits ) : 0.0;

        $balance = $credits - $debits;
        if ( $balance < 0 ) {
            $balance = 0;
        }

        global $wpdb;
        $wallet = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM {$this->wallet_table} WHERE user_id = %d",
                $user_id
            )
        );

        if ( $wallet ) {
            $wpdb->update(
                $this->wallet_table,
                [ 'balance' => $balance ],
                [ 'user_id' => $user_id ],
                [ '%f' ],
                [ '%d' ]
            );
        } else {
            $wpdb->insert(
                $this->wallet_table,
                [
                    'user_id' => $user_id,
                    'balance' => $balance,
                    'frozen'  => 0,
                ],
                [ '%d', '%f', '%d' ]
            );
        }

        return $balance;
    }
    /**
     * Aplica regra de inadimplência de assinatura na carteira.
     *
     * wipe  : zera saldo (e opcionalmente registra transação expire)
     * freeze: marca carteira como congelada
     */
    public function handle_subscription_inadimplencia( $user_id ) {
        global $wpdb;

        $user_id = absint( $user_id );
        if ( $user_id <= 0 ) {
            return;
        }

        $acao   = $this->get_config( 'cashback_assinatura_atrasada_acao', 'none' );
        $log    = (bool) $this->get_config( 'cashback_assinatura_registrar_transacao', true );

        if ( $acao === 'none' ) {
            return;
        }

        $wallet = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM {$this->wallet_table} WHERE user_id = %d",
                $user_id
            )
        );

        if ( ! $wallet ) {
            return;
        }

        $balance = floatval( $wallet->balance );

        if ( $acao === 'wipe' ) {

            if ( $balance > 0 && $log ) {
                // registra perda como expire
                $this->add_cashback_transaction(
                    $user_id,
                    'expire',
                    $balance,
                    __( 'Perda de cashback por assinatura vencida', 'soda-cashback-wallet' ),
                    [ 'status' => 'expired' ]
                );
            }

            // zera saldo e marca congelada
            $wpdb->update(
                $this->wallet_table,
                [
                    'balance' => 0,
                    'frozen'  => 1,
                ],
                [ 'user_id' => $user_id ],
                [ '%f', '%d' ],
                [ '%d' ]
            );

        } elseif ( $acao === 'freeze' ) {

            $wpdb->update(
                $this->wallet_table,
                [ 'frozen' => 1 ],
                [ 'user_id' => $user_id ],
                [ '%d' ],
                [ '%d' ]
            );
        }
    }

    //final da class
}
//fora da class
/**
 * Atalho global para pegar saldo de cashback.
 */
function get_cashback_balance( $user_id = null ) {
    if ( ! $user_id ) {
        $user_id = get_current_user_id();
    }
    return Soda_Cashback_Wallet::instance()->get_cashback_balance( $user_id );
}

/**
 * Atalho global para registrar transação.
 */
function add_cashback_transaction( $user_id, $type, $amount, $description = '', $extras = [] ) {
    return Soda_Cashback_Wallet::instance()->add_cashback_transaction( $user_id, $type, $amount, $description, $extras );
}

/**
 * Atalho global para recalcular carteira.
 */
function recalculate_cashback_wallet( $user_id ) {
    return Soda_Cashback_Wallet::instance()->recalculate_cashback_wallet( $user_id );
}

/**
 * Atalho global para regra de inadimplência.
 */
function handle_subscription_inadimplencia( $user_id ) {
    return Soda_Cashback_Wallet::instance()->handle_subscription_inadimplencia( $user_id );
}
//assinaturas modelo 1
// Só registra hooks se WooCommerce Subscriptions estiver ativo
if ( class_exists( 'WC_Subscription' ) && function_exists( 'wcs_get_subscriptions' ) ) {

    // Quando a assinatura mudar para status problemático, aplica inadimplência
    $bad_statuses = [ 'on-hold', 'cancelled', 'expired' ];

    foreach ( $bad_statuses as $status ) {
        add_action( 'woocommerce_subscription_status_' . $status, function ( $subscription ) {
            if ( ! $subscription instanceof WC_Subscription ) {
                return;
            }
            $user_id = $subscription->get_user_id();
            handle_subscription_inadimplencia( $user_id );
        } );
    }
}

// agenda cron na ativação
register_activation_hook( __FILE__, function () {
    if ( ! wp_next_scheduled( 'soda_cashback_daily_subscription_check' ) ) {
        wp_schedule_event( time() + HOUR_IN_SECONDS, 'daily', 'soda_cashback_daily_subscription_check' );
    }
});

// remove cron na desativação se quiser
register_deactivation_hook( __FILE__, function () {
    $timestamp = wp_next_scheduled( 'soda_cashback_daily_subscription_check' );
    if ( $timestamp ) {
        wp_unschedule_event( $timestamp, 'soda_cashback_daily_subscription_check' );
    }
});

add_action( 'soda_cashback_daily_subscription_check', function () {
    if ( ! class_exists( 'WC_Subscription' ) || ! function_exists( 'wcs_get_subscriptions' ) ) {
        return;
    }

    // Exemplo simples: pega assinaturas on-hold / expired / cancelled
    $subscriptions = wcs_get_subscriptions( [
        'subscription_status' => [ 'on-hold', 'expired', 'cancelled' ],
        'limit'               => -1,
    ] );

    if ( empty( $subscriptions ) ) {
        return;
    }

    foreach ( $subscriptions as $subscription ) {
        /** @var WC_Subscription $subscription */
        $user_id = $subscription->get_user_id();
        handle_subscription_inadimplencia( $user_id );
    }
});

/**
 * Gera cashback quando o pedido é concluído.
 */
add_action( 'woocommerce_order_status_completed', function ( $order_id ) {

    $order = wc_get_order( $order_id );
    if ( ! $order ) {
        return;
    }

    $user_id = $order->get_user_id();
    if ( ! $user_id ) {
        return;
    }

    // Exemplo de filtro para "tipo bar"
    // Ajuste conforme sua lógica (meta, role etc.)
    $user = get_user_by( 'id', $user_id );
    if ( ! in_array( 'customer', (array) $user->roles, true ) ) {
        return;
    }

    // Verifica se cashback está ativado
    $instance  = Soda_Cashback_Wallet::instance();
    $enabled   = $instance->get_config( 'cashback_enabled', 1 ); // ajuste o field
    if ( ! $enabled ) {
        return;
    }

    // Aqui você pode checar se assinatura está em dia (via plugin de assinatura)
    // Se não estiver, simplesmente return;

    // Valor do pedido (sem frete, sem taxa, ajuste como quiser)
    $subtotal = $order->get_subtotal();

    // Percentual global (ex.: 5)
    $percent = floatval( $instance->get_config( 'cashback_percentual_padrao', 0 ) );
    if ( $percent <= 0 ) {
        return;
    }

    $cashback_value = round( $subtotal * ( $percent / 100 ), 2 );
    if ( $cashback_value <= 0 ) {
        return;
    }

    // Prazo em meses para expirar
    $months_valid = intval( $instance->get_config( 'cashback_meses_validade_padrao', 3 ) );
    $expires_at   = null;
    if ( $months_valid > 0 ) {
        $expires_at = ( new DateTime( 'now', wp_timezone() ) )
            ->modify( '+' . $months_valid . ' months' )
            ->format( 'Y-m-d H:i:s' );
    }

    add_cashback_transaction(
        $user_id,
        'credit',
        $cashback_value,
        sprintf( __( 'Cashback do pedido #%d', 'soda-cashback-wallet' ), $order_id ),
        [
            'order_id'   => $order_id,
            'expires_at' => $expires_at,
            'status'     => 'active',
        ]
    );
}, 20 );

// Ativação
register_activation_hook( __FILE__, [ 'Soda_Cashback_Wallet', 'activate' ] );

// Bootstrap
add_action( 'plugins_loaded', [ 'Soda_Cashback_Wallet', 'instance' ] );

add_action( 'wp_enqueue_scripts', function () {
    // Só na conta do cliente > endpoint cashback-wallet
    if ( is_page( 'cashback-wallet' ) ) {

        // Bootstrap 5.3
        wp_enqueue_style(
            'bootstrap-5-3',
            'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css',
            [],
            '5.3.3'
        );

        wp_enqueue_script(
            'bootstrap-5-3',
            'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js',
            [ 'jquery' ],
            '5.3.3',
            true
        );

        // DataTables + Bootstrap 5 + Responsive
        wp_enqueue_style(
            'datatables-bs5',
            'https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css',
            [ 'bootstrap-5-3' ],
            '1.13.8'
        );

        wp_enqueue_style(
            'datatables-bs5-responsive',
            'https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css',
            [ 'datatables-bs5' ],
            '2.5.0'
        );

        wp_enqueue_script(
            'datatables-core',
            'https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js',
            [ 'jquery' ],
            '1.13.8',
            true
        );

        wp_enqueue_script(
            'datatables-bs5',
            'https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js',
            [ 'datatables-core', 'bootstrap-5-3' ],
            '1.13.8',
            true
        );

        wp_enqueue_script(
            'datatables-responsive',
            'https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js',
            [ 'datatables-core' ],
            '2.5.0',
            true
        );

        wp_enqueue_script(
            'datatables-responsive-bs5',
            'https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js',
            [ 'datatables-responsive', 'datatables-bs5' ],
            '2.5.0',
            true
        );

        // Init DataTables
        wp_add_inline_script(
            'datatables-responsive-bs5',
            "jQuery(function($){
                $('#cashback-transactions').DataTable({
                    responsive: true,
                    pageLength: 10,
                    lengthChange: false,
                    language: {
                        url: 'https://cdn.datatables.net/plug-ins/1.13.8/i18n/pt-BR.json'
                    }
                });
            });"
        );
    }
} );
