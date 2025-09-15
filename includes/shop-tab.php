<?php
if (!defined('ABSPATH')) exit;

function bes_shop_tab() {
    $defaults = [
        'show_featured' => 1,
        'show_sale_badge' => 1,
        'show_add_to_cart' => 1,
        'show_whatsapp_button' => 1,
        'hide_prices' => 0,
        'disable_shop_page' => 0,
        'hide_categories' => 0,
        'hide_tags' => 0,
        'custom_label' => ''
    ];

    $settings = wp_parse_args(get_option('bes_shop_settings', []), $defaults);
    ?>

    <h2>Shop Settings</h2>
    <p>Control shop page layout and product visibility.</p>

    <form method="post" action="options.php">
        <?php settings_fields('bes_shop_group'); ?>

        <div class="bes-shop-card">
            <?php
            $checkboxes = [
                'show_featured' => 'Show Featured Products',
                'show_sale_badge' => 'Show Sale Badge',
                'show_add_to_cart' => 'Show Add to Cart Buttons',
                'show_whatsapp_button' => 'Show WhatsApp Button',
                'hide_prices' => 'Hide Product Prices',
                'disable_shop_page' => 'Disable Shop Page',
                'hide_categories' => 'Hide Categories',
                'hide_tags' => 'Hide Tags'
            ];

            foreach ($checkboxes as $key => $label):
                $val = !empty($settings[$key]) ? 1 : 0;
            ?>
                <div class="bes-shop-field">
                    <label class="switch">
                        <input type="hidden" name="bes_shop_settings[<?php echo esc_attr($key); ?>]" value="0">
                        <input type="checkbox" name="bes_shop_settings[<?php echo esc_attr($key); ?>]" value="1" <?php checked($val,1); ?>>
                        <span class="slider round"></span> <?php echo esc_html($label); ?>
                    </label>
                </div>
            <?php endforeach; ?>

            <div class="bes-shop-field" style="margin-top:10px;">
                <label>Custom Label/Text for Shop Page:</label><br>
                <input type="text" name="bes_shop_settings[custom_label]" value="<?php echo esc_attr($settings['custom_label']); ?>" style="width:100%; max-width:400px; padding:5px;">
            </div>

            <?php submit_button('Save Shop Settings'); ?>
        </div>
    </form>

<?php
}
