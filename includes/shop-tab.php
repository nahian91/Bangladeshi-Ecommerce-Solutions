<?php
if (!defined('ABSPATH')) exit;

// 1️⃣ Register Option
add_action('admin_init', function() {
    register_setting('bcaw_shop_group', 'bcaw_shop_settings', [
        'type' => 'array',
        'sanitize_callback' => function($input){
            return [
                'add_to_cart_text'       => sanitize_text_field($input['add_to_cart_text'] ?? 'Add to Cart'),
                'products_per_page'      => max(1,intval($input['products_per_page'] ?? 12)),
                'show_price'             => !empty($input['show_price']) ? 1 : 0,
                'show_add_to_cart'       => !empty($input['show_add_to_cart']) ? 1 : 0,
                'show_sorting'           => !empty($input['show_sorting']) ? 1 : 0,
                'show_filters'           => !empty($input['show_filters']) ? 1 : 0,
                'show_result_count'      => !empty($input['show_result_count']) ? 1 : 0,
            ];
        },
    ]);
});

// 2️⃣ Admin Settings with Switches
function bcaw_shop_tab() {
    $defaults = [
        'add_to_cart_text' => 'Add to Cart',
        'products_per_page'=> 12,
        'show_price'       => 1,
        'show_add_to_cart' => 1,
        'show_sorting'     => 1,
        'show_filters'     => 1,
        'show_result_count'=> 1,
    ];
    $settings = wp_parse_args(get_option('bcaw_shop_settings', []), $defaults);
    ?>
    <div class="bcaw-shop-card" style="background:#fff;padding:20px;border-radius:12px;max-width:700px;">
        <h2>Shop Settings</h2>
        <form method="post" action="options.php">
            <?php settings_fields('bcaw_shop_group'); ?>

            <p>
                <label>Add to Cart Button Text</label><br>
                <input type="text" name="bcaw_shop_settings[add_to_cart_text]" value="<?php echo esc_attr($settings['add_to_cart_text']); ?>">
            </p>

            <p>
                <label>Products Per Page</label><br>
                <input type="number" min="1" name="bcaw_shop_settings[products_per_page]" value="<?php echo esc_attr($settings['products_per_page']); ?>">
            </p>

            <style>
                .bcaw-switch { position: relative; display: inline-block; width: 50px; height: 24px; }
                .bcaw-switch input { opacity: 0; width: 0; height: 0; }
                .bcaw-slider { position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0;
                                background-color: #ccc; transition: .4s; border-radius: 24px; }
                .bcaw-slider:before { position: absolute; content: ""; height: 18px; width: 18px; left: 3px; bottom: 3px;
                                      background-color: white; transition: .4s; border-radius: 50%; }
                input:checked + .bcaw-slider { background-color: #0073aa; }
                input:checked + .bcaw-slider:before { transform: translateX(26px); }
            </style>

            <?php 
            $switches = [
                'show_price' => 'Show Price',
                'show_add_to_cart' => 'Show Add to Cart Button',
                'show_sorting' => 'Show Sorting Dropdown',
                'show_filters' => 'Show Filters',
                'show_result_count' => 'Show Result Count',
            ];
            foreach ($switches as $key => $label): ?>
                <p>
                    <label><?php echo esc_html($label); ?></label>
                    <label class="bcaw-switch">
                        <input type="checkbox" name="bcaw_shop_settings[<?php echo esc_attr($key); ?>]" value="1" <?php checked($settings[$key],1); ?>>
                        <span class="bcaw-slider"></span>
                    </label>
                </p>
            <?php endforeach; ?>

            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}

// 3️⃣ Frontend Hooks
add_filter('loop_shop_per_page', function($per_page){
    $opts = get_option('bcaw_shop_settings', []);
    return intval($opts['products_per_page'] ?? $per_page);
}, 20);

add_filter('woocommerce_product_add_to_cart_text', function($text){
    $opts = get_option('bcaw_shop_settings', []);
    return $opts['add_to_cart_text'] ?? $text;
});

add_filter('woocommerce_loop_add_to_cart_link', function($button_html, $product){
    $opts = get_option('bcaw_shop_settings', []);
    if(empty($opts['show_add_to_cart'])) return '';
    return $button_html;
}, 10, 2);

add_filter('woocommerce_get_price_html', function($price, $product){
    $opts = get_option('bcaw_shop_settings', []);
    if(empty($opts['show_price'])) return '';
    return $price;
}, 10, 2);

add_action('wp_head', function(){
    $opts = get_option('bcaw_shop_settings', []);
    ?>
    <style>
        <?php if(empty($opts['show_sorting'])): ?>
            .woocommerce-ordering, .woocommerce-product-blocks-orderby{display:none !important;}
        <?php endif; ?>
        <?php if(empty($opts['show_result_count'])): ?>
            .woocommerce-result-count, .wc-block-components-result-count{display:none !important;}
        <?php endif; ?>
        <?php if(empty($opts['show_filters'])): ?>
            .widget_layered_nav, .widget_price_filter, .wp-block-woocommerce-product-filters{display:none !important;}
        <?php endif; ?>
    </style>
    <?php
});
