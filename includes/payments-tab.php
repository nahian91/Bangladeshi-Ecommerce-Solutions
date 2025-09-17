<?php
if (!defined('ABSPATH')) exit;

function bes_payments_tab() {

    $allowed_gateways = ['bkash', 'nagad', 'upay', 'rocket'];
    $gateways = WC()->payment_gateways->payment_gateways();

    // Display transient notice if exists
    if ($message = get_transient('bpm_success_message')) {
        echo '<div class="notice notice-success is-dismissible" style="margin-top:10px;">';
        echo '<p>' . esc_html($message) . '</p>';
        echo '</div>';
        delete_transient('bpm_success_message');
    }

    // Get all completed orders
    $orders = wc_get_orders(['limit' => -1, 'status' => 'completed']);
    $total_amount = 0;

    foreach ($orders as $order) {
        $total_amount += $order->get_total();
    }
    ?>

    <div class="bpm-container">

        <!-- Payment Method Statistics -->
        <h2><?php esc_html_e('Payment Method Statistics', 'bangladeshi-payments-mobile'); ?></h2>
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th><?php esc_html_e('Payment Method', 'bangladeshi-payments-mobile'); ?></th>
                    <th><?php esc_html_e('Total Amount', 'bangladeshi-payments-mobile'); ?></th>
                    <th><?php esc_html_e('Status', 'bangladeshi-payments-mobile'); ?></th>
                    <th><?php esc_html_e('Action', 'bangladeshi-payments-mobile'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($gateways as $gateway):
                    if (!in_array($gateway->id, $allowed_gateways)) continue;

                    $total_gateway_amount = 0;
                    $gateway_orders = wc_get_orders([
                        'limit' => -1,
                        'status' => 'completed',
                        'payment_method' => $gateway->id,
                    ]);
                    foreach ($gateway_orders as $order) {
                        $total_gateway_amount += $order->get_total();
                    }

                    $status = ($gateway->enabled === 'yes') ? 'Active' : 'Inactive';
                    $status_class = ($gateway->enabled === 'yes') ? 'status-active' : 'status-inactive';
                    $edit_link = admin_url('admin.php?page=wc-settings&tab=checkout&section=' . $gateway->id);
                ?>
                    <tr>
                        <td><?php echo esc_html($gateway->get_title()); ?></td>
                        <td><?php echo wp_kses_post(wc_price($total_gateway_amount)); ?></td>
                        <td class="<?php echo esc_attr($status_class); ?>"><?php echo esc_html($status); ?></td>
                        <td><a href="<?php echo esc_url($edit_link); ?>" target="_blank" class="button button-primary"><?php esc_html_e('Edit', 'bangladeshi-payments-mobile'); ?></a></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <h2><?php esc_html_e('Transaction Information', 'bangladeshi-payments-mobile'); ?></h2>
        <table id="transaction-info-table" class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th><?php esc_html_e('Order ID', 'bangladeshi-payments-mobile'); ?></th>
                    <th><?php esc_html_e('Payment Method', 'bangladeshi-payments-mobile'); ?></th>
                    <th><?php esc_html_e('Transaction ID', 'bangladeshi-payments-mobile'); ?></th>
                    <th><?php esc_html_e('Phone Number', 'bangladeshi-payments-mobile'); ?></th>
                    <th><?php esc_html_e('Amount', 'bangladeshi-payments-mobile'); ?></th>
                    <th><?php esc_html_e('Date', 'bangladeshi-payments-mobile'); ?></th>
                    <th><?php esc_html_e('Order Status', 'bangladeshi-payments-mobile'); ?></th>
                    <th><?php esc_html_e('Action', 'bangladeshi-payments-mobile'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order):
                    $method = $order->get_payment_method();
                    $transaction_id = get_post_meta($order->get_id(), "_{$method}_transaction_id", true) ?: 'N/A';
                    $phone = get_post_meta($order->get_id(), "_{$method}_phone", true) ?: 'N/A';
                ?>
                    <tr>
                        <td><?php echo esc_html($order->get_id()); ?></td>
                        <td><?php echo esc_html(ucwords($method)); ?></td>
                        <td><?php echo esc_html($transaction_id); ?></td>
                        <td><?php echo esc_html($phone); ?></td>
                        <td><?php echo wp_kses_post(wc_price($order->get_total())); ?></td>
                        <td><?php echo esc_html($order->get_date_created()->date('d F Y')); ?></td>
                        <td><?php echo esc_html(wc_get_order_status_name($order->get_status())); ?></td>
                        <td><a href="<?php echo esc_url($order->get_edit_order_url()); ?>" class="button button-primary"><?php esc_html_e('Edit Order', 'bangladeshi-payments-mobile'); ?></a></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <?php if ($total_amount > 0): ?>
            <h3><?php esc_html_e('Total Amount: ', 'bangladeshi-payments-mobile'); echo wp_kses_post(wc_price($total_amount)); ?></h3>
        <?php endif; ?>

    </div>

    <?php
    // Enqueue DataTables
    add_action('admin_footer', function() {
        ?>
        <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
        <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
        <script>
            jQuery(document).ready(function($){
                $('#transaction-info-table').DataTable({
                    paging: true,
                    searching: true,
                    ordering: true,
                    order: [[0, 'desc']]
                });
            });
        </script>
        <style>
            .status-active { color: green; font-weight: bold; }
            .status-inactive { color: red; font-weight: bold; }
            table.dataTable { width: 100% !important; }
        </style>
        <?php
    });
}
