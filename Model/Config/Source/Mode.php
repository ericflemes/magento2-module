<?php

namespace PayPalBR\PayPalPlus\Model\Config\Source;

class Mode implements \Magento\Framework\Option\ArrayInterface
{
	/*
	* Option getter
	* @return array
	*/
	public function toOptionArray()
	{
		$options = [
			"1" => __("SandBox"),
			"2" => __("Production")
		];

		$ret = [];
		foreach ($options as $key => $value) {
			$ret[] = [
				"value" => $key,
				"label" => $value
			];
		}

		return $ret;
	}
}