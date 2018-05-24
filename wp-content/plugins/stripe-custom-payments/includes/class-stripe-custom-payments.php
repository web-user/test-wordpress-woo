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
        $this->load_dependencies();


    }


    private function load_dependencies() {

        /**
         * The class responsible for orchestrating the actions and filters of the
         * core plugin.
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-stripe-custom-payments-loader.php';





        $this->loader = new Stripe_Custom_Loader();

    }


    public function run() {
        $this->loader->run();
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
