<?php
/**
 * Plugin Name: Bangladesh eCommerce Solution Advanced
 * Description: WooCommerce Checkout & Cart Advanced Settings + Analytics + Delivery Scheduler + Product & Shop Control
 * Version: 1.8.0
 * Author: Abdullah Nahian
 */

if (!defined('ABSPATH')) exit;

// -------------------- Include Tab Files --------------------
foreach (glob(plugin_dir_path(__FILE__) . "includes/*.php") as $tab_file) {
    include_once $tab_file;
}

// -------------------- Register Options --------------------
add_action('admin_init', function() {
    register_setting('bes_general_group', 'bes_general_settings');
    register_setting('bes_checkout_group', 'bes_checkout_fields');
    register_setting('bes_cart_group', 'bes_cart_settings');
    register_setting('bes_delivery_group', 'bes_delivery_scheduler');
    register_setting('bes_package_group', 'bes_package_settings');
    register_setting('bes_marketing_group', 'bes_marketing_settings');
    register_setting('bes_whatsapp_group', 'bes_whatsapp_settings');
    register_setting('bes_district_group', 'bes_district_settings');
    register_setting('bes_thankyou_group', 'bes_thankyou_settings');
    register_setting('bes_reports_group', 'bes_reports_settings');
});

// -------------------- Plugin Activation Defaults --------------------
register_activation_hook(__FILE__, function(){
    if(!get_option('bes_general_settings')){
        $defaults = [
            'checkout'=>1,'cart'=>1,'delivery'=>1,'package'=>1,'marketing'=>1,'whatsapp'=>1,
            'district'=>1,'thankyou'=>1,'reports'=>1,'system'=>1,'media_check'=>1,
            'product'=>1,'shop'=>1
        ];
        update_option('bes_general_settings', $defaults);
    }
});

// -------------------- Admin Menu --------------------
add_action('admin_menu','bes_add_main_menu');
function bes_add_main_menu(){
    add_menu_page(
        'BES Settings',
        'BES Settings',
        'manage_options',
        'bes-settings',
        'bes_settings_page',
        'dashicons-admin-tools',
        56
    );
}

// -------------------- Admin Assets (Global + Tab-wise) --------------------
add_action('admin_enqueue_scripts', function($hook){
    if($hook !== 'toplevel_page_bes-settings') return;

    $active_tab = isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : 'general';

    // Global admin CSS/JS
    wp_enqueue_style(
        'bes-admin-css',
        plugin_dir_url(__FILE__).'assets/css/bes-admin.css',
        [],
        '1.8.0'
    );

    wp_enqueue_script(
        'bes-admin-js',
        plugin_dir_url(__FILE__).'assets/js/bes-admin.js',
        ['jquery'],
        '1.8.0',
        true
    );

    wp_localize_script('bes-admin-js', 'besSettings', [
        'activeTab' => $active_tab
    ]);

    // Tab-specific CSS
    $css_file_path = plugin_dir_path(__FILE__)."assets/css/bes-{$active_tab}.css";
    if(file_exists($css_file_path)){
        wp_enqueue_style(
            "bes-{$active_tab}-css",
            plugin_dir_url(__FILE__)."assets/css/bes-{$active_tab}.css",
            [],
            '1.8.0'
        );
    }

    // Tab-specific JS
    $js_file_path = plugin_dir_path(__FILE__)."assets/js/bes-{$active_tab}.js";
    if(file_exists($js_file_path)){
        wp_enqueue_script(
            "bes-{$active_tab}-js",
            plugin_dir_url(__FILE__)."assets/js/bes-{$active_tab}.js",
            ['jquery'],
            '1.8.0',
            true
        );
        wp_localize_script("bes-{$active_tab}-js", 'besSettings', [
            'activeTab' => $active_tab
        ]);
    }
});

// -------------------- Settings Page --------------------
function bes_settings_page(){
    if(!current_user_can('manage_options')) return;

    $active_tab = isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : 'general';
    $general = get_option('bes_general_settings', []);

    echo '<div class="wrap"><h1>BES Settings</h1>';

    // Tabs
    echo '<h2 class="nav-tab-wrapper">';
    $tabs = [
        'general'=>'General',
        'checkout'=>'Checkout',
        'cart'=>'Cart',
        'delivery'=>'Delivery',
        'package'=>'Package/Invoice',
        'marketing'=>'Marketing',
        'whatsapp'=>'WhatsApp',
        'district'=>'District/Upazilla',
        'thankyou'=>'Thank You Messages',
        'reports'=>'Reports',
        'system'=>'System Info',
        'media_check'=>'Image/Video Check',
        'product'=>'Product',
        'shop'=>'Shop'
    ];

    foreach($tabs as $key=>$label){
        if($key=='general' || !isset($general[$key]) || $general[$key]){
            echo '<a href="?page=bes-settings&tab='.$key.'" class="nav-tab '.($active_tab==$key?'nav-tab-active':'').'">'.$label.'</a>';
        }
    }
    echo '</h2>';

    // Form
    echo '<form method="post" action="options.php">';
    switch($active_tab){
        case 'general': settings_fields('bes_general_group'); bes_general_tab(); break;
        case 'checkout': settings_fields('bes_checkout_group'); bes_checkout_tab(); break;
        case 'cart': settings_fields('bes_cart_group'); bes_cart_tab(); break;
        case 'delivery': settings_fields('bes_delivery_group'); bes_delivery_tab(); break;
        case 'package': settings_fields('bes_package_group'); bes_package_tab(); break;
        case 'marketing': settings_fields('bes_marketing_group'); bes_marketing_tab(); break;
        case 'whatsapp': settings_fields('bes_whatsapp_group'); bes_whatsapp_tab(); break;
        case 'district': settings_fields('bes_district_group'); bes_district_tab(); break;
        case 'thankyou': settings_fields('bes_thankyou_group'); bes_thankyou_tab(); break;
        case 'reports': settings_fields('bes_reports_group'); bes_reports_tab(); break;
        case 'system': bes_system_info_tab(); break;
        case 'media_check': bes_media_check_tab(); break;
        case 'product': bes_product_tab(); break;
        case 'shop': bes_shop_tab(); break;
    }
    echo '</form></div>';
}

// -------------------- Admin Notices --------------------
add_action('admin_notices', function(){
    if(isset($_GET['settings-updated']) && $_GET['settings-updated']==='true' && isset($_GET['tab'])){
        $tab = sanitize_text_field($_GET['tab']);
        $messages = [
            'general'=>'General settings saved successfully!',
            'checkout'=>'Checkout settings saved successfully!',
            'cart'=>'Cart settings saved successfully!',
            'delivery'=>'Delivery settings saved successfully!',
            'package'=>'Package/Invoice settings saved successfully!',
            'marketing'=>'Marketing settings saved successfully!',
            'whatsapp'=>'WhatsApp settings saved successfully!',
            'district'=>'District settings saved successfully!',
            'thankyou'=>'Thank You Messages settings saved successfully!',
            'reports'=>'Reports settings saved successfully!',
            'system'=>'System Info settings saved successfully!',
            'media_check'=>'Image/Video Check settings saved successfully!',
            'product'=>'Product settings saved successfully!',
            'shop'=>'Shop settings saved successfully!'
        ];
        if(isset($messages[$tab])){
            echo '<div class="notice notice-success is-dismissible"><p>'.$messages[$tab].'</p></div>';
        }
    }
});
