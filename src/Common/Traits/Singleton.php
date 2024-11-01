<?php
/**
 * TicoSEO
 *
 * @package   ticoseo
 * @author    Kamil Baranek <kamil@ticoseo.com>
 * @copyright 2023 TicoSEO
 * @license   MIT
 * @link      https://ticoseo.com
 */

declare( strict_types = 1 );

namespace Ticoseo\Common\Traits;

/**
 * The singleton skeleton trait to instantiate the class only once
 *
 * @package Ticoseo\Common\Traits
 * @since 1.0.0
 */
trait Singleton {
	private static $instance;

	final private function __construct() {
	}

	final private function __clone() {
	}

	final public function __wakeup() {
	}

	/**
	 * @return self
	 * @since 1.0.0
	 */
	final public static function init(): self {
		if ( ! self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}
}
