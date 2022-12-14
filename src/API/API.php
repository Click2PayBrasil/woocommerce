<?php

namespace Click2pay_For_WooCommerce\API;

use Click2pay_For_WooCommerce;
use Exception;

defined( 'ABSPATH' ) || exit;

abstract class API {
	/**
	 * Gateway class.
	 *
	 * @var WC_Payment_Gateway
	 */
	protected $gateway;

	/**
	 * API URL.
	 *
	 * @var string
	 */
	protected $api_url = 'https://api.click2pay.com.br/v1/';

	/**
	 * Sandbox API URL.
	 *
	 * @var string
	 */
	protected $sandbox_api_url = 'https://apisandbox.click2pay.com.br/v1/';

	/**
	 * JS Library URL.
	 *
	 * @var string
	 */
	protected $js_url = 'https://api.click2pay.com.br/c2p/integrations/public/cardc2p.js';

	/**
	 * JS Library URL.
	 *
	 * @var string
	 */
	protected $sandbox_js_url = 'https://apisandbox.click2pay.com.br/c2p/integrations/public/cardc2p.js';


	/**
	 * Constructor.
	 *
	 * @param WC_Payment_Gateway $gateway Gateway instance.
	 */
	public function __construct( $gateway = null ) {
		$this->gateway = $gateway;
	}


	/**
	 * Using the gateway in test mode?
	 *
	 * @return bool
	 */
  public function is_sandbox() {
    return 'yes' === $this->gateway->sandbox;
  }


	/**
	 * Get API URL.
	 *
	 * @return string
	 */
	public function get_api_url() {
		return $this->is_sandbox() ? $this->sandbox_api_url : $this->api_url;
	}

	/**
	 * Get JS Library URL.
	 *
	 * @return string
	 */
	public function get_js_url() {
    return $this->is_sandbox() ? $this->sandbox_js_url : $this->js_url;
	}

	/**
	 * Returns a bool that indicates if currency is amongst the supported ones.
	 *
	 * @return bool
	 */
	public function using_supported_currency() {
		return 'BRL' === get_woocommerce_currency();
	}


  /**
	 * Process API Requests.
	 *
	 * @param  string $url      URL.
   * @param  array  $data     Request data.
	 * @param  string $method   Request method.
	 * @param  array  $headers  Request headers.
	 *
	 * @return object|WP_Error            Request response.
	 */
	protected function do_request( $endpoint, $data = [], $method = 'POST', $headers = [] ) {
		$url = $this->get_api_url() . $endpoint;

		// Pagar.me user-agent and api version.
		$useragent = 'click2pay-for-woocommerce/' . Click2pay_For_WooCommerce::VERSION;

		if ( defined( 'WC_VERSION' ) ) {
			$useragent .= ' woocommerce/' . WC_VERSION;
		}

		$useragent .= ' wordpress/' . get_bloginfo( 'version' );
		$useragent .= ' php/' . phpversion();

		$params = [
			'method'  => $method,
			'timeout' => 60,
			'headers' => [
				'Accept'       => 'application/json',
				'Content-Type' => 'application/json',
        'User-Agent'   => $useragent,
        'Authorization' => 'Basic ' . base64_encode( $this->gateway->client_id . ':' . $this->gateway->client_secret ),
			],
		];

		if ( 'POST' === $method && ! empty( $data ) ) {
			$params['body'] = wp_json_encode( $data );
		}

		if ( ! empty( $headers ) ) {
			$params['headers'] = wp_parse_args( $headers, $params['headers'] );
		}

    $this->gateway->log( $url . ' (' . $method . '):' . print_r( $params, true ) );

		$response = wp_safe_remote_post( $url, $params );

    if ( is_wp_error( $response ) ) {
      $this->gateway->log( 'WP_Error: ' . $response->get_error_message() );

      throw new Exception( sprintf( __( 'Ocorreu um erro interno: %s', 'click2pay-for-woocommerce' ) ) );
    }

    if ( ! $response ) {
      $this->gateway->log( 'Erro no requisi????o: resposta em branco.' );

      throw new Exception( __( 'Ocorreu um erro interno. Entre em contato para obter assist??ncia.', 'click2pay-for-woocommerce' ) );
    }

    $response_code = wp_remote_retrieve_response_code( $response );

    if ( 401 === $response_code ) {
      $this->gateway->log( 'Erro no requisi????o: credenciais inv??lidas' );

      throw new Exception( __( 'Ocorreu um erro de autentica????o. Entre em contato para obter assit??ncia', 'click2pay-for-woocommerce' ) );
    }

    return $response;
	}




