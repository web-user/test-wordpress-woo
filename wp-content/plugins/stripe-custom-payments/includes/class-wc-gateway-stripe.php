<?php



class WC_Gateway_Stripe extends WC_Payment_Gateway {



    /**
     * The delay between retries.
     *
     * @var int
     */
    public $retry_interval;

    /**
     * Should we capture Credit cards
     *
     * @var bool
     */
    public $capture;

    /**
     * Alternate credit card statement name
     *
     * @var bool
     */
    public $statement_descriptor;

    /**
     * Checkout enabled
     *
     * @var bool
     */
    public $stripe_checkout;

    /**
     * Stripe Checkout description.
     *
     * @var string
     */
    public $stripe_checkout_description;

    /**
     * Require 3D Secure enabled
     *
     * @var bool
     */
    public $three_d_secure;

    /**
     * Credit card image
     *
     * @var string
     */
    public $stripe_checkout_image;

    /**
     * Should we store the users credit cards?
     *
     * @var bool
     */
    public $saved_cards;

    /**
     * API access secret key
     *
     * @var string
     */
    public $secret_key;

    /**
     * Api access publishable key
     *
     * @var string
     */
    public $publishable_key;

    /**
     * Do we accept Payment Request?
     *
     * @var bool
     */
    public $payment_request;

    /**
     * Is test mode active?
     *
     * @var bool
     */
    public $testmode;

    /**  x c xc           cx
     * Inline CC form styling
     *
     * @var string
     */
    public $inline_cc_form;

    /**
     * Pre Orders Object
     *
     * @var object
     */
    public $pre_orders;



