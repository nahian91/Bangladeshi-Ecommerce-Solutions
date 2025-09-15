<?php
if (!defined('ABSPATH')) exit;

function bes_product_tab() {
    // Default settings
    $defaults = [
        'show_sku' => 1,
        'show_stock' => 1,
        'show_categories' => 1,
        'show_tags' => 0,
        'disable_price' => 0,
        'disable_add_to_cart' => 0,
        'disable_variations' => 0,
        'show_reviews' => 1,
        'custom_label' => '' // Custom text label
    ];

    $settings = wp_parse_args(get_option('bes_product_settings', []), $defaults);
    $checkboxes = [
        'show_sku' => 'Show SKU',
        'show_stock' => 'Show Stock Status',
        'show_categories' => 'Show Categories',
        'show_tags' => 'Show Tags',
        'disable_price' => 'Hide Product Price',
        'disable_add_to_cart' => 'Disable Add to Cart Button',
        'disable_variations' => 'Disable Product Variations',
        'show_reviews' => 'Show Reviews'
    ];
    ?>

    <h2>Product Settings</h2>
    <p>Control product page elements and visibility.</p>

    <form method="post" action="options.php">
        <?php settings_fields('bes_product_group'); ?>

        <div class="bes-product-card">
            <?php foreach ($checkboxes as $key => $label) : 
                $val = !empty($settings[$key]) ? 1 : 0; ?>
                <div class="bes-product-field">
                    <label class="switch">
                        <input type="hidden" name="bes_product_settings[<?php echo esc_attr($key); ?>]" value="0">
                        <input type="checkbox" name="bes_product_settings[<?php echo esc_attr($key); ?>]" value="1" <?php checked($val, 1); ?>>
                        <span class="slider round"></span> <?php echo esc_html($label); ?>
                    </label>
                </div>
            <?php endforeach; ?>

            <!-- Custom text label -->
            <div class="bes-product-field" style="margin-top:10px;">
                <label>Custom Label/Text for Product Page:</label><br>
                <input type="text" name="bes_product_settings[custom_label]" value="<?php echo esc_attr($settings['custom_label']); ?>" style="width:100%; max-width:400px; padding:5px;">
            </div>

            <?php submit_button('Save Product Settings'); ?>
        </div>
    </form>

<?php
}
