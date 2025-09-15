<?php
if (!defined('ABSPATH')) {
    exit;
}

class WC_Gateway_bKash extends WC_Payment_Gateway {

    public function __construct() {
        $this->id = 'bkash'; 
        $this->icon = ''; 
        $this->has_fields = true; 
        $this->method_title = __('bKash', 'bangladeshi-payments-mobile');
        $this->method_description = __('Pay via bKash by entering your phone number and transaction ID.', 'bangladeshi-payments-mobile');
        
        // Load the settings
        $this->init_form_fields();
        $this->init_settings();

        $this->title = $this->get_option('title');
        $this->description = $this->get_option('description');
        $this->icon = plugins_url( 'img/bkash.png', __FILE__ );
        $this->account_type = $this->get_option('account_type'); 
        $this->account_number = $this->get_option('account_number'); 
        $this->bkash_charge = $this->get_option('bkash_charge'); 

        add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'process_admin_options'));
    }

    // Admin settings fields
    public function init_form_fields() {
        $this->form_fields = array(
            'enabled' => array(
                'title' => __('Enable/Disable', 'bangladeshi-payments-mobile'),
                'type' => 'checkbox',
                'label' => __('Enable bKash Payment', 'bangladeshi-payments-mobile'),
                'default' => 'no',
            ),
            'title' => array(
                'title' => __('Title', 'bangladeshi-payments-mobile'),
                'type' => 'text',
                'description' => __('This controls the title the user sees during checkout.', 'bangladeshi-payments-mobile'),
                'default' => __('bKash', 'bangladeshi-payments-mobile'),
                'desc_tip' => true,
            ),
            'description' => array(
                'title' => __('Description', 'bangladeshi-payments-mobile'),
                'type' => 'textarea',
                'description' => __('Payment method description that the customer will see during checkout.', 'bangladeshi-payments-mobile'),
                'default' => __('Pay with bKash. Enter your bKash phone number and transaction ID.', 'bangladeshi-payments-mobile'),
            ),
            'account_type' => array(
                'title' => __('Account Type', 'bangladeshi-payments-mobile'),
                'type' => 'select',
                'options' => array(
                    'personal' => __('Personal', 'bangladeshi-payments-mobile'),
                    'agent' => __('Agent', 'bangladeshi-payments-mobile'),
                ),
                'description' => __('Select the type of account used for bKash transactions.', 'bangladeshi-payments-mobile'),
                'default' => 'personal',
            ),
            'account_number' => array(
                'title' => __('Account Number', 'bangladeshi-payments-mobile'),
                'type' => 'text',
                'description' => __('Enter the account number for bKash transactions.', 'bangladeshi-payments-mobile'),
                'default' => '',
                'required' => true,
            ),
            'apply_bkash_charge' => array(
                'title' => __('Apply bKash Charge', 'bangladeshi-payments-mobile'),
                'type' => 'checkbox',
                'label' => __('Apply bKash charge to total payment?', 'bangladeshi-payments-mobile'),
                'default' => 'yes',
            ),
            'bkash_charge' => array(
                'title' => __('bKash Charge (%)', 'bangladeshi-payments-mobile'),
                'type' => 'number',
                'description' => __('Enter the bKash charge as a percentage (e.g., 1.4 for 1.4%).', 'bangladeshi-payments-mobile'),
                'default' => '1.4',
                'custom_attributes' => array(
                    'step' => '0.01',
                ),
            ),
        );
    }
    
    public function payment_fields() {
        // Translators: %1$s is the total payment amount. %2$s is the bKash fees amount.
        echo '<p>' . sprintf(esc_html__('You need to send us %1$s (Fees %2$s)', 'bangladeshi-payments-mobile'), esc_html($this->calculate_total_payment()), esc_html($this->calculate_bkash_fees())) . '</p>';
        echo '<p>' . esc_html($this->description) . '</p>';

        // Show Account Type and Number
        echo '<p><strong>' . esc_html__('Account Type: ', 'bangladeshi-payments-mobile') . '</strong>' . esc_html(ucfirst($this->account_type)) . '</p>';
        echo '<p><strong>' . esc_html__('Account Number: ', 'bangladeshi-payments-mobile') . '</strong>' . esc_html($this->account_number) . '</p>';

        
        
        echo '<div>
                <label for="bkash_phone">' . esc_html__('bKash Phone Number', 'bangladeshi-payments-mobile') . ' <span class="required">*</span></label>
                <input type="text" name="bkash_phone" id="bkash_phone" placeholder="' . esc_attr__('01XXXXXXXXX', 'bangladeshi-payments-mobile') . '" required>
            </div>';
        echo '<div>
                <label for="bkash_transaction_id">' . esc_html__('bKash Transaction ID', 'bangladeshi-payments-mobile') . ' <span class="required">*</span></label>
                <input type="text" name="bkash_transaction_id" id="bkash_transaction_id" placeholder="' . esc_attr__('Transaction ID', 'bangladeshi-payments-mobile') . '" required>
            </div>';
        echo '<input type="hidden" name="bkash_nonce" value="' . esc_attr(wp_create_nonce('bkash_payment_nonce')) . '">';
    }

    // Calculate total payment based on order total and bKash charge
    private function calculate_total_payment() {
        global $woocommerce;
        $order_total = $woocommerce->cart->total; 
        $bkash_charge_percentage = $this->get_option('bkash_charge');
        
        // Check if the charge should be applied
        $apply_bkash_charge = $this->get_option('apply_bkash_charge') === 'yes';

        $bkash_fee = $apply_bkash_charge ? ($order_total * ($bkash_charge_percentage / 100)) : 0;
        $total_payment = $order_total + $bkash_fee;

        return number_format($total_payment, 2) . ' BDT';
    }

    // Calculate bKash fees
    private function calculate_bkash_fees() {
        global $woocommerce;
        $order_total = $woocommerce->cart->total; 
        $bkash_charge_percentage = $this->get_option('bkash_charge');

        // Check if the charge should be applied
        $apply_bkash_charge = $this->get_option('apply_bkash_charge') === 'yes';

        $bkash_fee = $apply_bkash_charge ? ($order_total * ($bkash_charge_percentage / 100)) : 0;
        return number_format($bkash_fee, 2) . ' BDT';
    }

    // Validate bkash fields (checkout)
    public function validate_fields() {
        if (isset($_POST['bkash_nonce'])) {
            $nonce = sanitize_text_field(wp_unslash($_POST['bkash_nonce']));
            if (!wp_verify_nonce($nonce, 'bkash_payment_nonce')) {
                wc_add_notice(__('Nonce verification failed.', 'bangladeshi-payments-mobile'), 'error');
                return false;
            }
        } else {
            wc_add_notice(__('Nonce is missing.', 'bangladeshi-payments-mobile'), 'error');
            return false;
        }

        // Check for bkash phone number
        if (isset($_POST['bkash_phone']) && !empty($_POST['bkash_phone'])) {
            $bkash_phone = sanitize_text_field(wp_unslash($_POST['bkash_phone']));
            if (!preg_match('/^01[0-9]{9}$/', $bkash_phone)) {
                wc_add_notice(__('Please enter a valid bkash phone number.', 'bangladeshi-payments-mobile'), 'error');
                return false;
            }
        } else {
            wc_add_notice(__('bkash phone number is required.', 'bangladeshi-payments-mobile'), 'error');
            return false;
        }

        // Check for bkash transaction ID
        if (isset($_POST['bkash_transaction_id']) && !empty($_POST['bkash_transaction_id'])) {
            $bkash_transaction_id = sanitize_text_field(wp_unslash($_POST['bkash_transaction_id']));
            if (empty($bkash_transaction_id)) {
                wc_add_notice(__('Please enter your bkash transaction ID.', 'bangladeshi-payments-mobile'), 'error');
                return false;
            }
        } else {
            wc_add_notice(__('bkash transaction ID is required.', 'bangladeshi-payments-mobile'), 'error');
            return false;
        }
        return true;
    }

    // Process the payment (checkout)
    public function process_payment($order_id) {
        if (!isset($_POST['bkash_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['bkash_nonce'])), 'bkash_payment_nonce')) {
            wc_add_notice(__('Nonce verification failed.', 'bangladeshi-payments-mobile'), 'error');
            return false;
        }

        // Check for bKash phone number
        if (isset($_POST['bkash_phone']) && !empty($_POST['bkash_phone'])) {
            $bkash_phone = sanitize_text_field(wp_unslash($_POST['bkash_phone']));
            if (!preg_match('/^01[0-9]{9}$/', $bkash_phone)) {
                wc_add_notice(__('Please enter a valid bKash phone number starting with 01 and containing 11 digits.', 'bangladeshi-payments-mobile'), 'error');
                return false;
            }
        } else {
            wc_add_notice(__('bKash phone number is required.', 'bangladeshi-payments-mobile'), 'error');
            return false;
        }


        // Check for bkash transaction ID
        if (isset($_POST['bkash_transaction_id'])) {
            $bkash_transaction_id = sanitize_text_field(wp_unslash($_POST['bkash_transaction_id']));
        } else {
            wc_add_notice(__('bkash transaction ID is required.', 'bangladeshi-payments-mobile'), 'error');
            return false;
        }

        $order = wc_get_order($order_id);
        
        update_post_meta($order_id, '_bkash_phone', $bkash_phone);
        update_post_meta($order_id, '_bkash_transaction_id', $bkash_transaction_id);
        
        $order->update_status('on-hold', __('Waiting for bkash payment confirmation.', 'bangladeshi-payments-mobile'));

        wc_reduce_stock_levels($order_id);
        WC()->cart->empty_cart();

        return array(
            'result' => 'success',
            'redirect' => $this->get_return_url($order),
        );
    }


    // Display bkash information on the order page
    public function display_bkash_info_on_order($order_id) {
        $bkash_phone = get_post_meta($order_id, '_bkash_phone', true);
        $bkash_transaction_id = get_post_meta($order_id, '_bkash_transaction_id', true);
        
            if ($bkash_phone || $bkash_transaction_id) {
                echo '<h3>' . esc_html('bkash Payment Information', 'bangladeshi-payments-mobile') . '</h3>';
                echo '<p><strong>' . esc_html('Phone Number:', 'bangladeshi-payments-mobile') . '</strong> ' . esc_html($bkash_phone) . '</p>';
                echo '<p><strong>' . esc_html('Transaction ID:', 'bangladeshi-payments-mobile') . '</strong> ' . esc_html($bkash_transaction_id) . '</p>';
            }
        }
    }

// Add the gateway to WooCommerce
add_filter('woocommerce_payment_gateways', 'add_bkash_gateway');
function add_bkash_gateway($methods) {
    $methods[] = 'WC_Gateway_bKash';
    return $methods;
}

// Display bkash information under Billing column on the order page
add_action('woocommerce_admin_order_data_after_billing_address', 'display_bkash_info_admin_order', 10, 1);
function display_bkash_info_admin_order($order) {
    $bkash_phone = get_post_meta($order->get_id(), '_bkash_phone', true);
    $bkash_transaction_id = get_post_meta($order->get_id(), '_bkash_transaction_id', true);
    
    if ($bkash_phone || $bkash_transaction_id) {
        ?>
            <div class="payment-order-page">
                <table>
                    <tr>
                        <td colspan="2">
                            <div class="payment-order-page-heading">
                                <div class="bkash-image bpm-bg-image"></div>
                                <h4><?php echo esc_html('bKash Payment Information', 'bangladeshi-payments-mobile'); ?></h4>
                            </div>     
                        </td>
                    </tr>
                    <tr>
                        <td><?php echo esc_html('Phone Number:', 'bangladeshi-payments-mobile');?></td>
                        <td><?php echo esc_html($bkash_phone);?></td>
                    </tr>
                    <tr>
                        <td><?php echo esc_html('Transaction ID:', 'bangladeshi-payments-mobile');?></td>
                        <td><?php echo esc_html($bkash_transaction_id);?></td>
                    </tr>
                </table>
            </div>
        <?php 
    }
}
