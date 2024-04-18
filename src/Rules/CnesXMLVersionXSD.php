<?php

namespace Sysvale\ValidationRules\Rules;

use SimpleXMLElement;
use Illuminate\Contracts\Validation\Rule;
use Sysvale\ValidationRules\Rules\CnesXMLRule;
use Sysvale\ValidationRules\Support\ZipWithXMLHandler;

class CnesXMLVersionXSD extends CnesXMLRule implements Rule
{
	private $versions;

	public function __construct($versions = [])
	{
		$this->versions = (array) $versions;
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

		$passes = $this->hasValidVersion($zip_handler->getSimpleXMLElement());

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
		return __('SysvaleValidationRules::messages.cnes_xml_version_xsd', [
			'version_xsd' => implode(' ou ', $this->versions),
		]);
	}

	protected function hasValidVersion(SimpleXMLElement $xml)
	{
		$identification = $this->getIdentificationAttributes($xml);
		$current_version_xsd = $identification['VERSAO_XSD'] ?? null;

		return in_array($current_version_xsd, $this->versions);
	}
}
