<?php
if (!defined('ABSPATH')) exit;

function bes_reports_tab() {
    if (!class_exists('WooCommerce')) : ?>
        <div class="notice notice-error">
            <p>WooCommerce must be active to view BES Reports.</p>
        </div>
    <?php
        return;
    endif;

    global $wpdb;

    $start_date = isset($_GET['start_date']) ? sanitize_text_field($_GET['start_date']) : date('Y-m-d', strtotime('-30 days'));
    $end_date   = isset($_GET['end_date']) ? sanitize_text_field($_GET['end_date']) : date('Y-m-d');
    ?>

    <h2>BES Unique Reports</h2>
    <p>Insights not available in default WooCommerce reports.</p>

    <!-- Date Filter -->
    <form method="get" style="margin-bottom:20px;">
        <input type="hidden" name="page" value="bes-settings">
        <input type="hidden" name="tab" value="reports">
        Start Date: <input type="date" name="start_date" value="<?php echo esc_attr($start_date); ?>">
        End Date: <input type="date" name="end_date" value="<?php echo esc_attr($end_date); ?>">
        <button class="button button-primary">Filter</button>
    </form>

    <!-- Charts -->
    <h3>Orders by District</h3>
    <canvas id="districtChart" style="max-width:600px;"></canvas>

    <h3>Average Order Value by Payment Method</h3>
    <canvas id="paymentChart" style="max-width:600px;"></canvas>

    <h3>Orders by Delivery Slot</h3>
    <canvas id="slotChart" style="max-width:600px;"></canvas>

    <h3>Orders by Customer Type</h3>
    <canvas id="customerChart" style="max-width:600px;"></canvas>

    <!-- Delivery Scheduler Usage -->
    <?php
    $delivery_orders = $wpdb->get_var($wpdb->prepare("
        SELECT COUNT(*) FROM {$wpdb->prefix}postmeta pm
        LEFT JOIN {$wpdb->prefix}posts p ON p.ID=pm.post_id
        WHERE pm.meta_key='_delivery_time_slot'
        AND p.post_type='shop_order'
        AND p.post_status IN ('wc-completed','wc-processing')
        AND p.post_date BETWEEN %s AND %s
    ", $start_date.' 00:00:00', $end_date.' 23:59:59'));
    ?>
    <h3>Delivery Scheduler Usage</h3>
    <p>Total Orders with Custom Delivery Slot: <strong><?php echo intval($delivery_orders); ?></strong></p>

    <?php
    // Fetch data for JS
    $districts = $wpdb->get_results($wpdb->prepare("
        SELECT meta_value as district, COUNT(*) as orders_count
        FROM {$wpdb->prefix}postmeta pm
        LEFT JOIN {$wpdb->prefix}posts p ON p.ID=pm.post_id
        WHERE pm.meta_key='_billing_district'
        AND p.post_type='shop_order'
        AND p.post_status IN ('wc-completed','wc-processing')
        AND p.post_date BETWEEN %s AND %s
        GROUP BY meta_value
        ORDER BY orders_count DESC
        LIMIT 10
    ", $start_date.' 00:00:00', $end_date.' 23:59:59'));

    $payments = $wpdb->get_results($wpdb->prepare("
        SELECT pm.meta_value as payment_method, AVG(pm2.meta_value) as avg_order
        FROM {$wpdb->prefix}postmeta pm
        LEFT JOIN {$wpdb->prefix}postmeta pm2 ON pm.post_id=pm2.post_id AND pm2.meta_key='_order_total'
        LEFT JOIN {$wpdb->prefix}posts p ON p.ID=pm.post_id
        WHERE pm.meta_key='_payment_method'
        AND p.post_type='shop_order'
        AND p.post_status IN ('wc-completed','wc-processing')
        AND p.post_date BETWEEN %s AND %s
        GROUP BY pm.meta_value
    ", $start_date.' 00:00:00', $end_date.' 23:59:59'));

    $slots = $wpdb->get_results($wpdb->prepare("
        SELECT pm.meta_value as slot, COUNT(*) as orders_count
        FROM {$wpdb->prefix}postmeta pm
        LEFT JOIN {$wpdb->prefix}posts p ON p.ID=pm.post_id
        WHERE pm.meta_key='_delivery_time_slot'
        AND p.post_type='shop_order'
        AND p.post_status IN ('wc-completed','wc-processing')
        AND p.post_date BETWEEN %s AND %s
        GROUP BY pm.meta_value
        ORDER BY orders_count DESC
    ", $start_date.' 00:00:00', $end_date.' 23:59:59'));

    $customers = $wpdb->get_results($wpdb->prepare("
        SELECT COUNT(*) as total, 
        CASE WHEN u.ID IS NULL THEN 'Guest' ELSE 'Registered' END as customer_type
        FROM {$wpdb->prefix}posts p
        LEFT JOIN {$wpdb->prefix}postmeta pm ON p.ID=pm.post_id AND pm.meta_key='_customer_user'
        LEFT JOIN {$wpdb->users} u ON u.ID=pm.meta_value
        WHERE p.post_type='shop_order' AND p.post_status IN ('wc-completed','wc-processing')
        AND p.post_date BETWEEN %s AND %s
        GROUP BY customer_type
    ", $start_date.' 00:00:00', $end_date.' 23:59:59'));

    wp_localize_script('bes-reports-js','besReportsData', [
        'districts' => $districts,
        'payments' => $payments,
        'slots' => $slots,
        'customers' => $customers,
    ]);
}
