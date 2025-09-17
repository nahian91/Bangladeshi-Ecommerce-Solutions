<?php
if ( ! defined( 'ABSPATH' ) ) exit;

function bes_product_tab() {
    $defaults = [
        'show_sku'           => 1,
        'show_stock'         => 1,
        'show_categories'    => 1,
        'show_tags'          => 0,
        'disable_price'      => 0,
        'disable_add_to_cart'=> 0,
        'disable_variations' => 0,
        'show_reviews'       => 1,
        'custom_label'       => ''
    ];
    $settings = wp_parse_args( get_option( 'bes_product_settings', [] ), $defaults );

    $checkboxes = [
        'show_sku'            => 'Show SKU',
        'show_stock'          => 'Show Stock Status',
        'show_categories'     => 'Show Categories',
        'show_tags'           => 'Show Tags',
        'disable_price'       => 'Hide Product Price',
        'disable_add_to_cart' => 'Disable Add to Cart Button',
        'disable_variations'  => 'Disable Product Variations',
        'show_reviews'        => 'Show Reviews'
    ];
    ?>
    <style>
        .bes-product-card {
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 12px;
            padding: 25px;
            max-width: 500px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            font-family: inherit;
        }
        .bes-product-card h2 { margin-top: 0; font-size: 22px; }
        .bes-product-card p { font-size: 14px; color: #555; margin-bottom: 20px; }
        .bes-product-field {
            margin-bottom: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
        }
        .bes-product-field input[type=text] {
            width: 100%;
            max-width: 400px;
            padding: 7px 10px;
            border-radius: 6px;
            border: 1px solid #ccc;
        }
        /* Toggle Switch */
        .switch { position: relative; display: inline-block; width: 50px; height: 24px; }
        .switch input { opacity: 0; width: 0; height: 0; }
        .slider {
            position: absolute;
            cursor: pointer;
            top: 0; left: 0; right: 0; bottom: 0;
            background-color: #ccc;
            transition: .3s;
            border-radius: 24px;
        }
        .slider:before {
            position: absolute;
            content: "";
            height: 18px;
            width: 18px;
            left: 3px;
            bottom: 3px;
            background-color: #fff;
            transition: .3s;
            border-radius: 50%;
        }
        input:checked + .slider { background-color: #007cba; }
        input:checked + .slider:before { transform: translateX(26px); }
    </style>

    <div class="bes-product-card">
        <h2><?php esc_html_e( 'Product Settings', 'bangladeshi-payments-mobile' ); ?></h2>
        <p><?php esc_html_e( 'Control what appears on your product pages.', 'bangladeshi-payments-mobile' ); ?></p>

        <form method="post" action="options.php">
            <?php settings_fields( 'bes_product_group' ); ?>

            <?php foreach ( $checkboxes as $key => $label ) :
                $val = ! empty( $settings[ $key ] ) ? 1 : 0; ?>
                <div class="bes-product-field">
                    <span><?php echo esc_html( $label ); ?></span>
                    <label class="switch">
                        <input type="hidden" name="bes_product_settings[<?php echo esc_attr( $key ); ?>]" value="0">
                        <input type="checkbox" name="bes_product_settings[<?php echo esc_attr( $key ); ?>]" value="1" <?php checked( $val, 1 ); ?>>
                        <span class="slider"></span>
                    </label>
                </div>
            <?php endforeach; ?>

            <div class="bes-product-field">
                <label><?php esc_html_e( 'Custom Label/Text for Product Page:', 'bangladeshi-payments-mobile' ); ?></label>
                <input type="text" name="bes_product_settings[custom_label]" value="<?php echo esc_attr( $settings['custom_label'] ); ?>">
            </div>

            <?php submit_button( __( 'Save Product Settings', 'bangladeshi-payments-mobile' ) ); ?>
        </form>
    </div>
    <?php
}
