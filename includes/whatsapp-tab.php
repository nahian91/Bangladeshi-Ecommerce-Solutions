<?php
if (!defined('ABSPATH')) exit;

function bcaw_whatsapp_tab() {
    $defaults = [
        'enabled'      => 0,
        'number'       => '',
        'button_text'  => 'Order via WhatsApp',
        'message'      => "Hello! I would like to order the following product:\n\nProduct: {product}\nQuantity: [Please specify]\n\nPlease confirm price and delivery.",
        'show_single'  => 1,
        'show_archive' => 1,
        'color'        => '#25D366',
    ];

    $settings = wp_parse_args(get_option('bes_whatsapp_settings', []), $defaults);

    // Pass settings to JS
    wp_localize_script('bcaw-whatsapp-js', 'bcawSettings', $settings);
    wp_enqueue_style('wp-color-picker');
    wp_enqueue_script('wp-color-picker');
    ?>

    <h2><?php esc_html_e('WhatsApp Order Settings', 'banglacommerce-all-in-one-woocommerce'); ?></h2>
    <form method="post">
        <?php wp_nonce_field('bes_whatsapp_save', 'bes_whatsapp_nonce'); ?>

        <div class="bcaw-card">

            <div class="bcaw-field">
                <label><?php esc_html_e('Enable WhatsApp Button', 'banglacommerce-all-in-one-woocommerce'); ?></label>
                <label class="bcaw-switch">
                    <input type="checkbox" name="bes_whatsapp_settings[enabled]" value="1" <?php checked($settings['enabled'], 1); ?>>
                    <span class="bcaw-slider"></span>
                </label>
            </div>

            <div class="bcaw-field">
                <label><?php esc_html_e('Phone Number', 'banglacommerce-all-in-one-woocommerce'); ?></label>
                <input type="text" name="bes_whatsapp_settings[number]" value="<?php echo esc_attr($settings['number']); ?>" class="bcaw-input bcaw-live" data-target="number">
            </div>

            <div class="bcaw-field">
                <label><?php esc_html_e('Button Text', 'banglacommerce-all-in-one-woocommerce'); ?></label>
                <input type="text" name="bes_whatsapp_settings[button_text]" value="<?php echo esc_attr($settings['button_text']); ?>" class="bcaw-input bcaw-live" data-target="text">
            </div>

            <div class="bcaw-field">
                <label><?php esc_html_e('Message Template', 'banglacommerce-all-in-one-woocommerce'); ?></label>
                <textarea name="bes_whatsapp_settings[message]" rows="6" class="bcaw-textarea bcaw-live" data-target="message"><?php echo esc_textarea($settings['message']); ?></textarea>
                <small><?php esc_html_e('Use {product} to include the product name.', 'banglacommerce-all-in-one-woocommerce'); ?></small>
            </div>

            <div class="bcaw-field">
                <label><?php esc_html_e('Show on Single Product Page', 'banglacommerce-all-in-one-woocommerce'); ?></label>
                <label class="bcaw-switch">
                    <input type="checkbox" name="bes_whatsapp_settings[show_single]" value="1" <?php checked($settings['show_single'], 1); ?>>
                    <span class="bcaw-slider"></span>
                </label>
            </div>

            <div class="bcaw-field">
                <label><?php esc_html_e('Show on Shop / Archive Pages', 'banglacommerce-all-in-one-woocommerce'); ?></label>
                <label class="bcaw-switch">
                    <input type="checkbox" name="bes_whatsapp_settings[show_archive]" value="1" <?php checked($settings['show_archive'], 1); ?>>
                    <span class="bcaw-slider"></span>
                </label>
            </div>

            <div class="bcaw-field">
                <label><?php esc_html_e('Button Color', 'banglacommerce-all-in-one-woocommerce'); ?></label>
                <input type="text" name="bes_whatsapp_settings[color]" value="<?php echo esc_attr($settings['color']); ?>" class="bcaw-color-picker bcaw-live" data-target="color">
            </div>

            <div class="bcaw-field bcaw-preview-wrapper">
                <label><?php esc_html_e('Live Preview', 'banglacommerce-all-in-one-woocommerce'); ?></label>
                <div class="bcaw-preview">
                    <a href="#" class="bes-whatsapp-btn" id="bcaw-live-preview" target="_blank"><?php echo esc_html($settings['button_text']); ?></a>
                </div>
                <div class="bcaw-preview-message">
                    <small><?php esc_html_e('Preview message:', 'banglacommerce-all-in-one-woocommerce'); ?></small>
                    <div id="bcaw-live-message"><?php echo esc_html(str_replace('{product}', 'Sample Product', $settings['message'])); ?></div>
                </div>
                <button type="button" class="button" id="bcaw-copy-link"><?php esc_html_e('Copy WhatsApp Link', 'banglacommerce-all-in-one-woocommerce'); ?></button>
            </div>

        </div>

        <?php submit_button(__('Save WhatsApp Settings', 'banglacommerce-all-in-one-woocommerce')); ?>
    </form>

<?php
}

// Save Settings
add_action('admin_init', function() {
    if (isset($_POST['bes_whatsapp_settings']) && check_admin_referer('bes_whatsapp_save', 'bes_whatsapp_nonce')) {
        $data = $_POST['bes_whatsapp_settings'];
        $settings = [
            'enabled'      => !empty($data['enabled']) ? 1 : 0,
            'number'       => sanitize_text_field($data['number'] ?? ''),
            'button_text'  => sanitize_text_field($data['button_text'] ?? 'Order via WhatsApp'),
            'message'      => sanitize_textarea_field($data['message'] ?? ''),
            'show_single'  => !empty($data['show_single']) ? 1 : 0,
            'show_archive' => !empty($data['show_archive']) ? 1 : 0,
            'color'        => sanitize_hex_color($data['color'] ?? '#25D366'),
        ];
        update_option('bes_whatsapp_settings', $settings);
    }
});

// Frontend WhatsApp Button
function bes_whatsapp_button_output() {
    $settings = wp_parse_args(get_option('bes_whatsapp_settings', []), [
        'enabled'=>0,'number'=>'','button_text'=>'','message'=>'','color'=>'#25D366'
    ]);
    if (empty($settings['enabled']) || empty($settings['number'])) return;

    global $product;
    if (!$product) return;

    $product_name = $product->get_name();
    $message = rawurlencode(str_replace('{product}', $product_name, $settings['message']));
    $phone   = preg_replace('/\D/', '', $settings['number']);
    $color   = esc_attr($settings['color']);
    $text    = esc_html($settings['button_text']);

    echo '<div class="bes-whatsapp-wrap">
        <a href="'.esc_url("https://wa.me/$phone?text=$message").'" target="_blank" class="bes-whatsapp-btn" style="background:'.$color.';">'.$text.'</a>
    </div>';
}

// WooCommerce Hooks
add_action('woocommerce_after_add_to_cart_button', function () {
    $s = get_option('bes_whatsapp_settings', []);
    if (!empty($s['show_single'])) bes_whatsapp_button_output();
});
add_action('woocommerce_after_shop_loop_item', function () {
    $s = get_option('bes_whatsapp_settings', []);
    if (!empty($s['show_archive'])) bes_whatsapp_button_output();
});
