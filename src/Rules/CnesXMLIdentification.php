<?php

namespace Sysvale\ValidationRules\Rules;

use SimpleXMLElement;
use Illuminate\Contracts\Validation\Rule;
use Sysvale\ValidationRules\Support\ZipWithXMLHandler;

class CnesXMLIdentification implements Rule
{
	private $expected_ibge_code;

	public function __construct($expected_ibge_code, $version_xsd)
	{
		$this->expected_ibge_code = $expected_ibge_code;
		$this->version_xsd = $version_xsd;
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
		return __('SysvaleValidationRules::messages.cnes_xml_identification', [
			'version_xsd' => $this->version_xsd,
		]);
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
			$version_xsd = (string) $identification['VERSION_XSD'];

			if ($origin === 'PORTAL'
				&& $target === 'ESUS_AB'
				&& $ibge_code === $this->expected_ibge_code
				&& $version_xsd === $this->version_xsd
			) {
				return true;
			}
		}

		return false;
	}
}
