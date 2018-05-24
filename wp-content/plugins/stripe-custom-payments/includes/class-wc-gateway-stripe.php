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

    /**
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

        // Load the form fields.
        $this->form_fields = array(
            'enabled' => array(
                'title' => __( 'Enable/Disable', 'woocommerce' ),
                'type' => 'checkbox',
                'label' => __( 'Enable Cheque Payment', 'woocommerce' ),
                'default' => 'yes'
            ),
            'title' => array(
                'title' => __( 'Title', 'woocommerce' ),
                'type' => 'text',
                'description' => __( 'This controls the title which the user sees during checkout.', 'woocommerce' ),
                'default' => __( 'Cheque Payment', 'woocommerce' ),
                'desc_tip'      => true,
            ),
            'description' => array(
                'title' => __( 'Customer Message', 'woocommerce' ),
                'type' => 'textarea',
                'default' => ''
            )
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




    }








}