<?php

namespace Click2pay_For_WooCommerce\Gateways;

use Click2pay_For_WooCommerce;
use Click2pay_For_WooCommerce\API\Pix_API;
use Click2pay_For_WooCommerce\Traits\Helpers;
use Click2pay_For_WooCommerce\Traits\Logger;
use Exception;
use WC_Payment_Gateway;

defined( 'ABSPATH' ) || exit;

class Pix extends WC_Payment_Gateway {
  use Logger, Helpers;


  public $supports = [
    'products',
    'refunds',
  ];


	/**
	 * Constructor for the gateway.
	 */
	public function __construct() {
		$this->id                   = 'click2pay-pix';
    $this->transaction_type     = 'InstantPayment';
		$this->has_fields           = true;
		$this->method_title         = __( 'Click2pay - Pix', 'click2pay-for-woocommerce' );
		$this->method_description   = __( 'Receba pagamentos instantâneos via Pix.', 'click2pay-for-woocommerce' );
		// $this->view_transaction_url = 'https://beta.dashboard.pagar.me/#/transactions/%s';

		// Load the form fields.
		$this->init_form_fields();

		// Load the settings.
		$this->init_settings();

		// Define user set variables.
		$this->title         = $this->get_option( 'title' );
		$this->description   = $this->get_option( 'description' );
		$this->client_id     = $this->get_option( 'client_id' );
		$this->client_secret = $this->get_option( 'client_secret' );
    $this->prefix        = $this->get_option( 'prefix', 'wc-' );
		$this->expires_in    = $this->get_option( 'expires_in' );
		$this->debug         = $this->get_option( 'debug' );
    $this->sandbox       = $this->get_option( 'sandbox' );

    $this->instructions  = '';

		// Set the API.
		$this->api = new Pix_API( $this );

		// Active logs.
		if ( 'yes' === $this->debug ) {
			$this->set_logger_source( $this->id );
		}

		// Actions.
		add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
		add_action( 'woocommerce_before_thankyou', array( $this, 'thankyou_page' ) );
		add_action( 'woocommerce_email_before_order_table', array( $this, 'email_instructions' ), 10, 3 );

    add_action( 'woocommerce_api_' . $this->id, array( $this, 'ipn_handler' ) );
    add_action( 'woocommerce_api_' . $this->id . '_details', array( $this, 'pix_details_page' ) );

    add_action( 'wp_enqueue_scripts', array( $this, 'wp_enqueue_scripts' ) );

    add_filter( 'woocommerce_my_account_my_orders_actions', array( $this, 'orders_actions' ), 10, 2 );
	}

	/**
	 * Check if the gateway is available to take payments.
	 *
	 * @return bool
	 */
	public function is_available() {
		return parent::is_available() && ! empty( $this->client_id ) && ! empty( $this->client_secret ) && $this->api->using_supported_currency();
	}

