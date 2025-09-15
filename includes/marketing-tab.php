<?php
if (!defined('ABSPATH')) exit;

function bes_marketing_tab() {
    $settings = get_option('bes_marketing_settings', []);
    $campaign = isset($settings['campaign']) ? $settings['campaign'] : '';
    ?>

    <form method="post" action="options.php">
        <?php settings_fields('bes_marketing_group'); ?>
        <?php do_settings_sections('bes_marketing_group'); ?>

        <h2>Marketing Settings</h2>
        <p>Setup banners, campaigns, or promotional messages.</p>

        <div class="bes-marketing-wrapper" style="max-width:500px;">
            <label for="bes_marketing_campaign">Campaign Message:</label><br>
            <textarea id="bes_marketing_campaign" name="bes_marketing_settings[campaign]" rows="5" style="width:100%; padding:5px;"><?php echo esc_textarea($campaign); ?></textarea>
        </div>

        <?php submit_button('Save Marketing Settings'); ?>
    </form>

<?php
}
