<?php
if (!defined('ABSPATH')) exit;

function bes_cart_tab(){
    // Get saved settings or defaults
    $cart = get_option('bes_cart_settings', [
        'show_cross_sell'=>true,
        'enable_coupon'=>true,
        'cart_message'=>'',
        'checkout_button_text'=>'Proceed to Checkout',
    ]);

    // Ensure defaults exist
    $cart = wp_parse_args($cart, [
        'show_cross_sell'=>true,
        'enable_coupon'=>true,
        'cart_message'=>'',
        'checkout_button_text'=>'Proceed to Checkout',
    ]);

    settings_fields('bes_cart_group');
    do_settings_sections('bes_cart_group');

    echo '<h2>Cart Settings</h2>';
    echo '<div class="bes-cart-card">';

    // Show Cross-Sells
    echo '<div class="bes-cart-field">';
    echo '<label class="switch">';
    echo '<input type="checkbox" name="bes_cart_settings[show_cross_sell]" '.checked($cart['show_cross_sell'],true,false).'>';
    echo '<span class="slider round"></span> Show Cross-Sells';
    echo '</label>';
    echo '</div>';

    // Enable Coupon
    echo '<div class="bes-cart-field">';
    echo '<label class="switch">';
    echo '<input type="checkbox" name="bes_cart_settings[enable_coupon]" '.checked($cart['enable_coupon'],true,false).'>';
    echo '<span class="slider round"></span> Enable Coupon';
    echo '</label>';
    echo '</div>';

    // Cart Message
    echo '<div class="bes-cart-field" style="flex-direction:column;align-items:flex-start;">';
    echo '<label>Cart Message:</label>';
    echo '<textarea name="bes_cart_settings[cart_message]" placeholder="Cart Message" style="width:100%;max-width:500px;height:100px;">'.esc_textarea($cart['cart_message']).'</textarea>';
    echo '</div>';

    // Checkout Button Text
    echo '<div class="bes-cart-field" style="flex-direction:column;align-items:flex-start;">';
    echo '<label>Checkout Button Text <small class="bes-tooltip">Text displayed on the checkout button.</small></label>';
    echo '<input type="text" id="bes_checkout_button_text" name="bes_cart_settings[checkout_button_text]" value="'.esc_attr($cart['checkout_button_text']).'" placeholder="Checkout Button Text" style="width:300px;padding:5px;margin-bottom:5px;">';
    echo '<div class="bes-preview-button"><button class="button button-primary" id="preview_checkout_button">'.esc_html($cart['checkout_button_text']).'</button> <small>Preview</small></div>';
    echo '</div>';

    echo '</div>';

    submit_button('Save Cart Settings');
    ?>

    <style>
        .bes-cart-card { border:1px solid #ddd; border-radius:5px; padding:15px; max-width:600px; background:#fafafa; }
        .bes-cart-field { margin-bottom:15px; display:flex; align-items:center; gap:10px; flex-wrap:wrap; }
        textarea, input[type=text] { padding:5px; font-size:14px; }
        .bes-preview-button button { cursor: default; }

        /* Switch Styles */
        .switch { position: relative; display: inline-block; width:40px; height:20px; }
        .switch input { display:none; }
        .slider { position:absolute; cursor:pointer; top:0; left:0; right:0; bottom:0; background:#ccc; transition:.4s; border-radius:20px; }
        .slider:before { position:absolute; content:""; height:16px; width:16px; left:2px; bottom:2px; background:white; transition:.4s; border-radius:50%; }
        input:checked + .slider { background:#4caf50; }
        input:checked + .slider:before { transform:translateX(20px); }
    </style>

    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script>
        jQuery(document).ready(function($){
            // Live preview of checkout button
            $('#bes_checkout_button_text').on('input', function(){
                $('#preview_checkout_button').text($(this).val());
            });
        });
    </script>
    <?php
}

// Admin notice for Cart Settings Saved
add_action('admin_notices', function(){
    if (isset($_GET['settings-updated']) && $_GET['settings-updated'] === 'true') {
        echo '<div class="notice notice-success is-dismissible"><p>Cart settings saved successfully!</p></div>';
    }
});
