<?php

namespace Sysvale\ValidationRules\Rules;

abstract class CnesXMLRule
{
	protected function getIdentificationAttributes($xml)
	{
		$identification = $xml->{'IDENTIFICACAO'};

		if (empty($identification)) {
			return [];
		}

		return array_values((array) $identification->attributes())[0];
	}
}