	/**
	 * Settings fields.
	 */
	public function init_form_fields() {
		$this->form_fields = array(
			'enabled' => array(
				'title'   => __( 'Ativar método', 'click2pay-for-woocommerce' ),
				'type'    => 'checkbox',
				'label'   => __( 'Habilitar recebimentos via Pix', 'click2pay-for-woocommerce' ),
				'default' => 'no',
			),
			'title' => array(
				'title'       => __( 'Título', 'click2pay-for-woocommerce' ),
				'type'        => 'text',
				'description' => __( 'Isto mostra o título que o usuário vai ver no checkout.', 'click2pay-for-woocommerce' ),
				'desc_tip'    => true,
				'default'     => __( 'Pagamento via Pix', 'click2pay-for-woocommerce' ),
			),
			'description' => array(
				'title'       => __( 'Descrição', 'click2pay-for-woocommerce' ),
				'type'        => 'textarea',
				'description' => __( 'A descrição do pagamento que é exibida no checkout.', 'click2pay-for-woocommerce' ),
				'desc_tip'    => true,
				'default'     => __( 'Pagamento instantâneo via Pix', 'click2pay-for-woocommerce' ),
			),
			'expires_in' => array(
				'title'             => __( 'Validade do Pix, em minutos', 'click2pay-for-woocommerce' ),
				'type'              => 'number',
				'description'       => __( 'Após esse período não será mais possível realizar o pagamento. Padrão 1440 (24 horas).', 'click2pay-for-woocommerce' ),
				'default'           => 1440,
				'custom_attributes' => array(
					'required' => 'required',
          'min'      => 20,
          'step'     => 10,
				),
			),
			'integration' => array(
				'title'       => __( 'Configurações de Integração', 'click2pay-for-woocommerce' ),
				'type'        => 'title',
				'description' => '',
			),
			'client_id' => array(
				'title'             => __( 'Client ID', 'click2pay-for-woocommerce' ),
				'type'              => 'text',
				'description'       => __( 'Chave fornecida pela Click2pay', 'click2pay-for-woocommerce' ),
				'default'           => '',
				'custom_attributes' => array(
					'required' => 'required',
				),
			),
			'client_secret' => array(
				'title'             => __( 'Client Secret', 'click2pay-for-woocommerce' ),
				'type'              => 'text',
				'description'       => __( 'Chave fornecida pela Click2pay', 'click2pay-for-woocommerce' ),
				'default'           => '',
				'custom_attributes' => array(
					'required' => 'required',
				),
			),
			'prefix' => array(
				'title'             => __( 'Prefixo do pedido', 'click2pay-for-woocommerce' ),
				'type'              => 'text',
				'description'       => __( 'Adicione um prefixo único ao ID do pedido enviado à Click2Pay.', 'click2pay-for-woocommerce' ),
				'default'           => 'wc-',
				'custom_attributes' => array(
					'required' => 'required',
				),
			),
			'testing' => array(
				'title'       => __( 'Teste do Gateway', 'click2pay-for-woocommerce' ),
				'type'        => 'title',
				'description' => '',
			),
			'debug' => array(
				'title'       => __( 'Log de depuração', 'click2pay-for-woocommerce' ),
				'type'        => 'checkbox',
				'label'       => __( 'Habilitar logs', 'click2pay-for-woocommerce' ),
				'default'     => 'no',
				'description' => sprintf( __( 'Registra eventos deste método de pagamento, como requisições na API. Você pode verificar o log em %s', 'click2pay-for-woocommerce' ), '<a target="_blank" href="' . esc_url( admin_url( 'admin.php?page=wc-status&tab=logs&log_file=' . esc_attr( $this->id ) . '-' . sanitize_file_name( wp_hash( $this->id ) ) . '.log' ) ) . '">' . __( 'Status do Sistema &gt; Logs', 'click2pay-for-woocommerce' ) . '</a>' ),
			),
			'sandbox' => array(
				'title'       => __( 'Sandbox', 'click2pay-for-woocommerce' ),
				'type'        => 'checkbox',
				'label'       => __( 'Usar plugin em modo de testes', 'click2pay-for-woocommerce' ),
				'default'     => 'no',
				'description' => __( 'Neste caso, as transações não serão realmente processadas. Utilize dados de teste.', 'click2pay-for-woocommerce' ),
			),
		);
	}

	/**
	 * Payment fields.
	 */
	public function payment_fields() {
		if ( $description = $this->get_description() ) {
			echo wp_kses_post( wpautop( wptexturize( $description ) ) );
		}
	}

	/**
	 * Process the payment.
	 *
	 * @param int $order_id Order ID.
	 *
	 * @return array Redirect data.
	 */
	public function process_payment( $order_id ) {
    $order = wc_get_order( $order_id );
		$response_data = $this->api->create_transaction( $order );

    if ( isset( $response_data->error, $response_data->errorDescription ) ) {
      throw new Exception( sprintf( __( 'Ocorreu um erro ao processar sua solicitação: %s', 'click2pay-for-woocommerce' ), $response_data->errorDescription ) );
    }

    $data = $response_data->data;

    $order->set_transaction_id( $data->tid );
    $order->update_meta_data( '_click2pay_external_identifier', $data->externalIdentifier );
    $order->update_meta_data( '_click2pay_data', $data );
    $order->update_meta_data( '_click2pay_pix_copy_paste', $data->pix->textPayment );
    $order->update_meta_data( '_click2pay_pix_image', $data->pix->qrCodeImage->base64 );
    $order->update_meta_data( '_click2pay_pix_minutes_to_expire', $this->expires_in );

    $order->set_status( 'on-hold', sprintf( __( 'Pagamento iniciado com Pix. Link de pagamento do cliente: <code>%s</code>', 'click2pay-for-woocommerce' ), '<a target="_blank" href="' . $this->get_pix_details_page( $order ) . '">' . __( 'Acessar página', 'click2pay-for-woocommerce' ) . '</a>' ) );

    $order->save();

    return [
      'result'   => 'success',
      'redirect' => $this->get_return_url( $order ),
    ];
	}




	/**
	 * Process refund.
	 *
	 * If the gateway declares 'refunds' support, this will allow it to refund.
	 * a passed in amount.
	 *
	 * @param  int        $order_id Order ID.
	 * @param  float|null $amount Refund amount.
	 * @param  string     $reason Refund reason.
	 * @return boolean True or false based on success, or a WP_Error object.
	 */
	public function process_refund( $order_id, $amount = null, $reason = '' ) {
    $order = wc_get_order( $order_id );
		$response_data = $this->api->refund_transaction( $order, $amount );

    if ( isset( $response_data->error, $response_data->errorDescription ) ) {
      throw new Exception( sprintf( __( 'Ocorreu um erro ao processar o reembolso: %s', 'click2pay-for-woocommerce' ), $response_data->errorDescription ) );
    }

    $order->add_order_note( sprintf( __( 'Processado reembolso automático de %s. %s', 'click2pay-for-woocommerce' ), wc_price( $amount ), $reason ) );

    $order->update_meta_data( '_click2pay_refund_data', $response_data );
    $order->save();

		return true;
	}


