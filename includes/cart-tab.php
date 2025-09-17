<?php
if (!defined('ABSPATH')) exit;

function bcaw_cart_tab(){
    $cart = wp_parse_args(get_option('bcaw_cart_settings', []), [
        'show_cross_sell' => true,
        'enable_coupon' => true,
        'cart_message' => '',
        'checkout_button_text' => 'Proceed to Checkout',
    ]);

    settings_fields('bcaw_cart_group');
    do_settings_sections('bcaw_cart_group');
    ?>

    <h2 style="margin-bottom:20px;"><?php esc_html_e('Cart Settings', 'banglacommerce-all-in-one-woocommerce'); ?></h2>

    <div class="bcaw-cart-card" style="max-width:800px; padding:20px; background:#fff; border-radius:10px; box-shadow:0 2px 10px rgba(0,0,0,0.1);">

        <!-- Show Cross-Sells -->
        <div class="bcaw-cart-field" style="margin-bottom:20px; display:flex; align-items:center; justify-content:space-between;">
            <label><?php esc_html_e('Show Cross-Sells', 'banglacommerce-all-in-one-woocommerce'); ?></label>
            <label class="bcaw-switch">
                <input type="checkbox" name="bcaw_cart_settings[show_cross_sell]" <?php checked($cart['show_cross_sell'], true); ?>>
                <span class="bcaw-slider round"></span>
            </label>
        </div>

        <!-- Enable Coupon -->
        <div class="bcaw-cart-field" style="margin-bottom:20px; display:flex; align-items:center; justify-content:space-between;">
            <label><?php esc_html_e('Enable Coupon', 'banglacommerce-all-in-one-woocommerce'); ?></label>
            <label class="bcaw-switch">
                <input type="checkbox" name="bcaw_cart_settings[enable_coupon]" <?php checked($cart['enable_coupon'], true); ?>>
                <span class="bcaw-slider round"></span>
            </label>
        </div>

        <!-- Cart Message -->
        <div class="bcaw-cart-field" style="margin-bottom:20px; display:flex; flex-direction:column;">
            <label><?php esc_html_e('Cart Message:', 'banglacommerce-all-in-one-woocommerce'); ?></label>
            <textarea name="bcaw_cart_settings[cart_message]" placeholder="Enter a message to display on the cart page" style="width:100%; padding:10px; border-radius:5px; border:1px solid #ccc; min-height:100px;"><?php echo esc_textarea($cart['cart_message']); ?></textarea>
        </div>

        <!-- Checkout Button Text -->
        <div class="bcaw-cart-field" style="margin-bottom:20px; display:flex; flex-direction:column;">
            <label>
                <?php esc_html_e('Checkout Button Text', 'banglacommerce-all-in-one-woocommerce'); ?> 
                <small class="bcaw-tooltip" title="Text displayed on the checkout button.">(?)</small>
            </label>
            <input type="text" id="bcaw_checkout_button_text" name="bcaw_cart_settings[checkout_button_text]" value="<?php echo esc_attr($cart['checkout_button_text']); ?>" placeholder="Checkout Button Text" style="width:300px; padding:8px; border-radius:5px; border:1px solid #ccc; margin-bottom:10px;">
            
            <div class="bcaw-preview-button" style="margin-top:5px;">
                <button class="button button-primary" id="bcaw_preview_checkout_button"><?php echo esc_html($cart['checkout_button_text']); ?></button> 
                <small style="margin-left:10px; color:#555;"><?php esc_html_e('Preview', 'banglacommerce-all-in-one-woocommerce'); ?></small>
            </div>
        </div>

    </div>

    <?php submit_button(__('Save Cart Settings', 'banglacommerce-all-in-one-woocommerce')); ?>

    <!-- CSS for Switch -->
    <style>
        .bcaw-switch {
            position: relative;
            display: inline-block;
            width: 50px;
            height: 24px;
        }
        .bcaw-switch input { display:none; }
        .bcaw-slider {
            position: absolute;
            cursor: pointer;
            top:0; left:0; right:0; bottom:0;
            background-color:#ccc;
            transition:.4s;
            border-radius:24px;
        }
        .bcaw-slider:before {
            position: absolute;
            content:"";
            height:20px;
            width:20px;
            left:2px;
            bottom:2px;
            background-color:white;
            transition:.4s;
            border-radius:50%;
        }
        .bcaw-switch input:checked + .bcaw-slider { background-color:#007cba; }
        .bcaw-switch input:checked + .bcaw-slider:before { transform: translateX(26px); }
        .bcaw-tooltip { cursor: help; font-size:12px; color:#888; }
    </style>

    <!-- JS for Live Preview -->
    <script>
        document.addEventListener('DOMContentLoaded', function(){
            const input = document.getElementById('bcaw_checkout_button_text');
            const preview = document.getElementById('bcaw_preview_checkout_button');
            input.addEventListener('input', function(){
                preview.textContent = input.value || '<?php echo esc_js($cart['checkout_button_text']); ?>';
            });
        });
    </script>

<?php
}
