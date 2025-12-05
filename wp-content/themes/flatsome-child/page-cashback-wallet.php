<?php
/**
 * Template Name: Cashback Wallet
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

get_header();

if ( ! is_user_logged_in() ) : ?>

    <div class="container my-5">
        <p><?php esc_html_e( 'Você precisa estar logado para ver sua carteira.', 'soda-cashback-wallet' ); ?></p>
    </div>

<?php
else :

    $user_id = get_current_user_id();
    global $wpdb;

    $wallet_table       = $wpdb->prefix . 'soda_cashback_wallets';
    $transactions_table = $wpdb->prefix . 'soda_cashback_transactions';

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
            "SELECT * FROM {$transactions_table} WHERE user_id = %d ORDER BY created_at DESC",
            $user_id
        )
    );
    ?>

    <div class="woocommerce my-4">
        <?php do_action( 'woocommerce_account_navigation' ); ?>

        <div class="woocommerce-MyAccount-content">
            <div class="container-fluid px-0">
                <div class="row">
                    <div class="col-12 col-lg-10">

                        <h2 class="mb-4"><?php esc_html_e( 'Minha Carteira de Cashback', 'soda-cashback-wallet' ); ?></h2>

                        <div class="card mb-4">
                            <div class="card-body d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                                <div>
                                    <h5 class="card-title mb-1">
                                        <?php esc_html_e( 'Saldo disponível', 'soda-cashback-wallet' ); ?>
                                    </h5>
                                    <p class="card-text fs-4 fw-bold mb-0">
                                        <?php echo function_exists( 'wc_price' ) ? wc_price( $balance ) : esc_html( number_format( $balance, 2, ',', '.' ) ); ?>
                                    </p>
                                </div>
                                <?php if ( $frozen ) : ?>
                                    <div class="mt-3 mt-md-0 alert alert-danger mb-0 py-2 px-3">
                                        <?php esc_html_e( 'Sua carteira está congelada devido a pendências na assinatura. Regularize para voltar a usar o saldo.', 'soda-cashback-wallet' ); ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <h3 class="mb-3"><?php esc_html_e( 'Últimas transações', 'soda-cashback-wallet' ); ?></h3>

                        <div class="card">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="cashback-transactions" class="table table-striped table-bordered table-hover align-middle nowrap w-100">
                                        <thead class="table-light">
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
                <td>
                    <?php
                    if ( function_exists( 'wc_format_datetime' ) && function_exists( 'wc_string_to_datetime' ) ) {
                        echo esc_html( wc_format_datetime( wc_string_to_datetime( $t->created_at ) ) );
                    } else {
                        echo esc_html( $t->created_at );
                    }
                    ?>
                </td>
                <td><?php echo esc_html( $t->type ); ?></td>
                <td><?php echo esc_html( $t->description ); ?></td>
                <td>
                    <?php
                    if ( function_exists( 'wc_price' ) ) {
                        echo wc_price( $t->amount );
                    } else {
                        echo esc_html( number_format( $t->amount, 2, ',', '.' ) );
                    }
                    ?>
                </td>
                <td><?php echo esc_html( $t->status ); ?></td>
            </tr>
        <?php endforeach; ?>
    <?php endif; ?>
</tbody>

                                    </table>
                                    <?php if ( empty( $transactions ) ) : ?>
    <p class="text-center my-3">
        <?php esc_html_e( 'Nenhuma transação ainda.', 'soda-cashback-wallet' ); ?>
    </p>
<?php endif; ?>

                                </div><!-- .table-responsive -->
                            </div>
                        </div><!-- .card -->

                    </div>
                </div>
            </div>
        </div><!-- .woocommerce-MyAccount-content -->
    </div><!-- .woocommerce -->

<?php
endif;

get_footer();