	/**
	 * Thank You page message.
	 *
	 * @param int $order_id Order ID.
	 */
	public function thankyou_page( $order_id ) {
		$order = wc_get_order( $order_id );

    if ( ! $order ) {
      return;
    }

    if ( $this->id !== $order->get_payment_method() ) {
      return;
    }

    wp_enqueue_script( 'click2pay-pix' );

		wc_get_template(
			'checkout/click2pay/pix.php',
			[
        'id' => $this->id,
        'instructions' => $this->instructions,
        'is_email' => false,
        'order' => $order,
        'payload' => $order->get_meta( '_click2pay_pix_copy_paste' ),
        'pix_image' => $order->get_meta( '_click2pay_pix_image' ),
        'pix_minutes_to_expire' => $order->get_meta( '_click2pay_pix_minutes_to_expire' ),
        'pix_details_page' => $this->get_pix_details_page( $order ),
      ],
			'',
			Click2pay_For_WooCommerce::get_templates_path()
		);
	}

	/**
	 * Add content to the WC emails.
	 *
	 * @param  object $order         Order object.
	 * @param  bool   $sent_to_admin Send to admin.
	 * @param  bool   $plain_text    Plain text or HTML.
	 *
	 * @return string                Payment instructions.
	 */
	public function email_instructions( $order, $sent_to_admin, $plain_text = false ) {
		if ( $sent_to_admin || ! $order->has_status( [ 'on-hold' ] ) || $this->id !== $order->get_payment_method() ) {
			return;
		}

		wc_get_template(
			'checkout/click2pay/pix.php',
			[
        'id' => $this->id,
        'instructions' => $this->instructions,
        'is_email' => true,
        'order' => $order,
        'payload' => $order->get_meta( '_click2pay_pix_copy_paste' ),
        'pix_image' => $order->get_meta( '_click2pay_pix_image' ),
        'pix_minutes_to_expire' => $order->get_meta( '_click2pay_pix_minutes_to_expire' ),
        'pix_details_page' => $this->get_pix_details_page( $order ),
      ],
			'',
			Click2pay_For_WooCommerce::get_templates_path()
		);
	}

	/**
	 * IPN handler.
	 */
	public function ipn_handler() {
		$this->api->ipn_handler();
	}


  public function wp_enqueue_scripts() {
    wp_register_script(
      'click2pay-clipboard',
      Click2pay_For_WooCommerce::plugin_url() . '/assets/vendor/clipboard.min.js',
      [ 'jquery' ],
      '2.0.10',
      true
    );

    wp_register_script(
      'click2pay-pix',
      Click2pay_For_WooCommerce::plugin_url() . '/assets/js/gateways/pix.js',
      [ 'click2pay-clipboard' ],
      Click2pay_For_WooCommerce::VERSION,
      true
    );
  }


  public function pix_details_page() {
		try {

			if ( ! isset( $_GET['id'], $_GET['key'] ) ) {
				throw new \Exception( __( 'URL inválida' ) );
			}

			$order_id = esc_attr( $_GET['id'] );
			$order_key = esc_attr( $_GET['key'] );

			$order = wc_get_order( $order_id );

			if ( ! $order ) {
				throw new \Exception( __( 'Pedido não encontrado' ) );
			}

			if ( $order->get_order_key() !== $order_key ) {
				throw new \Exception( __( 'Sem permissões para visualizar esta página' ) );
			}

			get_header();

			echo '<div class="woocommerce-thankyou-order-received"></div>';

			$this->thankyou_page( $order->get_id() );

			get_footer();

			exit;

		} catch (\Exception $e) {
			wp_die( $e->getMessage() );
		}
  }



  public function get_pix_details_page( $order ) {
    return apply_filters(
      'click2pay_pix_url',
      add_query_arg(
        array(
          'id'  => $order->get_id(),
          'key' => $order->get_order_key(),
        ),
        WC()->api_request_url( $this->id . '_details' )
      ),
      $order
    );
  }


  public function orders_actions( $actions, $order ) {
    if ( $this->id !== $order->get_payment_method() ) {
      return $actions;
    }

    if ( ! $order->has_status( [ 'on-hold' ] ) ) {
      return $actions;
    }

    $expires_in = $order->get_meta( '_click2pay_pix_minutes_to_expire' );

    if ( ! $expires_in ) {
      return $actions;
    }

    $created_at = $order->get_date_created();

    $now = new \WC_DateTime();

    $created_at->modify( '+' . $expires_in . ' minutes' );

    if ( '+' === $created_at->diff( $now )->format( '%R' ) ) {
      return $actions;
    }

    $actions[ $this->id ] = array(
			'url'  => $this->get_pix_details_page( $order ),
			'name' => __( 'Acessar Pix', 'click2pay-for-woocommerce' ),
		);

    return $actions;
  }
}
