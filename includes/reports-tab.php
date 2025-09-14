<?php
if (!defined('ABSPATH')) exit;

function bes_reports_tab(){
    if (!class_exists('WooCommerce')) {
        echo '<div class="notice notice-error"><p>WooCommerce must be active to view BES Reports.</p></div>';
        return;
    }

    global $wpdb;

    echo '<h2>BES Unique Reports</h2>';
    echo '<p>Insights not available in default WooCommerce reports.</p>';

    // ===== Date Filter =====
    $start_date = isset($_GET['start_date']) ? sanitize_text_field($_GET['start_date']) : date('Y-m-d', strtotime('-30 days'));
    $end_date   = isset($_GET['end_date']) ? sanitize_text_field($_GET['end_date']) : date('Y-m-d');

    echo '<form method="get" style="margin-bottom:20px;">';
    echo '<input type="hidden" name="page" value="bes-settings">';
    echo '<input type="hidden" name="tab" value="reports">';
    echo 'Start Date: <input type="date" name="start_date" value="'.esc_attr($start_date).'">';
    echo ' End Date: <input type="date" name="end_date" value="'.esc_attr($end_date).'">';
    echo ' <button class="button button-primary">Filter</button>';
    echo '</form>';

    // Convert dates for SQL
    $start = $wpdb->prepare('%s', $start_date . ' 00:00:00');
    $end   = $wpdb->prepare('%s', $end_date . ' 23:59:59');

    // ===== 1️⃣ Orders by District/Upazilla =====
    $districts = $wpdb->get_results("
        SELECT meta_value as district, COUNT(*) as orders_count
        FROM {$wpdb->prefix}postmeta pm
        LEFT JOIN {$wpdb->prefix}posts p ON p.ID=pm.post_id
        WHERE pm.meta_key='_billing_district'
        AND p.post_type='shop_order'
        AND p.post_status IN ('wc-completed','wc-processing')
        AND p.post_date BETWEEN '$start' AND '$end'
        GROUP BY meta_value
        ORDER BY orders_count DESC
        LIMIT 10
    ");

    $district_labels = [];
    $district_data = [];
    foreach($districts as $d){
        $district_labels[] = $d->district;
        $district_data[] = intval($d->orders_count);
    }

    echo '<h3>Orders by District</h3>';
    echo '<canvas id="districtChart" style="max-width:600px;"></canvas>';

    // ===== 2️⃣ Average Order Value by Payment Method =====
    $payments = $wpdb->get_results("
        SELECT pm.meta_value as payment_method, AVG(pm2.meta_value) as avg_order
        FROM {$wpdb->prefix}postmeta pm
        LEFT JOIN {$wpdb->prefix}postmeta pm2 ON pm.post_id=pm2.post_id AND pm2.meta_key='_order_total'
        LEFT JOIN {$wpdb->prefix}posts p ON p.ID=pm.post_id
        WHERE pm.meta_key='_payment_method'
        AND p.post_type='shop_order'
        AND p.post_status IN ('wc-completed','wc-processing')
        AND p.post_date BETWEEN '$start' AND '$end'
        GROUP BY pm.meta_value
    ");

    $payment_labels = [];
    $payment_data = [];
    foreach($payments as $p){
        $payment_labels[] = $p->payment_method;
        $payment_data[] = floatval($p->avg_order);
    }

    echo '<h3>Average Order Value by Payment Method</h3>';
    echo '<canvas id="paymentChart" style="max-width:600px;"></canvas>';

    // ===== 3️⃣ Orders by Delivery Slot =====
    $slots = $wpdb->get_results("
        SELECT pm.meta_value as slot, COUNT(*) as orders_count
        FROM {$wpdb->prefix}postmeta pm
        LEFT JOIN {$wpdb->prefix}posts p ON p.ID=pm.post_id
        WHERE pm.meta_key='_delivery_time_slot'
        AND p.post_type='shop_order'
        AND p.post_status IN ('wc-completed','wc-processing')
        AND p.post_date BETWEEN '$start' AND '$end'
        GROUP BY pm.meta_value
        ORDER BY orders_count DESC
    ");

    $slot_labels = [];
    $slot_data = [];
    foreach($slots as $s){
        $slot_labels[] = $s->slot;
        $slot_data[] = intval($s->orders_count);
    }

    echo '<h3>Orders by Delivery Slot</h3>';
    echo '<canvas id="slotChart" style="max-width:600px;"></canvas>';

    // ===== 4️⃣ Orders by Customer Type =====
    $customers = $wpdb->get_results("
        SELECT COUNT(*) as total, 
        CASE WHEN u.ID IS NULL THEN 'Guest' ELSE 'Registered' END as customer_type
        FROM {$wpdb->prefix}posts p
        LEFT JOIN {$wpdb->prefix}postmeta pm ON p.ID=pm.post_id AND pm.meta_key='_customer_user'
        LEFT JOIN {$wpdb->users} u ON u.ID=pm.meta_value
        WHERE p.post_type='shop_order' AND p.post_status IN ('wc-completed','wc-processing')
        AND p.post_date BETWEEN '$start' AND '$end'
        GROUP BY customer_type
    ");

    $customer_labels = [];
    $customer_data = [];
    foreach($customers as $c){
        $customer_labels[] = $c->customer_type;
        $customer_data[] = intval($c->total);
    }

    echo '<h3>Orders by Customer Type</h3>';
    echo '<canvas id="customerChart" style="max-width:600px;"></canvas>';

    // ===== 5️⃣ Delivery Scheduler Usage =====
    $delivery_orders = $wpdb->get_var("
        SELECT COUNT(*) FROM {$wpdb->prefix}postmeta pm
        LEFT JOIN {$wpdb->prefix}posts p ON p.ID=pm.post_id
        WHERE pm.meta_key='_delivery_time_slot'
        AND p.post_type='shop_order'
        AND p.post_status IN ('wc-completed','wc-processing')
        AND p.post_date BETWEEN '$start' AND '$end'
    ");
    echo '<h3>Delivery Scheduler Usage</h3>';
    echo '<p>Total Orders with Custom Delivery Slot: <strong>'.intval($delivery_orders).'</strong></p>';

    // ===== Chart.js =====
    echo '<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>';
    echo "<script>
        const districtCtx = document.getElementById('districtChart').getContext('2d');
        new Chart(districtCtx, {
            type: 'bar',
            data: {
                labels: ".json_encode($district_labels).",
                datasets: [{
                    label: 'Orders',
                    data: ".json_encode($district_data).",
                    backgroundColor: 'rgba(75, 192, 192, 0.6)'
                }]
            },
            options: { responsive:true, plugins:{legend:{display:false}} }
        });

        const paymentCtx = document.getElementById('paymentChart').getContext('2d');
        new Chart(paymentCtx, {
            type: 'bar',
            data: {
                labels: ".json_encode($payment_labels).",
                datasets: [{
                    label: 'Average Order Value',
                    data: ".json_encode($payment_data).",
                    backgroundColor: 'rgba(153, 102, 255, 0.6)'
                }]
            },
            options: { responsive:true, plugins:{legend:{display:false}}, scales:{y:{beginAtZero:true}} }
        });

        const slotCtx = document.getElementById('slotChart').getContext('2d');
        new Chart(slotCtx, {
            type: 'bar',
            data: {
                labels: ".json_encode($slot_labels).",
                datasets: [{
                    label: 'Orders',
                    data: ".json_encode($slot_data).",
                    backgroundColor: 'rgba(255, 159, 64, 0.6)'
                }]
            },
            options: { responsive:true, plugins:{legend:{display:false}} }
        });

        const customerCtx = document.getElementById('customerChart').getContext('2d');
        new Chart(customerCtx, {
            type: 'pie',
            data: {
                labels: ".json_encode($customer_labels).",
                datasets: [{
                    label: 'Orders',
                    data: ".json_encode($customer_data).",
                    backgroundColor: ['#36A2EB','#FF6384']
                }]
            },
            options: { responsive:true }
        });
    </script>";
}
