<?php
/*
 * Plugin Name: WooCommerce AGC Cheque Payments
 * Plugin URI:https://wordpress.org/
 * Description: Take credit card payments on your store.
 * Author: wordpress
 * Version: 1.0.1
 */

if( ! in_array ( 'woocommerce/woocommerce.php', apply_filters ( 'active_plugins', get_option ( 'active_plugins' ) ) ) ) {
    return;
}

function wc_agc_cheque_add_to_gateways($gateways) {
    $gateways[] = 'WC_Gateway_agc_cheque';
    return $gateways;
}

add_filter ( 'woocommerce_payment_gateways', 'wc_agc_cheque_add_to_gateways' );

function wc_agc_cheque_gateway_plugin_links($links) {

    $plugin_links = array (
        '<a href="' . admin_url ( 'admin.php?page=wc-settings&tab=checkout&section=agc_cheque_gateway' ) . '">' . __ ( 'Configure', 'wc-gateway-agc_cheque' ) . '</a>'
    );

    return array_merge ( $plugin_links, $links );
}

add_filter ( 'plugin_action_links_' . plugin_basename ( __FILE__ ), 'wc_agc_cheque_gateway_plugin_links' );

add_action ( 'plugins_loaded', 'wc_agc_cheque_gateway_init', 11 );

function wc_agc_cheque_gateway_init() {

    class WC_Gateway_agc_cheque extends WC_Payment_Gateway {

        /**
         * Constructor for the gateway.
         */
        public function __construct() {

            $this->id                 = 'agc_cheque_gateway';
            $this->icon               = apply_filters ( 'woocommerce_agc_cheque_icon', '' );
            $this->has_fields         = false;
            $this->method_title       = __ ( 'AGC Cheque Payments', 'wc-gateway-agc_cheque' );
            $this->method_description = __ ( 'Allows Agc Cheque payments. Very handy if you use your cheque gateway for another payment method, and can help with testing. Orders are marked as "on-hold" when received.', 'wc-gateway-agc_cheque' );

            // Load the settings.
            $this->init_form_fields ();
            $this->init_settings ();

            // Define user set variables
            $this->title        = $this->get_option ( 'title' );
            $this->description  = $this->get_option ( 'description' );
            $this->instructions = $this->get_option ( 'instructions', $this->description );

            // Actions
            add_action ( 'woocommerce_update_options_payment_gateways_' . $this->id, array ( $this, 'process_admin_options' ) );
            add_action ( 'woocommerce_thankyou_' . $this->id, array ( $this, 'thankyou_page' ) );

            // Customer Emails
            add_action ( 'woocommerce_email_before_order_table', array ( $this, 'email_instructions' ), 10, 3 );
        }

        /**
         * Initialize Gateway Settings Form Fields
         */
        public function init_form_fields() {

            $this->form_fields = apply_filters ( 'wc_agc_cheque_form_fields', array (
                'enabled'      => array (
                    'title'   => __ ( 'Enable/Disable', 'wc-gateway-agc_cheque' ),
                    'type'    => 'checkbox',
                    'label'   => __ ( 'Enable AGC Cheque Payments Payment', 'wc-gateway-agc_cheque' ),
                    'default' => 'yes'
                ),
                'title'        => array (
                    'title'       => __ ( 'Title', 'wc-gateway-agc_cheque' ),
                    'type'        => 'text',
                    'description' => __ ( 'This controls the title for the payment method the customer sees during checkout.', 'wc-gateway-agc_cheque' ),
                    'default'     => __ ( 'AGC Cheque Payments Payment', 'wc-gateway-agc_cheque' ),
                    'desc_tip'    => true,
                ),
                'description'  => array (
                    'title'       => __ ( 'Description', 'wc-gateway-agc_cheque' ),
                    'type'        => 'textarea',
                    'description' => __ ( 'Payment method description that the customer will see on your checkout.', 'wc-gateway-agc_cheque' ),
                    'default'     => __ ( 'Please remit payment to Store Name upon pickup or delivery.', 'wc-gateway-agc_cheque' ),
                    'desc_tip'    => true,
                ),
                'instructions' => array (
                    'title'       => __ ( 'Instructions', 'wc-gateway-agc_cheque' ),
                    'type'        => 'textarea',
                    'description' => __ ( 'Instructions that will be added to the thank you page and emails.', 'wc-gateway-agc_cheque' ),
                    'default'     => '',
                    'desc_tip'    => true,
                ),
                    ) );
        }

        /**
         * Output for the order received page.
         */
//            public function thankyou_page() {
//                if( $this->instructions ) {
//                    echo wpautop ( wptexturize ( $this->instructions ) );
//                }
//            }
        public function thankyou_page($order_id) {
            if( $this->instructions ) {
                echo wpautop ( wptexturize ( $this->instructions ) ) . '<br>';
            }
            $order = wc_get_order ( $order_id );
            if( $order->has_status ( 'on-hold' ) && $order->get_payment_method () === $this->id ) {
                echo '<p>' . __ ( 'Order status: AGC awaiting cheque payment', 'wc-gateway-agc_cheque' ) . '</p>';
            }
        }

        /**
         * Add content to the WC emails.
         *
         * @access public
         * @param WC_Order $order
         * @param bool $sent_to_admin
         * @param bool $plain_text
         */
        public function email_instructions($order, $sent_to_admin, $plain_text = false) {

            if( $this->instructions && ! $sent_to_admin && $this->id === $order->payment_method && $order->has_status ( 'on-hold' ) ) {
                echo wpautop ( wptexturize ( $this->instructions ) ) . PHP_EOL;
            }
        }

        /**
         * Process the payment and return the result
         *
         * @param int $order_id
         * @return array
         */
        public function process_payment($order_id) {

            $order = wc_get_order ( $order_id );

            // Mark as on-hold (we're awaiting the payment)
            $order->update_status ( 'on-hold', __ ( 'AGC awaiting cheque payment', 'wc-gateway-agc_cheque' ) );

            // Reduce stock levels
            $order->reduce_order_stock ();

            // Remove cart
            WC ()->cart->empty_cart ();

            // Return thankyou redirect
            return array (
                'result'   => 'success',
                'redirect' => $this->get_return_url ( $order )
            );
        }

        public function add_order_note_on_completion($order_id) {
            $order = wc_get_order ( $order_id );

            if( $order->get_payment_method () === $this->id ) {
                $statuses = array ( 'on-hold', 'completed' );
                if( in_array ( $order->get_status (), $statuses ) ) {
                    $order->add_order_note ( __ ( 'Cheque payment completed', 'wc-gateway-agc_cheque' ) );
//                    $order->remove_order_notes_with_meta_key ( '_customer_hold' );
                    $order_notes = $order->get_customer_order_notes ();
                    foreach ( $order_notes as $note ) {
                        if( $note->comment_type === 'on-hold' ) {
                            $order->delete_order_note_by_id ( $note->comment_ID );
                            break;
                        }
                    }
                }
            }
        }

    }

    // end \WC_Gateway_Offline class
}

// Add order note on status transition
add_action ( 'woocommerce_order_status_completed', 'wc_agc_cheque_add_order_note_on_completion' );

function wc_agc_cheque_add_order_note_on_completion($order_id) {
    $payment_method = get_post_meta ( $order_id, '_payment_method', true );

    if( $payment_method === 'agc_cheque_gateway' ) {
        $gateway = new WC_Gateway_agc_cheque();
        $gateway->add_order_note_on_completion ( $order_id );
    }
}
