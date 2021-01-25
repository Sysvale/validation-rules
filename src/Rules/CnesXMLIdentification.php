<?php

namespace Sysvale\ValidationRules\Rules;

use SimpleXMLElement;
use Illuminate\Contracts\Validation\Rule;
use Sysvale\ValidationRules\Support\ZipWithXMLHandler;

class CnesXMLIdentification implements Rule
{
	private $expected_ibge_code;

	public function __construct($expected_ibge_code)
	{
		$this->expected_ibge_code = $expected_ibge_code;
	}

	/**
	 * Determine if the validation rule passes.
	 *
	 * @param  string  $attribute
	 * @param  mixed  $value
	 * @return bool
	 */
	public function passes($attribute, $value)
	{
		$zip_handler = resolve(ZipWithXMLHandler::class)
			->buildZip($value->path());

		$passes = $this->hasValidIdentification($zip_handler->getSimpleXMLElement());

		$zip_handler->closeZip();

		return $passes;
	}

	/**
	 * Get the validation error message.
	 *
	 * @return string
	 */
	public function message()
	{
		return __('SysvaleValidationRules::messages.cnes_xml_identification');
	}


	protected function hasValidIdentification(SimpleXMLElement $xml)
	{
		foreach ($xml->children() as $key => $identification) {
			if ($key !== 'IDENTIFICACAO') {
				return false;
			}

			$origin = (string) $identification['ORIGEM'];
			$target = (string) $identification['DESTINO'];
			$ibge_code = (string) $identification['CO_IBGE_MUN'];

			if ($origin === 'PORTAL'
				&& $target === 'ESUS_AB'
				&& $ibge_code === $this->expected_ibge_code
			) {
				return true;
			}
		}

		return false;
	}
}
