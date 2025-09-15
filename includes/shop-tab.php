<?php
if (!defined('ABSPATH')) exit;

// 1️⃣ Register settings
add_action('admin_init', function() {
    register_setting('bes_shop_group', 'bes_shop_settings');
});

// 2️⃣ Shop Settings Card
function bes_shop_tab() {
    $defaults = [
        'add_to_cart_text'    => 'Add to Cart',
        'show_total_products' => 1,
        'products_per_page'   => 12,
    ];
    $settings = wp_parse_args(get_option('bes_shop_settings', []), $defaults);
    ?>
    <style>
        .bes-shop-card { background:#fff; border:1px solid #ddd; border-radius:12px; padding:20px; max-width:500px; box-shadow:0 2px 8px rgba(0,0,0,0.05); font-family:inherit; }
        .bes-shop-field { margin-bottom:15px; display:flex; justify-content:space-between; align-items:center; }
        .bes-text, .bes-number { padding:6px 10px; border:1px solid #ccc; border-radius:6px; width:200px; }
    </style>

    <div class="bes-shop-card">
        <h2>Shop Settings</h2>
        <form method="post" action="options.php">
            <?php settings_fields('bes_shop_group'); ?>

            <div class="bes-shop-field">
                <span>Add to Cart Button Text</span>
                <input type="text" class="bes-text" name="bes_shop_settings[add_to_cart_text]" value="<?php echo esc_attr($settings['add_to_cart_text']); ?>">
            </div>

            <div class="bes-shop-field">
                <span>Show Total Products</span>
                <input type="checkbox" name="bes_shop_settings[show_total_products]" value="1" <?php checked($settings['show_total_products'],1); ?>>
            </div>

            <div class="bes-shop-field">
                <span>Products Per Page</span>
                <input type="number" min="1" class="bes-number" name="bes_shop_settings[products_per_page]" value="<?php echo esc_attr($settings['products_per_page']); ?>">
            </div>

            <?php submit_button('Save Settings'); ?>
        </form>
    </div>
    <?php
}

// 3️⃣ Change Add to Cart text
add_filter('woocommerce_product_add_to_cart_text', function($text, $product){
    $settings = get_option('bes_shop_settings', []);
    if(!empty($settings['add_to_cart_text'])){
        return esc_html($settings['add_to_cart_text']);
    }
    return $text;
}, 10, 2);

// 4️⃣ Show total products above loop
add_action('woocommerce_before_shop_loop', function(){
    $settings = get_option('bes_shop_settings', []);
    if(!empty($settings['show_total_products'])){
        $total = wc_get_loop_prop('total');
        echo '<p>Total Products: <strong>'.$total.'</strong></p>';
    }
});

// 5️⃣ Products per page
add_filter('loop_shop_per_page', function($per_page){
    $settings = get_option('bes_shop_settings', []);
    if(!empty($settings['products_per_page'])){
        return intval($settings['products_per_page']);
    }
    return $per_page;
}, 20);