    public function __construct() {
        $this->retry_interval       = 1;
        $this->id                   = 'stripe';
        $this->method_title         = __( 'Stripe' );
        /* translators: 1) link to Stripe register page 2) link to Stripe api keys page */
        $this->method_description   = sprintf( __( 'Stripe works by adding payment fields on the checkout and then sending the details to Stripe for verification. <a href="%1$s" target="_blank">Sign up</a> for a Stripe account, and <a href="%2$s" target="_blank">get your Stripe account keys</a>.', 'woocommerce-gateway-stripe' ), 'https://dashboard.stripe.com/register', 'https://dashboard.stripe.com/account/apikeys' );
        $this->has_fields           = true;
        $this->supports             = array(
            'products',
            'refunds',
            'tokenization',
            'add_payment_method',
            'subscriptions',
            'subscription_cancellation',
            'subscription_suspension',
            'subscription_reactivation',
            'subscription_amount_changes',
            'subscription_date_changes',
            'subscription_payment_method_change',
            'subscription_payment_method_change_customer',
            'subscription_payment_method_change_admin',
            'multiple_subscriptions',
            'pre-orders',
        );


        // Load the form fields.
        $this->form_fields = array(
            'enabled' => array(
                'title'       => __( 'Enable/Disable', 'woocommerce-gateway-stripe' ),
                'label'       => __( 'Enable Stripe', 'woocommerce-gateway-stripe' ),
                'type'        => 'checkbox',
                'description' => '',
                'default'     => 'no',
            ),
            'title' => array(
                'title'       => __( 'Title', 'woocommerce-gateway-stripe' ),
                'type'        => 'text',
                'description' => __( 'This controls the title which the user sees during checkout.', 'woocommerce-gateway-stripe' ),
                'default'     => __( 'Credit Card (Stripe)', 'woocommerce-gateway-stripe' ),
                'desc_tip'    => true,
            ),
            'description' => array(
                'title'       => __( 'Description', 'woocommerce-gateway-stripe' ),
                'type'        => 'text',
                'description' => __( 'This controls the description which the user sees during checkout.', 'woocommerce-gateway-stripe' ),
                'default'     => __( 'Pay with your credit card via Stripe.', 'woocommerce-gateway-stripe' ),
                'desc_tip'    => true,
            ),
            'webhook' => array(
                'title'       => __( 'Webhook Endpoints', 'woocommerce-gateway-stripe' ),
                'type'        => 'title',
                /* translators: webhook URL */
//                'description' => $this->display_admin_settings_webhook_description(),
            ),
            'testmode' => array(
                'title'       => __( 'Test mode', 'woocommerce-gateway-stripe' ),
                'label'       => __( 'Enable Test Mode', 'woocommerce-gateway-stripe' ),
                'type'        => 'checkbox',
                'description' => __( 'Place the payment gateway in test mode using test API keys.', 'woocommerce-gateway-stripe' ),
                'default'     => 'yes',
                'desc_tip'    => true,
            ),
            'test_publishable_key' => array(
                'title'       => __( 'Test Publishable Key', 'woocommerce-gateway-stripe' ),
                'type'        => 'password',
                'description' => __( 'Get your API keys from your stripe account.', 'woocommerce-gateway-stripe' ),
                'default'     => '',
                'desc_tip'    => true,
            ),
            'test_secret_key' => array(
                'title'       => __( 'Test Secret Key', 'woocommerce-gateway-stripe' ),
                'type'        => 'password',
                'description' => __( 'Get your API keys from your stripe account.', 'woocommerce-gateway-stripe' ),
                'default'     => '',
                'desc_tip'    => true,
            ),
            'publishable_key' => array(
                'title'       => __( 'Live Publishable Key', 'woocommerce-gateway-stripe' ),
                'type'        => 'password',
                'description' => __( 'Get your API keys from your stripe account.', 'woocommerce-gateway-stripe' ),
                'default'     => '',
                'desc_tip'    => true,
            ),
            'secret_key' => array(
                'title'       => __( 'Live Secret Key', 'woocommerce-gateway-stripe' ),
                'type'        => 'password',
                'description' => __( 'Get your API keys from your stripe account.', 'woocommerce-gateway-stripe' ),
                'default'     => '',
                'desc_tip'    => true,
            ),
            'inline_cc_form' => array(
                'title'       => __( 'Inline Credit Card Form', 'woocommerce-gateway-stripe' ),
                'type'        => 'checkbox',
                'description' => __( 'Choose the style you want to show for your credit card form. When unchecked, the credit card form will display separate credit card number field, expiry date field and cvc field.', 'woocommerce-gateway-stripe' ),
                'default'     => 'no',
                'desc_tip'    => true,
            ),
            'statement_descriptor' => array(
                'title'       => __( 'Statement Descriptor', 'woocommerce-gateway-stripe' ),
                'type'        => 'text',
                'description' => __( 'This may be up to 22 characters. The statement description must contain at least one letter, may not include ><"\' characters, and will appear on your customer\'s statement in capital letters.', 'woocommerce-gateway-stripe' ),
                'default'     => '',
                'desc_tip'    => true,
            ),
            'capture' => array(
                'title'       => __( 'Capture', 'woocommerce-gateway-stripe' ),
                'label'       => __( 'Capture charge immediately', 'woocommerce-gateway-stripe' ),
                'type'        => 'checkbox',
                'description' => __( 'Whether or not to immediately capture the charge. When unchecked, the charge issues an authorization and will need to be captured later. Uncaptured charges expire in 7 days.', 'woocommerce-gateway-stripe' ),
                'default'     => 'yes',
                'desc_tip'    => true,
            ),

            'stripe_checkout' => array(
                'title'       => __( 'Stripe Modal Checkout', 'woocommerce-gateway-stripe' ),
                'label'       => __( 'Enable Stripe Checkout', 'woocommerce-gateway-stripe' ),
                'type'        => 'checkbox',
                'description' => __( 'If enabled, this option shows a "pay" button and modal credit card form on the checkout, instead of credit card fields directly on the page. We recommend you leave this disabled and use the embedded form as that is the preferred method.', 'woocommerce-gateway-stripe' ),
                'default'     => 'no',
                'desc_tip'    => true,
            ),
            'stripe_checkout_image' => array(
                'title'       => __( 'Stripe Checkout Image', 'woocommerce-gateway-stripe' ),
                'description' => __( 'Optionally enter the URL to a 128x128px image of your brand or product. e.g. <code>https://yoursite.com/wp-content/uploads/2013/09/yourimage.jpg</code>', 'woocommerce-gateway-stripe' ),
                'type'        => 'text',
                'default'     => '',
                'desc_tip'    => true,
            ),
            'stripe_checkout_description' => array(
                'title'       => __( 'Stripe Checkout Description', 'woocommerce-gateway-stripe' ),
                'type'        => 'text',
                'description' => __( 'Shows a description of your store on Stripe Modal Checkout.', 'woocommerce-gateway-stripe' ),
                'default'     => '',
                'desc_tip'    => true,
            ),
            'payment_request' => array(
                'title'       => __( 'Payment Request Buttons', 'woocommerce-gateway-stripe' ),
                /* translators: 1) br tag 2) opening anchor tag 3) closing anchor tag */
                'label'       => sprintf( __( 'Enable Payment Request Buttons. (Apple Pay/Chrome Payment Request API) %1$sBy using Apple Pay, you agree to %2$s and %3$s\'s terms of service.', 'woocommerce-gateway-stripe' ), '<br />', '<a href="https://stripe.com/apple-pay/legal" target="_blank">Stripe</a>', '<a href="https://developer.apple.com/apple-pay/acceptable-use-guidelines-for-websites/" target="_blank">Apple</a>' ),
                'type'        => 'checkbox',
                'description' => __( 'If enabled, users will be able to pay using Apple Pay or Chrome Payment Request if supported by the browser.', 'woocommerce-gateway-stripe' ),
                'default'     => 'yes',
                'desc_tip'    => true,
            ),
            'payment_request_button_type' => array(
                'title'       => __( 'Payment Request Button Type', 'woocommerce-gateway-stripe' ),
                'label'       => __( 'Button Type', 'woocommerce-gateway-stripe' ),
                'type'        => 'select',
                'description' => __( 'Select the button type you would like to show.', 'woocommerce-gateway-stripe' ),
                'default'     => 'buy',
                'desc_tip'    => true,
                'options'     => array(
                    'default' => __( 'Default', 'woocommerce-gateway-stripe' ),
                    'buy'     => __( 'Buy', 'woocommerce-gateway-stripe' ),
                    'donate'  => __( 'Donate', 'woocommerce-gateway-stripe' ),
                ),
            ),
            'payment_request_button_theme' => array(
                'title'       => __( 'Payment Request Button Theme', 'woocommerce-gateway-stripe' ),
                'label'       => __( 'Button Theme', 'woocommerce-gateway-stripe' ),
                'type'        => 'select',
                'description' => __( 'Select the button theme you would like to show.', 'woocommerce-gateway-stripe' ),
                'default'     => 'dark',
                'desc_tip'    => true,
                'options'     => array(
                    'dark'          => __( 'Dark', 'woocommerce-gateway-stripe' ),
                    'light'         => __( 'Light', 'woocommerce-gateway-stripe' ),
                    'light-outline' => __( 'Light-Outline', 'woocommerce-gateway-stripe' ),
                ),
            ),
            'payment_request_button_height' => array(
                'title'       => __( 'Payment Request Button Height', 'woocommerce-gateway-stripe' ),
                'label'       => __( 'Button Height', 'woocommerce-gateway-stripe' ),
                'type'        => 'text',
                'description' => __( 'Enter the height you would like the button to be in pixels. Width will always be 100%.', 'woocommerce-gateway-stripe' ),
                'default'     => '44',
                'desc_tip'    => true,
            ),
            'saved_cards' => array(
                'title'       => __( 'Saved Cards', 'woocommerce-gateway-stripe' ),
                'label'       => __( 'Enable Payment via Saved Cards', 'woocommerce-gateway-stripe' ),
                'type'        => 'checkbox',
                'description' => __( 'If enabled, users will be able to pay with a saved card during checkout. Card details are saved on Stripe servers, not on your store.', 'woocommerce-gateway-stripe' ),
                'default'     => 'no',
                'desc_tip'    => true,
            ),

        );

        // Load the settings.
        $this->init_settings();

        // Get setting values.
        $this->title                       = $this->get_option( 'title' );
        $this->description                 = $this->get_option( 'description' );
        $this->enabled                     = $this->get_option( 'enabled' );
        $this->testmode                    = 'yes' === $this->get_option( 'testmode' );
        $this->inline_cc_form              = 'yes' === $this->get_option( 'inline_cc_form' );
        $this->capture                     = 'yes' === $this->get_option( 'capture', 'yes' );

        $this->three_d_secure              = 'yes' === $this->get_option( 'three_d_secure' );
        $this->stripe_checkout             = 'yes' === $this->get_option( 'stripe_checkout' );
        $this->stripe_checkout_image       = $this->get_option( 'stripe_checkout_image', '' );
        $this->stripe_checkout_description = $this->get_option( 'stripe_checkout_description' );
        $this->saved_cards                 = 'yes' === $this->get_option( 'saved_cards' );
        $this->secret_key                  = $this->testmode ? $this->get_option( 'test_secret_key' ) : $this->get_option( 'secret_key' );
        $this->publishable_key             = $this->testmode ? $this->get_option( 'test_publishable_key' ) : $this->get_option( 'publishable_key' );
        $this->payment_request             = 'yes' === $this->get_option( 'payment_request', 'yes' );

        if ( $this->stripe_checkout ) {
            $this->order_button_text = __( 'Continue to payment', 'woocommerce-gateway-stripe' );
        }


        WC_Stripe_API::set_secret_key( $this->secret_key );

        // Hooks.
        add_action( 'wp_enqueue_scripts', array( $this, 'payment_scripts' ) );

        add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
        add_action( 'woocommerce_admin_order_totals_after_total', array( $this, 'display_order_fee' ), 10, 1 );
        add_action( 'woocommerce_admin_order_totals_after_total', array( $this, 'display_order_payout' ), 20, 1 );
        add_action( 'woocommerce_customer_save_address', array( $this, 'show_update_card_notice' ), 10, 2 );
        add_action( 'woocommerce_receipt_stripe', array( $this, 'stripe_checkout_receipt_page' ) );
        add_action( 'woocommerce_api_' . strtolower( get_class( $this ) ), array( $this, 'stripe_checkout_return_handler' ) );





    }




