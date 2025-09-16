<?php
if (!defined('ABSPATH')) exit;

function bes_package_tab() {

    $defaults = [
        'company_name' => '',
        'address' => '',
        'phone' => '',
        'email' => '',
        'footer_text' => '',
        'terms' => '',
        'fields_order' => ['company_name','address','phone','email','footer_text','terms'],
    ];

    $settings = wp_parse_args(get_option('bes_package_settings', []), $defaults);

    $active_sub = isset($_GET['sub_tab']) ? sanitize_text_field($_GET['sub_tab']) : 'settings';
    ?>
    <div class="wrap">
        <h1><?php _e('Package / Invoice','bes'); ?></h1>
        <h2 class="nav-tab-wrapper" style="margin-bottom:20px;">
            <a href="?page=bes-settings&tab=package&sub_tab=settings" class="nav-tab <?php echo $active_sub=='settings'?'nav-tab-active':''; ?>"><?php _e('Settings','bes'); ?></a>
            <a href="?page=bes-settings&tab=package&sub_tab=orders" class="nav-tab <?php echo $active_sub=='orders'?'nav-tab-active':''; ?>"><?php _e('Orders','bes'); ?></a>
        </h2>

        <?php if($active_sub=='settings'): ?>
            <form method="post" action="options.php">
            <?php settings_fields('bes_package_group'); ?>

            <div id="bes-fields-container" style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-top:20px;">
                <?php foreach ($settings['fields_order'] as $field) : ?>
                    <div class="bes-field-item" data-field="<?php echo esc_attr($field); ?>" style="background:#fff;border:1px solid #ddd;border-radius:8px;padding:15px;box-shadow:0 2px 6px rgba(0,0,0,0.05);">
                        <?php switch ($field) :
                            case 'company_name': ?>
                                <label><?php _e('Company Name','bes'); ?></label>
                                <input type="text" name="bes_package_settings[company_name]" value="<?php echo esc_attr($settings['company_name']); ?>" style="width:100%;padding:8px;border:1px solid #ccc;border-radius:4px;">
                            <?php break;

                            case 'address': ?>
                                <label><?php _e('Address','bes'); ?></label>
                                <textarea name="bes_package_settings[address]" style="width:100%;padding:8px;border:1px solid #ccc;border-radius:4px;"><?php echo esc_textarea($settings['address']); ?></textarea>
                            <?php break;

                            case 'phone': ?>
                                <label><?php _e('Phone','bes'); ?></label>
                                <input type="text" name="bes_package_settings[phone]" value="<?php echo esc_attr($settings['phone']); ?>" style="width:100%;padding:8px;border:1px solid #ccc;border-radius:4px;">
                            <?php break;

                            case 'email': ?>
                                <label><?php _e('Email','bes'); ?></label>
                                <input type="text" name="bes_package_settings[email]" value="<?php echo esc_attr($settings['email']); ?>" style="width:100%;padding:8px;border:1px solid #ccc;border-radius:4px;">
                            <?php break;

                            case 'footer_text': ?>
                                <label><?php _e('Footer Text','bes'); ?></label>
                                <textarea name="bes_package_settings[footer_text]" style="width:100%;padding:8px;border:1px solid #ccc;border-radius:4px;"><?php echo esc_textarea($settings['footer_text']); ?></textarea>
                            <?php break;

                            case 'terms': ?>
                                <label><?php _e('Terms & Conditions','bes'); ?></label>
                                <textarea name="bes_package_settings[terms]" style="width:100%;padding:8px;border:1px solid #ccc;border-radius:4px;"><?php echo esc_textarea($settings['terms']); ?></textarea>
                            <?php break;
                        endswitch; ?>
                    </div>
                <?php endforeach; ?>
            </div>
            <br>
            <?php submit_button(__('Save Package/Invoice Settings','bes')); ?>
            </form>

        <?php else: // Orders sub-tab ?>
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th><?php _e('Order','bes'); ?></th>
                        <th><?php _e('Customer','bes'); ?></th>
                        <th><?php _e('Total','bes'); ?></th>
                        <th><?php _e('Status','bes'); ?></th>
                        <th><?php _e('Actions','bes'); ?></th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $orders = wc_get_orders(['limit'=>20,'orderby'=>'date','order'=>'DESC']);
                foreach($orders as $order):
                ?>
                    <tr>
                        <td><?php echo $order->get_order_number(); ?></td>
                        <td><?php echo $order->get_billing_first_name().' '.$order->get_billing_last_name(); ?></td>
                        <td><?php echo wc_price($order->get_total()); ?></td>
                        <td><?php echo wc_get_order_status_name($order->get_status()); ?></td>
                        <td>
                            <button class="button bes-show-invoice" data-id="<?php echo $order->get_id(); ?>" data-type="invoice"><?php _e('Invoice','bes'); ?></button>
                            <button class="button bes-show-invoice" data-id="<?php echo $order->get_id(); ?>" data-type="package"><?php _e('Package','bes'); ?></button>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>

            <script>
            jQuery(document).ready(function($){
                $('.bes-show-invoice').click(function(e){
                    e.preventDefault();
                    var order_id = $(this).data('id');
                    var type = $(this).data('type');
                    var w = window.open('', '_blank');
                    $.post(ajaxurl, {action:'bes_get_invoice_package', order_id:order_id, type:type}, function(response){
                        w.document.open();
                        w.document.write(response);
                        w.document.close();
                    });
                });
            });
            </script>
        <?php endif; ?>
    </div>
<?php
}

