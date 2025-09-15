<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Delivery Scheduler Settings Page
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
    <h2>Delivery Scheduler</h2>
    <div class="bes-delivery-card">

        <label class="switch">
            <input type="checkbox" name="bes_delivery_scheduler[enabled]" <?php checked( $delivery['enabled'], true ); ?>>
            <span class="slider round"></span> Enable Delivery Scheduler
        </label>

        <h4>Available Time Slots</h4>
        <div id="bes-time-slots">
            <?php foreach ( $delivery['time_slots'] as $slot ) : ?>
                <div class="bes-repeater-item">
                    <input type="text" name="bes_delivery_scheduler[time_slots][]" value="<?php echo esc_attr( $slot ); ?>">
                    <button class="button remove-item">Remove</button>
                </div>
            <?php endforeach; ?>
        </div>
        <button class="button" id="add-slot">Add Time Slot</button>

        <h4>Blackout Dates</h4>
        <div id="bes-blackout-dates">
            <?php foreach ( $delivery['blackout_dates'] as $date ) : ?>
                <div class="bes-repeater-item">
                    <input type="text" class="date-picker" name="bes_delivery_scheduler[blackout_dates][]" value="<?php echo esc_attr( $date ); ?>">
                    <button class="button remove-item">Remove</button>
                </div>
            <?php endforeach; ?>
        </div>
        <button class="button" id="add-date">Add Blackout Date</button>

    </div>
    <?php
    submit_button( 'Save Delivery Settings' );
}

/**
 * Register menu & settings
 */
add_action( 'admin_menu', function () {
    add_submenu_page(
        'woocommerce',
        'Delivery Scheduler',
        'Delivery Scheduler',
        'manage_options',
        'bes-delivery-scheduler',
        'bes_delivery_tab'
    );
} );

add_action( 'admin_init', function () {
    register_setting( 'bes_delivery_group', 'bes_delivery_scheduler' );
} );

/**
 * Checkout fields
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
        'class'       => [ 'form-row-wide' ],
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
