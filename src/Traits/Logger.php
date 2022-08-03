<?php

namespace Click2pay_For_WooCommerce\Traits;

use WC_Logger;

defined( 'ABSPATH' ) || exit;

trait Logger {
  /**
   * $source
   *
   * @var string
   */
  public $source;


  /**
   * $log
   *
   * @var WC_Logger
   */
  public static $log;


  public function set_logger_source( $set ) {
    $this->source = $set;
  }


  /**
	 * Log an event.
	 *
	 * @param string $message Log message.
	 * @param string $level Optional, defaults to info, valid levels: emergency|alert|critical|error|warning|notice|info|debug.
	 */
  public function log( $message, $level = 'info' ) {
    if ( ! $this->source ) {
      return;
    }

    $message = is_string( $message ) ? $message : print_r( $message, true );

		if ( ! isset( self::$log ) ) {
			self::$log = wc_get_logger();
		}

		self::$log->log( $level, $message, array( 'source' => $this->source ) );
  }
}