	/**
	 * IPN handler.
	 */
	public function ipn_handler() {
		@ob_clean();

    try {
      $this->validate_request();

      $data = json_decode( file_get_contents( 'php://input' ) );

      $this->gateway->log( 'Webhook recebido:' . print_r( $data, true ) );

      if ( $this->gateway->transaction_type !== $data->transaction_type ) {
        $this->gateway->log( 'Tipo de transa????o inv??lida para este gateway:' . $this->gateway->transaction_type );

        throw new Exception( __( 'Tipo de transa????o inv??lida', 'click2pay-for-woocommerce' ) );
      }

      if ( ! in_array( $data->type, [ 'PAYMENT_RECEIVED', 'PAYMENT_REFUNDED' ] ) ) {
        $this->gateway->log( 'Tipo de evento inv??lido:' . $data->type );

        throw new Exception( __( 'Tipo de evento inv??lido', 'click2pay-for-woocommerce' ) );
      }

      $order_id = $this->get_order_id_by_transaction( $data->tid );

      if ( ! $order_id ) {
        $this->gateway->log( 'Transa????o n??o encontrada:' . $data->tid );

        throw new Exception( __( 'Transa????o n??o encontrada', 'click2pay-for-woocommerce' ) );
      }

      $order = wc_get_order( $order_id );

      if ( ! $order ) {
        $this->gateway->log( 'Pedido inv??lido:' . $order_id );

        throw new Exception( __( 'Pedido inv??lido', 'click2pay-for-woocommerce' ) );
      }

      if ( 'PAYMENT_RECEIVED' === $data->type && 'paid' === $data->status ) {
        $this->gateway->log( 'Pedido Pago! #' . $order_id );

        if ( ! $order->is_paid() ) {
          $order->add_order_note( __( 'Notifica????o de pagamento recebida', 'click2pay-for-woocommerce' ) );
          $order->update_meta_data( '_click2pay_payment_webhook_data', $data );
          $order->payment_complete();
        } else {
          $order->add_order_note( __( 'Nova notifica????o de pagamento recebida', 'click2pay-for-woocommerce' ) );
        }

      } else if ( 'PAYMENT_REFUNDED' === $data->type ) {
        $this->gateway->log( 'Pedido reembolsado! #' . $order_id );
        // handle it!
      } else {
        $this->gateway->log( 'No handler found:' . print_r( $data, true ) );
      }

      wp_die(
        __( 'Webhook received', 'click2pay-for-woocommerce' ),
        __( 'Webhook response', 'click2pay-for-woocommerce' ),
        array( 'response' => 200, 'code' => 'success' )
      );

    } catch ( Exception $e ) {
      wp_die(
        $e->getMessage(),
        __( 'Webhook response', 'click2pay-for-woocommerce' ),
        array( 'response' => $e->getCode() ? $e->getCode() : 400, 'code' => 'error' )
      );
    }
	}


  private function validate_request() {
		// Validate user secret.
		if ( ! hash_equals( base64_encode( $this->gateway->client_id ), $this->get_authorization_header() ) ) { // @codingStandardsIgnoreLine
      throw new Exception( __( 'N??o autorizado.', 'click2pay-for-woocommerce' ), 401 );
    }

    return true;
  }



	/**
	 * Get the authorization header.
	 *
	 * On certain systems and configurations, the Authorization header will be
	 * stripped out by the server or PHP. Typically this is then used to
	 * generate `PHP_AUTH_USER`/`PHP_AUTH_PASS` but not passed on. We use
	 * `getallheaders` here to try and grab it out instead.
	 *
	 * @since 3.0.0
	 *
	 * @return string Authorization header if set.
	 */
	public function get_authorization_header() {
		if ( ! empty( $_SERVER['HTTP_C2P_HASH'] ) ) {
			return wp_unslash( $_SERVER['HTTP_C2P_HASH'] ); // WPCS: sanitization ok.
		}

		if ( function_exists( 'getallheaders' ) ) {
			$headers = getallheaders();
      $k = '';
			// Check for the authoization header case-insensitively.
			foreach ( $headers as $key => $value ) {
				if ( 'c2p-hash' === strtolower( $key ) ) {
					return wp_unslash( $value );
				}
			}
		}

		return '';
  }


  public function get_order_id_by_transaction( $transaction_id ) {
    global $wpdb;

    $query = apply_filters(
      'click2pay_for_woocommerce_order_id_by_transaction_query',
      $wpdb->prepare( "SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = '_transaction_id' AND meta_value = %s", $transaction_id ),
      $transaction_id
    );

    $value = (int) $wpdb->get_var( $query );

    return $value;
  }
}
