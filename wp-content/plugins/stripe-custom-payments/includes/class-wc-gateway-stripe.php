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
        $this->method_title         = __( 'Stripe Custom' );
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
            'capture' => array(
                'title'       => __( 'Capture', 'woocommerce-gateway-stripe' ),
                'label'       => __( 'Capture charge immediately', 'woocommerce-gateway-stripe' ),
                'type'        => 'checkbox',
                'description' => __( 'Whether or not to immediately capture the charge. When unchecked, the charge issues an authorization and will need to be captured later. Uncaptured charges expire in 7 days.', 'woocommerce-gateway-stripe' ),
                'default'     => 'yes',
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
//        add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );
        add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
        add_action( 'woocommerce_admin_order_totals_after_total', array( $this, 'display_order_fee' ), 10, 1 );
        add_action( 'woocommerce_admin_order_totals_after_total', array( $this, 'display_order_payout' ), 20, 1 );
        add_action( 'woocommerce_customer_save_address', array( $this, 'show_update_card_notice' ), 10, 2 );
        add_action( 'woocommerce_receipt_stripe', array( $this, 'stripe_checkout_receipt_page' ) );
        add_action( 'woocommerce_api_' . strtolower( get_class( $this ) ), array( $this, 'stripe_checkout_return_handler' ) );

        if ( WC_Stripe_Helper::is_pre_orders_exists() ) {
            $this->pre_orders = new WC_Stripe_Pre_Orders_Compat();

            add_action( 'wc_pre_orders_process_pre_order_completion_payment_' . $this->id, array( $this->pre_orders, 'process_pre_order_release_payment' ) );
        }





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

        wp_register_style( 'stripe_styles', plugins_url( 'assets/css/stripe-styles.css', __FILE__ ), array() );
        wp_enqueue_style( 'stripe_styles' );
        wp_register_script( 'stripe_checkout', 'https://checkout.stripe.com/checkout.js', '', true );
        wp_register_script( 'stripe', 'https://js.stripe.com/v3/', '', '3.0', true );
        wp_register_script( 'woocommerce_stripe', plugins_url( 'assets/js/stripe' . $suffix . '.js', __FILE__ ), array( 'jquery-payment', 'stripe' ), true );

        $stripe_params = array(
            'key'                  => $this->publishable_key,
            'i18n_terms'           => __( 'Please accept the terms and conditions first', 'woocommerce-gateway-stripe' ),
            'i18n_required_fields' => __( 'Please fill in required checkout fields first', 'woocommerce-gateway-stripe' ),
        );

        // If we're on the pay page we need to pass stripe.js the address of the order.
        if ( isset( $_GET['pay_for_order'] ) && 'true' === $_GET['pay_for_order'] ) {
            $order_id = wc_get_order_id_by_order_key( urldecode( $_GET['key'] ) );
            $order    = wc_get_order( $order_id );

            $stripe_params['billing_first_name'] = WC_Stripe_Helper::is_pre_30() ? $order->billing_first_name : $order->get_billing_first_name();
            $stripe_params['billing_last_name']  = WC_Stripe_Helper::is_pre_30() ? $order->billing_last_name : $order->get_billing_last_name();
            $stripe_params['billing_address_1']  = WC_Stripe_Helper::is_pre_30() ? $order->billing_address_1 : $order->get_billing_address_1();
            $stripe_params['billing_address_2']  = WC_Stripe_Helper::is_pre_30() ? $order->billing_address_2 : $order->get_billing_address_2();
            $stripe_params['billing_state']      = WC_Stripe_Helper::is_pre_30() ? $order->billing_state : $order->get_billing_state();
            $stripe_params['billing_city']       = WC_Stripe_Helper::is_pre_30() ? $order->billing_city : $order->get_billing_city();
            $stripe_params['billing_postcode']   = WC_Stripe_Helper::is_pre_30() ? $order->billing_postcode : $order->get_billing_postcode();
            $stripe_params['billing_country']    = WC_Stripe_Helper::is_pre_30() ? $order->billing_country : $order->get_billing_country();
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

        // merge localized messages to be use in JS
        $stripe_params = array_merge( $stripe_params, WC_Stripe_Helper::get_localized_messages() );

        wp_localize_script( 'woocommerce_stripe', 'wc_stripe_params', apply_filters( 'wc_stripe_params', $stripe_params ) );
        wp_localize_script( 'woocommerce_stripe_checkout', 'wc_stripe_params', apply_filters( 'wc_stripe_params', $stripe_params ) );

        if ( $this->stripe_checkout ) {
            wp_enqueue_script( 'stripe_checkout' );
        }

        $this->tokenization_script();
        wp_enqueue_script( 'woocommerce_stripe' );
    }





    /**
     * Payment form on checkout page
     */
    public function payment_fields() {
        $user                 = wp_get_current_user();
        $display_tokenization = $this->supports( 'tokenization' ) && is_checkout() && $this->saved_cards;
        $total                = WC()->cart->total;
        $user_email           = '';
        $description          = $this->get_description() ? $this->get_description() : '';
        $firstname            = '';
        $lastname             = '';

        // If paying from order, we need to get total from order not cart.
        if ( isset( $_GET['pay_for_order'] ) && ! empty( $_GET['key'] ) ) {
            $order      = wc_get_order( wc_get_order_id_by_order_key( wc_clean( $_GET['key'] ) ) );
            $total      = $order->get_total();
            $user_email = WC_Stripe_Helper::is_pre_30() ? $order->billing_email : $order->get_billing_email();
        } else {
            if ( $user->ID ) {
                $user_email = get_user_meta( $user->ID, 'billing_email', true );
                $user_email = $user_email ? $user_email : $user->user_email;
            }
        }

        if ( is_add_payment_method_page() ) {
            $pay_button_text = __( 'Add Card', 'woocommerce-gateway-stripe' );
            $total           = '';
            $firstname       = $user->user_firstname;
            $lastname        = $user->user_lastname;

        } elseif ( function_exists( 'wcs_order_contains_subscription' ) && isset( $_GET['change_payment_method'] ) ) {
            $pay_button_text = __( 'Change Payment Method', 'woocommerce-gateway-stripe' );
            $total        = '';
        } else {
            $pay_button_text = '';
        }

        ob_start();

        echo '<div
			id="stripe-payment-data"
			data-panel-label="' . esc_attr( $pay_button_text ) . '"
			data-description="' . esc_attr( strip_tags( $this->stripe_checkout_description ) ) . '"
			data-email="' . esc_attr( $user_email ) . '"
			data-verify-zip="' . esc_attr( apply_filters( 'wc_stripe_checkout_verify_zip', false ) ? 'true' : 'false' ) . '"
			data-billing-address="' . esc_attr( apply_filters( 'wc_stripe_checkout_require_billing_address', false ) ? 'true' : 'false' ) . '"
			data-shipping-address="' . esc_attr( apply_filters( 'wc_stripe_checkout_require_shipping_address', false ) ? 'true' : 'false' ) . '" 
			data-amount="' . esc_attr( WC_Stripe_Helper::get_stripe_amount( $total ) ) . '"
			data-name="' . esc_attr( $this->statement_descriptor ) . '"
			data-full-name="' . esc_attr( $firstname . ' ' . $lastname ) . '"
			data-currency="' . esc_attr( strtolower( get_woocommerce_currency() ) ) . '"
			data-image="' . esc_attr( $this->stripe_checkout_image ) . '"
			data-locale="' . esc_attr( apply_filters( 'wc_stripe_checkout_locale', $this->get_locale() ) ) . '"
			data-three-d-secure="' . esc_attr( $this->three_d_secure ? 'true' : 'false' ) . '"
			data-allow-remember-me="' . esc_attr( apply_filters( 'wc_stripe_allow_remember_me', true ) ? 'true' : 'false' ) . '">';

        if ( $description ) {
            if ( $this->testmode ) {
                /* translators: link to Stripe testing page */
                $description .= ' ' . sprintf( __( 'TEST MODE ENABLED. In test mode, you can use the card number 4242424242424242 with any CVC and a valid expiration date or check the <a href="%s" target="_blank">Testing Stripe documentation</a> for more card numbers.', 'woocommerce-gateway-stripe' ), 'https://stripe.com/docs/testing' );
                $description  = trim( $description );
            }

            echo apply_filters( 'wc_stripe_description', wpautop( wp_kses_post( $description ) ), $this->id );
        }

        if ( $display_tokenization ) {
            $this->tokenization_script();
            $this->saved_payment_methods();
        }

        if ( ! $this->stripe_checkout ) {
            $this->elements_form();
        }

        if ( apply_filters( 'wc_stripe_display_save_payment_method_checkbox', $display_tokenization ) && ! is_add_payment_method_page() && ! isset( $_GET['change_payment_method'] ) ) {

            if ( ! $this->stripe_checkout ) {
                $this->save_payment_method_checkbox();
            } elseif ( $this->stripe_checkout && isset( $_GET['pay_for_order'] ) && ! empty( $_GET['key'] ) ) {
                $this->save_payment_method_checkbox();
            }
        }

        echo '</div>';

        ob_end_flush();
    }


    /**
     * Adds a notice for customer when they update their billing address.
     *
     * @since 4.1.0
     * @param int $user_id
     * @param array $load_address
     */
    public function show_update_card_notice( $user_id, $load_address ) {
        if ( ! $this->saved_cards || ! WC_Stripe_Payment_Tokens::customer_has_saved_methods( $user_id ) || 'billing' !== $load_address ) {
            return;
        }

        /* translators: 1) Opening anchor tag 2) closing anchor tag */
        wc_add_notice( sprintf( __( 'If your billing address has been changed for saved payment methods, be sure to remove any %1$ssaved payment methods%2$s on file and re-add them.', 'woocommerce-gateway-stripe' ), '<a href="' . esc_url( wc_get_endpoint_url( 'payment-methods' ) ) . '" class="wc-stripe-update-card-notice" style="text-decoration:underline;">', '</a>' ), 'notice' );
    }


    /**
     * Gets the locale with normalization that only Stripe accepts.
     *
     * @since 4.0.6
     * @return string $locale
     */
    public function get_locale() {
        $locale = get_locale();

        /*
         * Stripe expects Norwegian to only be passed NO.
         * But WP has different dialects.
         */
        if ( 'NO' === substr( $locale, 3, 2 ) ) {
            $locale = 'no';
        } else {
            $locale = substr( get_locale(), 0, 2 );
        }

        return $locale;
    }

    /**
     * Renders the Stripe elements form.
     *
     * @since 4.0.0
     * @version 4.0.0
     */
    public function elements_form() {
        ?>
        <fieldset id="wc-<?php echo esc_attr( $this->id ); ?>-cc-form" class="wc-credit-card-form wc-payment-form" style="background:transparent;">
            <?php do_action( 'woocommerce_credit_card_form_start', $this->id ); ?>

            <?php if ( $this->inline_cc_form ) { ?>
                <label for="card-element">
                    <?php esc_html_e( 'Credit or debit card', 'woocommerce-gateway-stripe' ); ?>
                </label>

                <div id="stripe-card-element" style="background:#fff;padding:0 1em;border:1px solid #ddd;margin:5px 0;padding:10px 5px;">
                    <!-- a Stripe Element will be inserted here. -->
                </div>
            <?php } else { ?>
                <div class="form-row form-row-wide">
                    <label for="stripe-card-element"><?php esc_html_e( 'Card Number', 'woocommerce-gateway-stripe' ); ?> <span class="required">*</span></label>
                    <div class="stripe-card-group">
                        <div id="stripe-card-element" style="background:#fff;padding:0 1em;border:1px solid #ddd;margin:5px 0;padding:10px 5px;">
                            <!-- a Stripe Element will be inserted here. -->
                        </div>

                        <i class="stripe-credit-card-brand stripe-card-brand" alt="Credit Card"></i>
                    </div>
                </div>

                <div class="form-row form-row-first">
                    <label for="stripe-exp-element"><?php esc_html_e( 'Expiry Date', 'woocommerce-gateway-stripe' ); ?> <span class="required">*</span></label>

                    <div id="stripe-exp-element" style="background:#fff;padding:0 1em;border:1px solid #ddd;margin:5px 0;padding:10px 5px;">
                        <!-- a Stripe Element will be inserted here. -->
                    </div>
                </div>

                <div class="form-row form-row-last">
                    <label for="stripe-cvc-element"><?php esc_html_e( 'Card Code (CVC)', 'woocommerce-gateway-stripe' ); ?> <span class="required">*</span></label>
                    <div id="stripe-cvc-element" style="background:#fff;padding:0 1em;border:1px solid #ddd;margin:5px 0;padding:10px 5px;">
                        <!-- a Stripe Element will be inserted here. -->
                    </div>
                </div>
                <div class="clear"></div>
            <?php } ?>

            <!-- Used to display form errors -->
            <div class="stripe-source-errors" role="alert"></div>
            <?php do_action( 'woocommerce_credit_card_form_end', $this->id ); ?>
            <div class="clear"></div>
        </fieldset>
        <?php
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
     * Builds the return URL from redirects.
     *
     * @since 4.0.0
     * @version 4.0.0
     * @param object $order
     * @param int $id Stripe session id.
     */
    public function get_stripe_return_url( $order = null, $id = null ) {
        if ( is_object( $order ) ) {
            if ( empty( $id ) ) {
                $id = uniqid();
            }

            $order_id = WC_Stripe_Helper::is_pre_30() ? $order->id : $order->get_id();

            $args = array(
                'utm_nooverride' => '1',
                'order_id'       => $order_id,
            );

            return esc_url_raw( add_query_arg( $args, $this->get_return_url( $order ) ) );
        }

        return esc_url_raw( add_query_arg( array( 'utm_nooverride' => '1' ), $this->get_return_url() ) );
    }



    /**
     * Handles the return from processing the payment.
     *
     * @since 4.1.0
     */
    public function stripe_checkout_return_handler() {
        if ( ! $this->stripe_checkout ) {
            return;
        }

        if ( ! wp_verify_nonce( $_POST['stripe_checkout_process_nonce'], 'stripe-checkout-process' ) ) {
            return;
        }

        $order_id = wc_clean( $_POST['order_id'] );
        $order    = wc_get_order( $order_id );

        do_action( 'wc_stripe_checkout_return_handler', $order );

        if ( WC_Stripe_Helper::is_pre_orders_exists() && $this->pre_orders->is_pre_order( $order_id ) && WC_Pre_Orders_Order::order_requires_payment_tokenization( $order_id ) ) {
            $result = $this->pre_orders->process_pre_order( $order_id );
        } else {
            $result = $this->process_payment( $order_id );
        }

        if ( 'success' === $result['result'] ) {
            wp_redirect( $result['redirect'] );
            exit;
        }

        // Redirects back to pay order page.
        wp_safe_redirect( $order->get_checkout_payment_url( true ) );
        exit;
    }

    /**
     * Checks if we need to redirect for Stripe Checkout.
     *
     * @since 4.1.0
     * @return bool
     */
    public function maybe_redirect_stripe_checkout() {
        return (
            $this->stripe_checkout &&
            ! isset( $_POST['stripe_checkout_order'] ) &&
            ! $this->is_using_saved_payment_method() &&
            ! is_wc_endpoint_url( 'order-pay' )
        );
    }

    /**
     * Checks if we need to process pre orders when
     * pre orders is in the cart.
     *
     * @since 4.1.0
     * @param int $order_id
     * @return bool
     */
    public function maybe_process_pre_orders( $order_id ) {
        return (
            WC_Stripe_Helper::is_pre_orders_exists() &&
            $this->pre_orders->is_pre_order( $order_id ) &&
            WC_Pre_Orders_Order::order_requires_payment_tokenization( $order_id ) &&
            ! is_wc_endpoint_url( 'order-pay' )
        );
    }


    /**
     * Get payment source. This can be a new token/source or existing WC token.
     * If user is logged in and/or has WC account, create an account on Stripe.
     * This way we can attribute the payment to the user to better fight fraud.
     *
     * @since 3.1.0
     * @version 4.0.0
     * @param string $user_id
     * @param bool $force_save_source Should we force save payment source.
     *
     * @throws Exception When card was not added or for and invalid card.
     * @return object
     */
    public function prepare_source( $user_id, $force_save_source = false ) {
        $customer           = new WC_Stripe_Customer( $user_id );
        $set_customer       = true;
        $force_save_source  = apply_filters( 'wc_stripe_force_save_source', $force_save_source, $customer );
        $source_object      = '';
        $source_id          = '';
        $wc_token_id        = false;
        $payment_method     = isset( $_POST['payment_method'] ) ? wc_clean( $_POST['payment_method'] ) : 'stripe';
        $is_token           = false;

        // New CC info was entered and we have a new source to process.
        if ( ! empty( $_POST['stripe_source'] ) ) {
            $source_object = self::get_source_object( wc_clean( $_POST['stripe_source'] ) );
            $source_id     = $source_object->id;

            // This checks to see if customer opted to save the payment method to file.
            $maybe_saved_card = isset( $_POST[ 'wc-' . $payment_method . '-new-payment-method' ] ) && ! empty( $_POST[ 'wc-' . $payment_method . '-new-payment-method' ] );

            /**
             * This is true if the user wants to store the card to their account.
             * Criteria to save to file is they are logged in, they opted to save or product requirements and the source is
             * actually reusable. Either that or force_save_source is true.
             */
            if ( ( $user_id && $this->saved_cards && $maybe_saved_card && 'reusable' === $source_object->usage ) || $force_save_source ) {
                $response = $customer->add_source( $source_object->id );

            }
        } elseif ( $this->is_using_saved_payment_method() ) {
            // Use an existing token, and then process the payment.
            $wc_token_id = wc_clean( $_POST[ 'wc-' . $payment_method . '-payment-token' ] );
            $wc_token    = WC_Payment_Tokens::get( $wc_token_id );

            if ( ! $wc_token || $wc_token->get_user_id() !== get_current_user_id() ) {
                WC()->session->set( 'refresh_totals', true );

            }

            $source_id = $wc_token->get_token();

            if ( $this->is_type_legacy_card( $source_id ) ) {
                $is_token = true;
            }
        } elseif ( isset( $_POST['stripe_token'] ) && 'new' !== $_POST['stripe_token'] ) {
            $stripe_token     = wc_clean( $_POST['stripe_token'] );
            $maybe_saved_card = isset( $_POST[ 'wc-' . $payment_method . '-new-payment-method' ] ) && ! empty( $_POST[ 'wc-' . $payment_method . '-new-payment-method' ] );

            // This is true if the user wants to store the card to their account.
            if ( ( $user_id && $this->saved_cards && $maybe_saved_card ) || $force_save_source ) {
                $response = $customer->add_source( $stripe_token );

            } else {
                $set_customer = false;
                $source_id    = $stripe_token;
                $is_token     = true;
            }
        }

        if ( ! $set_customer ) {
            $customer_id = false;
        } else {
            $customer_id = $customer->get_id() ? $customer->get_id() : false;
        }

        if ( empty( $source_object ) && ! $is_token ) {
            $source_object = self::get_source_object( $source_id );
        }

        return (object) array(
            'token_id'      => $wc_token_id,
            'customer'      => $customer_id,
            'source'        => $source_id,
            'source_object' => $source_object,
        );
    }


    /**
     * Get source object by source id.
     *
     * @since 4.0.3
     * @param string $source_id The source ID to get source object for.
     */
    public function get_source_object( $source_id = '' ) {
        if ( empty( $source_id ) ) {
            return '';
        }

        $source_object = WC_Stripe_API::retrieve( 'sources/' . $source_id );

        return $source_object;
    }


    /**
     * Updates other subscription sources.
     *
     * @since 3.1.0
     * @version 4.0.0
     */
    public function save_source_to_order( $order, $source ) {
//        parent::save_source_to_order( $order, $source );

        $order_id = WC_Stripe_Helper::is_pre_30() ? $order->id : $order->get_id();

        // Also store it on the subscriptions being purchased or paid for in the order
        if ( function_exists( 'wcs_order_contains_subscription' ) && wcs_order_contains_subscription( $order_id ) ) {
            $subscriptions = wcs_get_subscriptions_for_order( $order_id );
        } elseif ( function_exists( 'wcs_order_contains_renewal' ) && wcs_order_contains_renewal( $order_id ) ) {
            $subscriptions = wcs_get_subscriptions_for_renewal_order( $order_id );
        } else {
            $subscriptions = array();
        }

        foreach ( $subscriptions as $subscription ) {
            $subscription_id = WC_Stripe_Helper::is_pre_30() ? $subscription->id : $subscription->get_id();
            update_post_meta( $subscription_id, '_stripe_customer_id', $source->customer );
            update_post_meta( $subscription_id, '_stripe_source_id', $source->source );
        }
    }

    /**
     * Validates that the order meets the minimum order amount
     * set by Stripe.
     *
     * @since 4.0.0
     * @version 4.0.0
     * @param object $order
     */
    public function validate_minimum_order_amount( $order ) {
        if ( $order->get_total() * 100 < WC_Stripe_Helper::get_minimum_amount() ) {
            /* translators: 1) dollar amount */
        }
    }

    /**
     * Check to see if we need to update the idempotency
     * key to be different from previous charge request.
     *
     * @since 4.1.0
     * @param object $source_object
     * @param object $error
     * @return bool
     */
    public function need_update_idempotency_key( $source_object, $error ) {
        return (
            $error &&
            1 < $this->retry_interval &&
            ! empty( $source_object ) &&
            'chargeable' === $source_object->status &&
            self::is_same_idempotency_error( $error )
        );
    }

    /**
     * Is $order_id a subscription?
     * @param  int  $order_id
     * @return boolean
     */
    public function has_subscription( $order_id ) {
        return ( function_exists( 'wcs_order_contains_subscription' ) && ( wcs_order_contains_subscription( $order_id ) || wcs_is_subscription( $order_id ) || wcs_order_contains_renewal( $order_id ) ) );
    }


    /**
     * Generate the request for the payment.
     *
     * @since 3.1.0
     * @version 4.0.0
     * @param  WC_Order $order
     * @param  object $prepared_source
     * @return array()
     */
    public function generate_payment_request( $order, $prepared_source ) {
        $settings                          = get_option( 'woocommerce_stripe_settings', array() );
        $statement_descriptor              = ! empty( $settings['statement_descriptor'] ) ? str_replace( "'", '', $settings['statement_descriptor'] ) : '';
        $capture                           = ! empty( $settings['capture'] ) && 'yes' === $settings['capture'] ? true : false;
        $post_data                         = array();
        $post_data['currency']             = strtolower( WC_Stripe_Helper::is_pre_30() ? $order->get_order_currency() : $order->get_currency() );
        $post_data['amount']               = WC_Stripe_Helper::get_stripe_amount( $order->get_total(), $post_data['currency'] );
        /* translators: 1) blog name 2) order number */
        $post_data['description']          = sprintf( __( '%1$s - Order %2$s', 'woocommerce-gateway-stripe' ), wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES ), $order->get_order_number() );
        $billing_email      = WC_Stripe_Helper::is_pre_30() ? $order->billing_email : $order->get_billing_email();
        $billing_first_name = WC_Stripe_Helper::is_pre_30() ? $order->billing_first_name : $order->get_billing_first_name();
        $billing_last_name  = WC_Stripe_Helper::is_pre_30() ? $order->billing_last_name : $order->get_billing_last_name();

        if ( ! empty( $billing_email ) && apply_filters( 'wc_stripe_send_stripe_receipt', false ) ) {
            $post_data['receipt_email'] = $billing_email;
        }

        switch ( WC_Stripe_Helper::is_pre_30() ? $order->payment_method : $order->get_payment_method() ) {
            case 'stripe':
                if ( ! empty( $statement_descriptor ) ) {
                    $post_data['statement_descriptor'] = WC_Stripe_Helper::clean_statement_descriptor( $statement_descriptor );
                }

                $post_data['capture'] = $capture ? 'true' : 'false';
            break;
            case 'stripe_sepa':
                if ( ! empty( $statement_descriptor ) ) {
                    $post_data['statement_descriptor'] = WC_Stripe_Helper::clean_statement_descriptor( $statement_descriptor );
                }
            break;
        }

        $post_data['expand[]'] = 'balance_transaction';

        $metadata = array(
            __( 'customer_name', 'woocommerce-gateway-stripe' ) => sanitize_text_field( $billing_first_name ) . ' ' . sanitize_text_field( $billing_last_name ),
            __( 'customer_email', 'woocommerce-gateway-stripe' ) => sanitize_email( $billing_email ),
            'order_id' => $order->get_order_number(),
        );

        if ( $this->has_subscription( WC_Stripe_Helper::is_pre_30() ? $order->get_id() : $order->get_id() ) ) {
            $metadata += array(
                'payment_type' => 'recurring',
                'site_url'     => esc_url( get_site_url() ),
            );
        }

        $post_data['metadata'] = apply_filters( 'wc_stripe_payment_metadata', $metadata, $order, $prepared_source );

        if ( $prepared_source->customer ) {
            $post_data['customer'] = $prepared_source->customer;
        }

        if ( $prepared_source->source ) {
            $post_data['source'] = $prepared_source->source;
        }

        /**
         * Filter the return value of the WC_Payment_Gateway_CC::generate_payment_request.
         *
         * @since 3.1.0
         * @param array $post_data
         * @param WC_Order $order
         * @param object $source
         */
        return apply_filters( 'wc_stripe_generate_payment_request', $post_data, $order, $prepared_source );
    }



    /**
     * Store extra meta data for an order from a Stripe Response.
     */
    public function process_response( $response, $order ) {

        $order_id = WC_Stripe_Helper::is_pre_30() ? $order->id : $order->get_id();

        $captured = ( isset( $response->captured ) && $response->captured ) ? 'yes' : 'no';

        // Store charge data.
        WC_Stripe_Helper::is_pre_30() ? update_post_meta( $order_id, '_stripe_charge_captured', $captured ) : $order->update_meta_data( '_stripe_charge_captured', $captured );

        // Store other data such as fees.
        if ( isset( $response->balance_transaction ) && isset( $response->balance_transaction->fee ) ) {
            // Fees and Net needs to both come from Stripe to be accurate as the returned
            // values are in the local currency of the Stripe account, not from WC.
            $fee = ! empty( $response->balance_transaction->fee ) ? WC_Stripe_Helper::format_balance_fee( $response->balance_transaction, 'fee' ) : 0;
            $net = ! empty( $response->balance_transaction->net ) ? WC_Stripe_Helper::format_balance_fee( $response->balance_transaction, 'net' ) : 0;
            WC_Stripe_Helper::update_stripe_fee( $order, $fee );
            WC_Stripe_Helper::update_stripe_net( $order, $net );

            // Store currency stripe.
            $currency = ! empty( $response->balance_transaction->currency ) ? strtoupper( $response->balance_transaction->currency ) : null;
            WC_Stripe_Helper::update_stripe_currency( $order, $currency );
        }

        if ( 'yes' === $captured ) {
            /**
             * Charge can be captured but in a pending state. Payment methods
             * that are asynchronous may take couple days to clear. Webhook will
             * take care of the status changes.
             */
            if ( 'pending' === $response->status ) {
                $order_stock_reduced = WC_Stripe_Helper::is_pre_30() ? get_post_meta( $order_id, '_order_stock_reduced', true ) : $order->get_meta( '_order_stock_reduced', true );

                if ( ! $order_stock_reduced ) {
                    WC_Stripe_Helper::is_pre_30() ? $order->reduce_order_stock() : wc_reduce_stock_levels( $order_id );
                }

                WC_Stripe_Helper::is_pre_30() ? update_post_meta( $order_id, '_transaction_id', $response->id ) : $order->set_transaction_id( $response->id );
                /* translators: transaction id */
                $order->update_status( 'on-hold', sprintf( __( 'Stripe charge awaiting payment: %s.', 'woocommerce-gateway-stripe' ), $response->id ) );
            }

            if ( 'succeeded' === $response->status ) {
                $order->payment_complete( $response->id );

                /* translators: transaction id */
                $message = sprintf( __( 'Stripe charge complete (Charge ID: %s)', 'woocommerce-gateway-stripe' ), $response->id );
                $order->add_order_note( $message );
            }

            if ( 'failed' === $response->status ) {
                $localized_message = __( 'Payment processing failed. Please retry.', 'woocommerce-gateway-stripe' );
                $order->add_order_note( $localized_message );
            }
        } else {
            WC_Stripe_Helper::is_pre_30() ? update_post_meta( $order_id, '_transaction_id', $response->id ) : $order->set_transaction_id( $response->id );

            if ( $order->has_status( array( 'pending', 'failed' ) ) ) {
                WC_Stripe_Helper::is_pre_30() ? $order->reduce_order_stock() : wc_reduce_stock_levels( $order_id );
            }

            /* translators: transaction id */
            $order->update_status( 'on-hold', sprintf( __( 'Stripe charge authorized (Charge ID: %s). Process order to take payment, or cancel to remove the pre-authorization.', 'woocommerce-gateway-stripe' ), $response->id ) );
        }

        if ( is_callable( array( $order, 'save' ) ) ) {
            $order->save();
        }

        do_action( 'wc_gateway_stripe_process_response', $response, $order );

        return $response;
    }



    /**
     * Process the payment
     *
     * @since 1.0.0
     * @since 4.1.0 Add 4th parameter to track previous error.
     * @param int  $order_id Reference.
     * @param bool $retry Should we retry on fail.
     * @param bool $force_save_source Force save the payment source.
     * @param mix $previous_error Any error message from previous request.
     *
     * @throws Exception If payment will not be accepted.
     *
     * @return array|void
     */
    public function process_payment( $order_id, $retry = true, $force_save_source = false, $previous_error = false ) {
        $order = wc_get_order( $order_id );

        if ( $this->maybe_redirect_stripe_checkout() ) {

            return array(
                'result'   => 'success',
                'redirect' => $order->get_checkout_payment_url( true ),
            );
        }

        if ( $this->maybe_process_pre_orders( $order_id ) ) {
            return $this->pre_orders->process_pre_order( $order_id );
        }

        // This comes from the create account checkbox in the checkout page.
        $create_account = ! empty( $_POST['createaccount'] ) ? true : false;

        if ( $create_account ) {
            $new_customer_id     = WC_Stripe_Helper::is_pre_30() ? $order->customer_user : $order->get_customer_id();
            $new_stripe_customer = new WC_Stripe_Customer( $new_customer_id );
            $new_stripe_customer->create_customer();
        }

        $prepared_source = $this->prepare_source( get_current_user_id(), $force_save_source );
        $source_object   = $prepared_source->source_object;



        $this->save_source_to_order( $order, $prepared_source );

        // Result from Stripe API request.
        $response = null;

        if ( $order->get_total() > 0 ) {
            // This will throw exception if not valid.
            $this->validate_minimum_order_amount( $order );


            /* If we're doing a retry and source is chargeable, we need to pass
             * a different idempotency key and retry for success.
             */
            if ( $this->need_update_idempotency_key( $source_object, $previous_error ) ) {
                add_filter( 'wc_stripe_idempotency_key', array( $this, 'change_idempotency_key' ), 10, 2 );
            }

            // Make the request.
            $response = WC_Stripe_API::request( $this->generate_payment_request( $order, $prepared_source ) );

            if ( ! empty( $response->error ) ) {
                // Customer param wrong? The user may have been deleted on stripe's end. Remove customer_id. Can be retried without.
                if ( $this->is_no_such_customer_error( $response->error ) ) {
                    if ( WC_Stripe_Helper::is_pre_30() ) {
                        delete_user_meta( $order->customer_user, '_stripe_customer_id' );
                        delete_post_meta( $order_id, '_stripe_customer_id' );
                    } else {
                        delete_user_meta( $order->get_customer_id(), '_stripe_customer_id' );
                        $order->delete_meta_data( '_stripe_customer_id' );
                        $order->save();
                    }
                }

                if ( $this->is_no_such_token_error( $response->error ) && $prepared_source->token_id ) {
                    // Source param wrong? The CARD may have been deleted on stripe's end. Remove token and show message.
                    $wc_token = WC_Payment_Tokens::get( $prepared_source->token_id );
                    $wc_token->delete();
                    $localized_message = __( 'This card is no longer available and has been removed.', 'woocommerce-gateway-stripe' );
                    $order->add_order_note( $localized_message );
                }

                // We want to retry.
                if ( $this->is_retryable_error( $response->error ) ) {
                    if ( $retry ) {
                        // Don't do anymore retries after this.
                        if ( 5 <= $this->retry_interval ) {
                            return $this->process_payment( $order_id, false, $force_save_source, $response->error );
                        }

                        sleep( $this->retry_interval );

                        $this->retry_interval++;

                        return $this->process_payment( $order_id, true, $force_save_source, $response->error );
                    } else {
                        $localized_message = __( 'Sorry, we are unable to process your payment at this time. Please retry later.', 'woocommerce-gateway-stripe' );
                        $order->add_order_note( $localized_message );

                    }
                }

                $localized_messages = WC_Stripe_Helper::get_localized_messages();

                if ( 'card_error' === $response->error->type ) {
                    $localized_message = isset( $localized_messages[ $response->error->code ] ) ? $localized_messages[ $response->error->code ] : $response->error->message;
                } else {
                    $localized_message = isset( $localized_messages[ $response->error->type ] ) ? $localized_messages[ $response->error->type ] : $response->error->message;
                }

                $order->add_order_note( $localized_message );


            }

            do_action( 'wc_gateway_stripe_process_payment', $response, $order );

            // Process valid response.
            $this->process_response( $response, $order );
        } else {
            $order->payment_complete();
        }

        // Remove cart.
        WC()->cart->empty_cart();

        // Return thank you page redirect.
        return array(
            'result'   => 'success',
            'redirect' => $this->get_return_url( $order ),
        );
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