    /**
     * Payment_scripts function.
     *
     * Outputs scripts used for stripe payment
     *
     * @since 3.1.0
     * @version 4.0.0
     */
    public function payment_scripts() {
        if ( ! is_cart() && ! is_checkout() && ! isset( $_GET['pay_for_order'] ) && ! is_add_payment_method_page() && ! isset( $_GET['change_payment_method'] ) ) {
            return;
        }

        // If Stripe is not enabled bail.
        if ( 'no' === $this->enabled ) {
            return;
        }


        $suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

        wp_enqueue_style( 'stripe_styles' );
        wp_register_script( 'stripe_checkout', 'https://checkout.stripe.com/checkout.js', '', true );
        wp_register_script( 'stripe', 'https://js.stripe.com/v3/', '', '3.0', true );

        $stripe_params = array(
            'key'                  => $this->publishable_key,
            'i18n_terms'           => __( 'Please accept the terms and conditions first', 'woocommerce-gateway-stripe' ),
            'i18n_required_fields' => __( 'Please fill in required checkout fields first', 'woocommerce-gateway-stripe' ),
        );

        // If we're on the pay page we need to pass stripe.js the address of the order.
        if ( isset( $_GET['pay_for_order'] ) && 'true' === $_GET['pay_for_order'] ) {
            $order_id = wc_get_order_id_by_order_key( urldecode( $_GET['key'] ) );
            $order    = wc_get_order( $order_id );

//            $stripe_params['billing_first_name'] = WC_Stripe_Helper::is_pre_30() ? $order->billing_first_name : $order->get_billing_first_name();
//            $stripe_params['billing_last_name']  = WC_Stripe_Helper::is_pre_30() ? $order->billing_last_name : $order->get_billing_last_name();
//            $stripe_params['billing_address_1']  = WC_Stripe_Helper::is_pre_30() ? $order->billing_address_1 : $order->get_billing_address_1();
//            $stripe_params['billing_address_2']  = WC_Stripe_Helper::is_pre_30() ? $order->billing_address_2 : $order->get_billing_address_2();
//            $stripe_params['billing_state']      = WC_Stripe_Helper::is_pre_30() ? $order->billing_state : $order->get_billing_state();
//            $stripe_params['billing_city']       = WC_Stripe_Helper::is_pre_30() ? $order->billing_city : $order->get_billing_city();
//            $stripe_params['billing_postcode']   = WC_Stripe_Helper::is_pre_30() ? $order->billing_postcode : $order->get_billing_postcode();
//            $stripe_params['billing_country']    = WC_Stripe_Helper::is_pre_30() ? $order->billing_country : $order->get_billing_country();
        }

        $stripe_params['no_prepaid_card_msg']                     = __( 'Sorry, we\'re not accepting prepaid cards at this time. Your credit card has not been charge. Please try with alternative payment method.', 'woocommerce-gateway-stripe' );
        $stripe_params['no_sepa_owner_msg']                       = __( 'Please enter your IBAN account name.', 'woocommerce-gateway-stripe' );
        $stripe_params['no_sepa_iban_msg']                        = __( 'Please enter your IBAN account number.', 'woocommerce-gateway-stripe' );
        $stripe_params['sepa_mandate_notification']               = apply_filters( 'wc_stripe_sepa_mandate_notification', 'email' );
        $stripe_params['allow_prepaid_card']                      = apply_filters( 'wc_stripe_allow_prepaid_card', true ) ? 'yes' : 'no';
        $stripe_params['inline_cc_form']                          = $this->inline_cc_form ? 'yes' : 'no';
        $stripe_params['stripe_checkout_require_billing_address'] = apply_filters( 'wc_stripe_checkout_require_billing_address', false ) ? 'yes' : 'no';
        $stripe_params['is_checkout']                             = ( is_checkout() && empty( $_GET['pay_for_order'] ) ) ? 'yes' : 'no';
        $stripe_params['return_url']                              = $this->get_stripe_return_url();
        $stripe_params['ajaxurl']                                 = WC_AJAX::get_endpoint( '%%endpoint%%' );
        $stripe_params['stripe_nonce']                            = wp_create_nonce( '_wc_stripe_nonce' );
        $stripe_params['statement_descriptor']                    = $this->statement_descriptor;
        $stripe_params['elements_options']                        = apply_filters( 'wc_stripe_elements_options', array() );
        $stripe_params['is_stripe_checkout']                      = $this->stripe_checkout ? 'yes' : 'no';
        $stripe_params['is_change_payment_page']                  = isset( $_GET['change_payment_method'] ) ? 'yes' : 'no';
        $stripe_params['is_add_payment_page']                     = is_wc_endpoint_url( 'add-payment-method' ) ? 'yes' : 'no';
        $stripe_params['is_pay_for_order_page']                   = is_wc_endpoint_url( 'order-pay' ) ? 'yes' : 'no';
        $stripe_params['elements_styling']                        = apply_filters( 'wc_stripe_elements_styling', false );
        $stripe_params['elements_classes']                        = apply_filters( 'wc_stripe_elements_classes', false );



        wp_localize_script( 'woocommerce_stripe', 'wc_stripe_params', apply_filters( 'wc_stripe_params', $stripe_params ) );
        wp_localize_script( 'woocommerce_stripe_checkout', 'wc_stripe_params', apply_filters( 'wc_stripe_params', $stripe_params ) );

        if ( $this->stripe_checkout ) {
            wp_enqueue_script( 'stripe_checkout' );
        }

        $this->tokenization_script();
        wp_enqueue_script( 'woocommerce_stripe' );
    }



