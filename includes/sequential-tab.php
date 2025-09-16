<?php
if (!defined('ABSPATH')) exit;

// -------------------- Sequential Number Tab --------------------
function bes_sequential_tab() {
    // Get current settings
    $settings = get_option('bes_sequential_settings', [
        'prefix' => 'ORD-',
        'start'  => 1000
    ]);

    // Get last used number
    $last_number = get_option('bes_last_order_number', $settings['start'] - 1);

    ?>
    <div class="bpsm-card">
        <h3>Sequential Order Number Settings</h3>

        <div class="bpsm-mt-10">
            <label>Order Prefix</label>
            <input class="bpsm-input" type="text" name="bes_sequential_settings[prefix]" value="<?php echo esc_attr($settings['prefix']); ?>" />
        </div>

        <div class="bpsm-mt-10">
            <label>Starting Number</label>
            <input class="bpsm-input" type="number" name="bes_sequential_settings[start]" value="<?php echo esc_attr($settings['start']); ?>" />
        </div>

        <div class="bpsm-mt-10">
            <label>Current Last Number</label>
            <input class="bpsm-input" type="number" value="<?php echo esc_attr($last_number); ?>" disabled />
        </div>

        <div class="bpsm-mt-15">
            <?php submit_button('Save Settings'); ?>
        </div>

        <div class="bpsm-mt-10">
            <form method="post">
                <input type="hidden" name="bes_reset_counter" value="1">
                <?php submit_button('Reset Counter', 'secondary'); ?>
            </form>
        </div>
    </div>

    <?php
    // Handle counter reset
    if (isset($_POST['bes_reset_counter']) && $_POST['bes_reset_counter'] == 1 && current_user_can('manage_options')) {
        $start = isset($settings['start']) ? intval($settings['start']) : 1000;
        update_option('bes_last_order_number', $start - 1);
        echo '<div class="bpsm-alert bpsm-alert-success bpsm-mt-10">Counter reset successfully!</div>';
    }
}

// -------------------- Register Setting --------------------
add_action('admin_init', function() {
    register_setting('bes_sequential_group', 'bes_sequential_settings');
});
