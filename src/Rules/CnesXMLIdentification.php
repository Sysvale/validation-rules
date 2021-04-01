<?php

namespace Sysvale\ValidationRules\Rules;

use SimpleXMLElement;
use Illuminate\Contracts\Validation\Rule;
use Sysvale\ValidationRules\Rules\CnesXMLRule;
use Sysvale\ValidationRules\Support\ZipWithXMLHandler;

class CnesXMLIdentification extends CnesXMLRule implements Rule
{
	private $expected_ibge_code;

	public function __construct($expected_ibge_code, $version_xsd = null)
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
		$identification = $this->getIdentificationAttributes($xml);

		$origin = (string) $identification['ORIGEM'] ?? '';
		$target = (string) $identification['DESTINO'] ?? '';
		$ibge_code = (string) $identification['CO_IBGE_MUN'] ?? '';

		return $origin === 'PORTAL'
			&& $target === 'ESUS_AB'
			&& $ibge_code === $this->expected_ibge_code;
	}
}