    /**
     * Displays the Stripe fee
     *
     * @since 4.1.0
     *
     * @param int $order_id
     */
    public function display_order_fee( $order_id ) {
        if ( apply_filters( 'wc_stripe_hide_display_order_fee', false, $order_id ) ) {
            return;
        }

        $order = wc_get_order( $order_id );

//        $fee      = WC_Stripe_Helper::get_stripe_fee( $order );
//        $currency = WC_Stripe_Helper::get_stripe_currency( $order );

//        if ( ! $fee || ! $currency ) {
//            return;
//        }

        ?>

        <tr>
            <td class="label stripe-fee">
                <?php echo wc_help_tip( __( 'This represents the fee Stripe collects for the transaction.', 'woocommerce-gateway-stripe' ) ); ?>
                <?php esc_html_e( 'Stripe Fee:', 'woocommerce-gateway-stripe' ); ?>
            </td>
            <td width="1%"></td>
            <td class="total">
<!--                -&nbsp;--><?php //echo wc_price( $fee, array( 'currency' => $currency ) ); ?>
            </td>
        </tr>

        <?php
    }


    /**
     * Displays the net total of the transaction without the charges of Stripe.
     *
     * @since 4.1.0
     *
     * @param int $order_id
     */
    public function display_order_payout( $order_id ) {
        if ( apply_filters( 'wc_stripe_hide_display_order_payout', false, $order_id ) ) {
            return;
        }

        $order = wc_get_order( $order_id );

//        $net      = WC_Stripe_Helper::get_stripe_net( $order );
//        $currency = WC_Stripe_Helper::get_stripe_currency( $order );

//        if ( ! $net || ! $currency ) {
//            return;
//        }

        ?>

        <tr>
            <td class="label stripe-payout">
                <?php echo wc_help_tip( __( 'This represents the net total that will be credited to your Stripe bank account. This may be in the currency that is set in your Stripe account.', 'woocommerce-gateway-stripe' ) ); ?>
                <?php esc_html_e( 'Stripe Payout:', 'woocommerce-gateway-stripe' ); ?>
            </td>
            <td width="1%"></td>
            <td class="total">
<!--                --><?php //echo wc_price( $net, array( 'currency' => $currency ) ); ?>
            </td>
        </tr>

        <?php
    }



}