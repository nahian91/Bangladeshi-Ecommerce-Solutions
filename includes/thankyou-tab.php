<?php
if (!defined('ABSPATH')) exit;

function bes_thankyou_tab() {
    $settings = get_option('bes_thankyou_settings', []);
    $message  = isset($settings['message']) ? $settings['message'] : '';
    ?>
    <h2>Custom Thank You Messages</h2>
    <p>Set different thank you messages after checkout.</p>

    <form method="post" action="options.php">
        <?php settings_fields('bes_thankyou_group'); ?>
        <textarea name="bes_thankyou_settings[message]" rows="5" style="width:100%; max-width:500px; padding:5px;"><?php echo esc_textarea($message); ?></textarea>
        <?php submit_button('Save Thank You Settings'); ?>
    </form>
    <?php
}
