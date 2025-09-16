<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Delivery Scheduler Settings Card (No Menu)
 */
function bes_delivery_tab() {
    $saved    = get_option( 'bes_delivery_scheduler', [] );
    $delivery = wp_parse_args( $saved, [
        'enabled'        => true,
        'time_slots'     => [],
        'blackout_dates' => [],
    ] );

    // Defaults
    if ( empty( $delivery['time_slots'] ) ) {
        $delivery['time_slots'] = [
            '9 AM - 12 PM',
            '12 PM - 3 PM',
            '3 PM - 6 PM',
            '6 PM - 9 PM',
            '9 PM - 11 PM',
        ];
    }

    if ( empty( $delivery['blackout_dates'] ) ) {
        for ( $i = 1; $i <= 5; $i++ ) {
            $delivery['blackout_dates'][] = date( 'Y-m-d', strtotime( "+$i day" ) );
        }
    }

    settings_fields( 'bes_delivery_group' );
    do_settings_sections( 'bes_delivery_group' );
    ?>
    <div class="bes-delivery-card" style="background:#fff; padding:25px; border-radius:12px; max-width:700px;">
        <h2 style="margin-bottom:20px;">Delivery Scheduler</h2>

        <!-- Enable Scheduler Switch -->
        <label class="switch">
            <input type="hidden" name="bes_delivery_scheduler[enabled]" value="0">
            <input type="checkbox" name="bes_delivery_scheduler[enabled]" value="1" <?php checked( $delivery['enabled'], true ); ?>>
            <span class="slider round"></span> Enable Delivery Scheduler
        </label>

        <!-- Time Slots -->
        <h4 style="margin-top:25px;">Available Time Slots</h4>
        <div id="bes-time-slots">
            <?php foreach ( $delivery['time_slots'] as $slot ) : ?>
                <div class="bes-repeater-item" style="margin-bottom:10px; display:flex; gap:10px; align-items:center;">
                    <input type="text" name="bes_delivery_scheduler[time_slots][]" value="<?php echo esc_attr( $slot ); ?>" style="flex:1; padding:5px;">
                    <button type="button" class="button remove-item">Remove</button>
                </div>
            <?php endforeach; ?>
        </div>
        <button type="button" class="button" id="add-slot">Add Time Slot</button>

        <!-- Blackout Dates -->
        <h4 style="margin-top:25px;">Blackout Dates</h4>
        <div id="bes-blackout-dates">
            <?php foreach ( $delivery['blackout_dates'] as $date ) : ?>
                <div class="bes-repeater-item" style="margin-bottom:10px; display:flex; gap:10px; align-items:center;">
                    <input type="text" class="date-picker" name="bes_delivery_scheduler[blackout_dates][]" value="<?php echo esc_attr( $date ); ?>" style="flex:1; padding:5px;">
                    <button type="button" class="button remove-item">Remove</button>
                </div>
            <?php endforeach; ?>
        </div>
        <button type="button" class="button" id="add-date">Add Blackout Date</button>

        <?php submit_button( 'Save Delivery Settings', 'primary', 'submit', true ); ?>
    </div>

    <!-- Styles -->
    <style>
        .switch { position: relative; display: inline-block; width: 50px; height: 24px; margin-bottom:10px; }
        .switch input { opacity: 0; width: 0; height: 0; }
        .slider { position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0;
                  background-color: #ccc; transition: .4s; border-radius: 24px; }
        .slider:before { position: absolute; content: ""; height: 18px; width: 18px; left: 3px; bottom: 3px;
                         background-color: white; transition: .4s; border-radius: 50%; }
        input:checked + .slider { background-color: #0073aa; }
        input:checked + .slider:before { transform: translateX(26px); }
        h4 { margin-bottom:10px; }
        .bes-repeater-item input { margin-right:10px; }
    </style>

    <?php
    // Enqueue jQuery UI Datepicker for admin
    wp_enqueue_script('jquery-ui-datepicker');
    wp_enqueue_style('jquery-ui-style', 'https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css');

    // JS for repeater + date picker
    ?>
    <script>
        jQuery(document).ready(function($){
            // Initialize Date Picker
            function initDatePicker() {
                $('.date-picker').datepicker({ dateFormat: 'yy-mm-dd', minDate: 0 });
            }
            initDatePicker();

            // Add Time Slot
            $('#add-slot').on('click', function(){
                $('#bes-time-slots').append('<div class="bes-repeater-item" style="margin-bottom:10px; display:flex; gap:10px; align-items:center;"><input type="text" name="bes_delivery_scheduler[time_slots][]" style="flex:1; padding:5px;"><button type="button" class="button remove-item">Remove</button></div>');
            });

            // Add Blackout Date
            $('#add-date').on('click', function(){
                $('#bes-blackout-dates').append('<div class="bes-repeater-item" style="margin-bottom:10px; display:flex; gap:10px; align-items:center;"><input type="text" class="date-picker" name="bes_delivery_scheduler[blackout_dates][]" style="flex:1; padding:5px;"><button type="button" class="button remove-item">Remove</button></div>');
                initDatePicker();
            });

            // Remove item
            $(document).on('click','.remove-item',function(){
                $(this).parent().remove();
            });
        });
    </script>
    <?php
}

// Register setting
add_action( 'admin_init', function () {
    register_setting( 'bes_delivery_group', 'bes_delivery_scheduler' );
} );

/**
 * WooCommerce Checkout Fields
 */
add_filter( 'woocommerce_checkout_fields', function ( $fields ) {
    $settings = get_option( 'bes_delivery_scheduler', [] );
    $settings = wp_parse_args( $settings, [ 'enabled' => true, 'time_slots' => [] ] );

    if ( empty( $settings['enabled'] ) ) {
        return $fields;
    }

    $fields['billing']['delivery_date'] = [
        'type'        => 'text',
        'label'       => __( 'Delivery Date', 'bes-delivery' ),
        'placeholder' => __( 'YYYY-MM-DD', 'bes-delivery' ),
        'required'    => true,
        'class'       => [ 'form-row-wide', 'delivery-date' ],
    ];

    $fields['billing']['delivery_time'] = [
        'type'     => 'select',
        'label'    => __( 'Delivery Time', 'bes-delivery' ),
        'required' => true,
        'options'  => array_combine( $settings['time_slots'], $settings['time_slots'] ),
        'class'    => [ 'form-row-wide' ],
    ];

    return $fields;
} );

/**
 * Save order meta
 */
add_action( 'woocommerce_checkout_update_order_meta', function ( $order_id ) {
    if ( ! empty( $_POST['delivery_date'] ) ) {
        update_post_meta( $order_id, '_delivery_date', sanitize_text_field( $_POST['delivery_date'] ) );
    }
    if ( ! empty( $_POST['delivery_time'] ) ) {
        update_post_meta( $order_id, '_delivery_time', sanitize_text_field( $_POST['delivery_time'] ) );
    }
} );

/**
 * Show in admin order details
 */
add_action( 'woocommerce_admin_order_data_after_billing_address', function ( $order ) {
    $date = get_post_meta( $order->get_id(), '_delivery_date', true );
    $time = get_post_meta( $order->get_id(), '_delivery_time', true );

    if ( $date || $time ) : ?>
        <p><strong><?php esc_html_e( 'Delivery Date', 'bes-delivery' ); ?>:</strong> <?php echo esc_html( $date ); ?></p>
        <p><strong><?php esc_html_e( 'Delivery Time', 'bes-delivery' ); ?>:</strong> <?php echo esc_html( $time ); ?></p>
    <?php endif;
} );

/**
 * Enqueue datepicker on checkout page
 */
add_action( 'wp_enqueue_scripts', function(){
    if( !is_checkout() ) return;

    wp_enqueue_script('jquery-ui-datepicker');
    wp_enqueue_style('jquery-ui-css', 'https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css');

    wp_add_inline_script('jquery-ui-datepicker', "
        jQuery(function($){
            function init_delivery_datepicker(){
                $('.delivery-date').datepicker({ 
                    dateFormat: 'yy-mm-dd',
                    minDate: 0
                });
            }
            init_delivery_datepicker();
            $(document.body).on('updated_checkout', function(){
                init_delivery_datepicker();
            });
        });
    ");
});
