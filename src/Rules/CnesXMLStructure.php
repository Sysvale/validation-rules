<?php

namespace Sysvale\ValidationRules\Rules;

use SimpleXMLElement;
use Illuminate\Contracts\Validation\Rule;
use Sysvale\ValidationRules\Support\ZipWithXMLHandler;

class CnesXMLStructure implements Rule
{
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

		$passes = $this->hasValidSctructure($zip_handler->getSimpleXMLElement());

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
		return __('SysvaleValidationRules::messages.cnes_xml_structure');
	}

	protected function hasValidSctructure(SimpleXMLElement $xml)
	{
		return $xml->getName() === 'ImportarXMLCNES'
			&& $xml->{'IDENTIFICACAO'} !== null
			&& $xml->{'IDENTIFICACAO'}->{'ESTABELECIMENTOS'} !== null
			&& $xml->{'IDENTIFICACAO'}->{'PROFISSIONAIS'} !== null;
	}
}
