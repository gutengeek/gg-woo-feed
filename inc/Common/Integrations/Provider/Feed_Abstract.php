<?php
namespace GG_Woo_Feed\Common\Integrations\Provider;

abstract class Feed_Abstract {
	abstract public function get_frame();

	abstract public function map_xml();

	abstract public function map_csv_txt();

	abstract public function map_atts( $no, $from, $to, $value, $cdata = false );
}
