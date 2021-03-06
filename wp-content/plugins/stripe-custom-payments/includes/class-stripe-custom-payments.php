<?php

/**
 * The file that defines the core plugin class
 */


class Stripe_Custom {


    protected $loader;
    protected $stripe_custom;

    protected $version;

    public function __construct() {

        $this->stripe_custom = 'stripe-custom-payments';
        $this->version = '1.0.0';




        add_action( 'plugins_loaded', array( $this, 'setup_wc_stripe' ) );


    }


    public function setup_wc_stripe(){


        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wc-gateway-stripe.php';

        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wc-stripe-api.php';

        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wc-stripe-helper.php';

        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wc-stripe-pre-orders-compat.php';

        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wc-stripe-payment-request.php';

        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wc-stripe-customer.php';



        add_filter( 'woocommerce_payment_gateways', array( $this, 'add_gateways' ) );




        add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'plugin_action_links' ) );


        add_filter( 'woocommerce_get_sections_checkout', array( $this, 'filter_gateway_order_admin' ) );

    }


    /**
     * Add the gateways to WooCommerce.
     *
     * @since 1.0.0
     * @version 4.0.0
     */
    public function add_gateways( $methods ) {

        $methods[] = 'WC_Gateway_Stripe';

        return $methods;
    }


    /**
     * Adds plugin action links.
     *
     * @since 1.0.0
     * @version 4.0.0
     */
    public function plugin_action_links( $links ) {
        $plugin_links = array(
            '<a href="admin.php?page=wc-settings&tab=checkout&section=stripe">' . esc_html__( 'Settings', 'woocommerce-gateway-stripe' ) . '</a>',

        );
        return array_merge( $plugin_links, $links );
    }


    /**
     * Modifies the order of the gateways displayed in admin.
     *
     * @since 4.0.0
     * @version 4.0.0
     */
    public function filter_gateway_order_admin( $sections ) {
        unset( $sections['stripe'] );


        $sections['stripe'] = 'Stripe';


        return $sections;
    }




    public function get_plugin_name() {
        return $this->stripe_custom;
    }

    public function get_loader() {
        return $this->loader;
    }

    public function get_version() {
        return $this->version;
    }

}
