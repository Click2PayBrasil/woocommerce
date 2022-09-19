<?php
/**
 * Plugin Name:          Click2pay para WooCommerce
 * Plugin URI:           https://fernandoacosta.net
 * Description:          Receba pagamentos via Pix, Cartão de crédito e boleto
 * Author:               Fernando Acosta
 * Author URI:           https://fernandoacosta.net
 * Version:              1.0.4
 * License:              GPLv2 or later
 * WC requires at least: 4.0.0
 * WC tested up to:      6.8.2
 *
 * This plugin is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 *
 * This plugin is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this plugin. If not, see
 * <https://www.gnu.org/licenses/gpl-2.0.txt>.
 *
 * @package Fernando_Acosta
 */

use Click2pay_For_WooCommerce\Credit_Card\WC_Payment_Token_CC_Click2pay;
use Click2pay_For_WooCommerce\Gateways;

if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly.
}

class Click2pay_For_WooCommerce {
  /**
   * Version.
   *
   * @var float
   */
  const VERSION = '1.0.4';

  /**
   * Instance of this class.
   *
   * @var object
   */
  protected static $instance = null;
  /**
   * Initialize the plugin public actions.
   */
  function __construct() {
    $this->init();
  }

  public function init() {
    $file = __DIR__ . '/vendor/autoload.php';

    if ( file_exists( $file ) ) {
      require_once $file;
    } else {
      // display message
      return;
    }

    if ( is_admin() ) {
      $this->admin_features();
    }

    add_filter( 'woocommerce_payment_gateways', array( $this, 'add_gateway' ) );

    add_filter( 'woocommerce_payment_methods_types', array( $this, 'register_custom_payment_method_type' ) );
    add_filter( 'woocommerce_payment_token_class', array( $this, 'register_token_classname' ), 10, 2 );
  }

  /**
   *
   * Admin includes
   */
  public function admin_features() {

  }


  /**
   * Return an instance of this class.
   *
   * @return object A single instance of this class.
   */
  public static function get_instance() {
    // If the single instance hasn't been set, set it now.
    if ( null == self::$instance ) {
      self::$instance = new self;
    }
    return self::$instance;
  }

  /**
   * Get main file.
   *
   * @return string
   */
  public static function get_main_file() {
    return __FILE__;
  }

  /**
   * Get plugin path.
   *
   * @return string
   */

  public static function get_plugin_path() {
    return plugin_dir_path( __FILE__ );
  }

  /**
   * Get the plugin url.
   * @return string
   */
  public static function plugin_url() {
    return untrailingslashit( plugins_url( '/', __FILE__ ) );
  }

  /**
   * Get the plugin dir url.
   * @return string
   */
  public static function plugin_dir_url() {
    return plugin_dir_url( __FILE__ );
  }

  /**
   * Get templates path.
   *
   * @return string
   */
  public static function get_templates_path() {
    return self::get_plugin_path() . 'templates/';
  }

  /**
   * Add the gateway to WooCommerce.
   *
   * @param  array $methods WooCommerce payment methods.
   *
   * @return array
   */
  public function add_gateway( $methods ) {
    $methods[] = Gateways\Credit_Card::class;
    $methods[] = Gateways\Pix::class;
    $methods[] = Gateways\Bank_Slip::class;

    return $methods;
  }

  public function register_custom_payment_method_type( $types ) {
    $types['CC_Click2pay'] = __( 'Cartão de crédito - Click2pay', 'click2pay-for-woocommerce' );

    return $types;
  }

  public function register_token_classname( $classname, $type ) {
    if ( 'CC_Click2pay' === $type ) {
      return WC_Payment_Token_CC_Click2pay::class;
    }

    return $classname;
  }
}

add_action( 'plugins_loaded', array( 'Click2pay_For_WooCommerce', 'get_instance' ) );
