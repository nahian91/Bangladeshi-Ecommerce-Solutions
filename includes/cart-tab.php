<?php
if (!defined('ABSPATH')) exit;

function bes_cart_tab(){
    $cart = wp_parse_args(get_option('bes_cart_settings', []), [
        'show_cross_sell' => true,
        'enable_coupon' => true,
        'cart_message' => '',
        'checkout_button_text' => 'Proceed to Checkout',
    ]);

    settings_fields('bes_cart_group');
    do_settings_sections('bes_cart_group');
    ?>

    <h2>Cart Settings</h2>
    <div class="bes-cart-card">

        <!-- Show Cross-Sells -->
        <div class="bes-cart-field">
            <label class="switch">
                <input type="checkbox" name="bes_cart_settings[show_cross_sell]" <?php checked($cart['show_cross_sell'], true); ?>>
                <span class="slider round"></span> Show Cross-Sells
            </label>
        </div>

        <!-- Enable Coupon -->
        <div class="bes-cart-field">
            <label class="switch">
                <input type="checkbox" name="bes_cart_settings[enable_coupon]" <?php checked($cart['enable_coupon'], true); ?>>
                <span class="slider round"></span> Enable Coupon
            </label>
        </div>

        <!-- Cart Message -->
        <div class="bes-cart-field" style="flex-direction:column; align-items:flex-start;">
            <label>Cart Message:</label>
            <textarea name="bes_cart_settings[cart_message]" placeholder="Cart Message" style="width:100%; max-width:500px; height:100px;"><?php echo esc_textarea($cart['cart_message']); ?></textarea>
        </div>

        <!-- Checkout Button Text -->
        <div class="bes-cart-field" style="flex-direction:column; align-items:flex-start;">
            <label>Checkout Button Text <small class="bes-tooltip">Text displayed on the checkout button.</small></label>
            <input type="text" id="bes_checkout_button_text" name="bes_cart_settings[checkout_button_text]" value="<?php echo esc_attr($cart['checkout_button_text']); ?>" placeholder="Checkout Button Text" style="width:300px; padding:5px; margin-bottom:5px;">
            <div class="bes-preview-button">
                <button class="button button-primary" id="preview_checkout_button"><?php echo esc_html($cart['checkout_button_text']); ?></button> <small>Preview</small>
            </div>
        </div>

    </div>

    <?php submit_button('Save Cart Settings'); ?>
<?php
}
