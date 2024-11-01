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

namespace Ticoseo\Common;

use Ticoseo\App\Frontend\Templates;
use Ticoseo\Common\Abstracts\Base;

/**
 * Main function class for external uses
 *
 * @see ticoseo()
 * @package Ticoseo\Common
 */
class Functions extends Base {
	/**
	 * Get plugin data by using ticoseo()->getData()
	 *
	 * @return array
	 * @since 1.0.0
	 */
	public function getData(): array {
		return $this->plugin->data();
	}

	/**
	 * Get the template instantiated class using ticoseo()->templates()
	 *
	 * @return Templates
	 * @since 1.0.0
	 */
	public function templates(): Templates {
		return new Templates();
	}
}
