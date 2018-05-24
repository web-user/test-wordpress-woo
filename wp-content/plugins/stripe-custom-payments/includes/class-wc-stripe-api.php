<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * WC_Stripe_API class.
 *
 * Communicates with Stripe API.
 */
class WC_Stripe_API {

    /**
     * Stripe API Endpoint
     */
    const ENDPOINT = 'https://api.stripe.com/v1/';
    const STRIPE_API_VERSION = '2018-02-28';

    /**
     * Secret API Key.
     * @var string
     */
    private static $secret_key = '';

    /**
     * Set secret API Key.
     * @param string $key
     */
    public static function set_secret_key( $secret_key ) {
        self::$secret_key = $secret_key;
    }

    /**
     * Get secret key.
     * @return string
     */
    public static function get_secret_key() {
        if ( ! self::$secret_key ) {
            $options = get_option( 'woocommerce_stripe_settings' );

            if ( isset( $options['testmode'], $options['secret_key'], $options['test_secret_key'] ) ) {
                self::set_secret_key( 'yes' === $options['testmode'] ? $options['test_secret_key'] : $options['secret_key'] );
            }
        }
        return self::$secret_key;
    }

    /**
     * Generates the user agent we use to pass to API request so
     * Stripe can identify our application.
     *
     * @since 4.0.0
     * @version 4.0.0
     */
    public static function get_user_agent() {
        $app_info = array(
            'name'    => 'WooCommerce Stripe Gateway',
            'version' => '1.0.0',
            'url'     => 'https://woocommerce.com/products/stripe/',
        );

        return array(
            'lang'         => 'php',
            'lang_version' => phpversion(),
            'publisher'    => 'woocommerce',
            'uname'        => php_uname(),
            'application'  => $app_info,
        );
    }

    /**
     * Generates the headers to pass to API request.
     *
     * @since 4.0.0
     * @version 4.0.0
     */
    public static function get_headers() {
        $user_agent = self::get_user_agent();
        $app_info   = $user_agent['application'];

        return apply_filters( 'woocommerce_stripe_request_headers', array(
            'Authorization'              => 'Basic ' . base64_encode( self::get_secret_key() . ':' ),
            'Stripe-Version'             => self::STRIPE_API_VERSION,
            'User-Agent'                 => $app_info['name'] . '/' . $app_info['version'] . ' (' . $app_info['url'] . ')',
            'X-Stripe-Client-User-Agent' => json_encode( $user_agent ),
        ) );
    }

    /**
     * Send the request to Stripe's API
     *
     * @since 3.1.0
     * @version 4.0.6
     * @param array $request
     * @param string $api
     * @param bool $with_headers To get the response with headers.
     * @return array|WP_Error
     */
    public static function request( $request, $api = 'charges', $method = 'POST', $with_headers = false ) {


        $headers = self::get_headers();

        if ( 'charges' === $api && 'POST' === $method ) {
            $customer = ! empty( $request['customer'] ) ? $request['customer'] : '';
            $source   = ! empty( $request['source'] ) ? $request['source'] : $customer;

            $headers['Idempotency-Key'] = apply_filters( 'wc_stripe_idempotency_key', $request['metadata']['order_id'] . '-' . $source, $request );
        }

        $response = wp_safe_remote_post(
            self::ENDPOINT . $api,
            array(
                'method'  => $method,
                'headers' => $headers,
                'body'    => apply_filters( 'woocommerce_stripe_request_body', $request, $api ),
                'timeout' => 70,
            )
        );


        if ( $with_headers ) {
            return array( 'headers' => wp_remote_retrieve_headers( $response ), 'body' => json_decode( $response['body'] ) );
        }

        return json_decode( $response['body'] );
    }

    /**
     * Retrieve API endpoint.
     *
     * @since 4.0.0
     * @version 4.0.0
     * @param string $api
     */
    public static function retrieve( $api ) {


        $response = wp_safe_remote_get(
            self::ENDPOINT . $api,
            array(
                'method'  => 'GET',
                'headers' => self::get_headers(),
                'timeout' => 70,
            )
        );

        return json_decode( $response['body'] );
    }
}