// ----------------- AJAX for Professional Invoice/Package -----------------
add_action('wp_ajax_bes_get_invoice_package', function(){
    if(!current_user_can('manage_woocommerce')) wp_die('Unauthorized');

    $order_id = intval($_POST['order_id']);
    $type = sanitize_text_field($_POST['type']);
    $settings = wp_parse_args(get_option('bes_package_settings', []), [
        'company_name'=>'','address'=>'','phone'=>'','email'=>'','footer_text'=>'','terms'=>''
    ]);
    $order = wc_get_order($order_id);
    if(!$order) wp_die('Invalid order');

    // Get WordPress site logo
    $custom_logo_id = get_theme_mod('custom_logo');
    $logo_url = $custom_logo_id ? wp_get_attachment_image_url($custom_logo_id, 'full') : '';

    ob_start(); ?>
    <html>
    <head>
        <title><?php echo $type=='invoice'?'Invoice':'Package'; ?> #<?php echo $order->get_order_number(); ?></title>
        <style>
            body{font-family:Arial,sans-serif;color:#333;margin:0;padding:20px;}
            .header{display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;}
            .header img{max-height:80px;}
            .company-info h2{margin:0;font-size:24px;}
            .company-info p{margin:2px 0;}
            .order-info h3{margin:0;}
            .details{display:flex;justify-content:space-between;margin-bottom:20px;}
            .details div{width:48%;}
            .items{width:100%;border-collapse:collapse;margin-bottom:20px;}
            .items th, .items td{border:1px solid #ccc;padding:8px;}
            .items th{background:#f4f4f4;}
            .footer{margin-top:30px;font-size:12px;color:#555;}
            .print-btn{margin-bottom:20px;padding:8px 12px;background:#0073aa;color:#fff;border:none;border-radius:4px;cursor:pointer;}
            .print-btn:hover{background:#005177;}
        </style>
    </head>
    <body>
        <button onclick="window.print()" class="print-btn">Print</button>
        <div class="header">
            <div class="company-info">
                <?php if($logo_url): ?><img src="<?php echo esc_url($logo_url); ?>" alt="Logo"><?php endif; ?>
                <h2><?php echo esc_html($settings['company_name']); ?></h2>
                <p><?php echo nl2br(esc_html($settings['address'])); ?></p>
                <p><?php echo esc_html($settings['phone']); ?> | <?php echo esc_html($settings['email']); ?></p>
            </div>
            <div class="order-info">
                <h3><?php echo $type=='invoice'?'Invoice':'Package'; ?> #<?php echo $order->get_order_number(); ?></h3>
                <p>Date: <?php echo $order->get_date_created()->date('Y-m-d H:i'); ?></p>
                <p>Status: <?php echo wc_get_order_status_name($order->get_status()); ?></p>
            </div>
        </div>

        <div class="details">
            <div>
                <strong>Billing Address:</strong>
                <p><?php echo nl2br($order->get_formatted_billing_address()); ?></p>
            </div>
            <div>
                <strong>Shipping Address:</strong>
                <p><?php echo nl2br($order->get_formatted_shipping_address()); ?></p>
            </div>
        </div>

        <table class="items">
            <tr>
                <th>Product</th><th>Qty</th><?php if($type=='invoice'): ?><th>Price</th><th>Total</th><?php endif; ?>
            </tr>
            <?php foreach($order->get_items() as $item): ?>
            <tr>
                <td><?php echo $item->get_name(); ?></td>
                <td><?php echo $item->get_quantity(); ?></td>
                <?php if($type=='invoice'): ?>
                    <td><?php echo wc_price($item->get_total()/$item->get_quantity()); ?></td>
                    <td><?php echo wc_price($item->get_total()); ?></td>
                <?php endif; ?>
            </tr>
            <?php endforeach; ?>
        </table>

        <?php if($type=='invoice'): ?>
            <p>Subtotal: <?php echo wc_price($order->get_subtotal()); ?></p>
            <p>Discounts: <?php echo wc_price($order->get_total_discount()); ?></p>
            <p>Tax: <?php echo wc_price($order->get_total_tax()); ?></p>
            <p>Total: <?php echo wc_price($order->get_total()); ?></p>
        <?php endif; ?>

        <div class="footer">
            <p><?php echo nl2br(esc_html($settings['footer_text'])); ?></p>
            <p><?php echo nl2br(esc_html($settings['terms'])); ?></p>
        </div>
    </body>
    </html>
    <?php
    echo ob_get_clean();
    wp_die();
});
