<?php

namespace Sysvale\ValidationRules\Rules;

use SimpleXMLElement;
use Illuminate\Contracts\Validation\Rule;
use Sysvale\ValidationRules\Rules\CnesXMLRule;
use Sysvale\ValidationRules\Support\ZipWithXMLHandler;

class CnesXMLDate extends CnesXMLRule implements Rule
{
	private $date;

	public function __construct($date)
	{
		$this->date = $date;
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

		$passes = $this->hasValidDate($zip_handler->getSimpleXMLElement());

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
		return __('SysvaleValidationRules::messages.cnes_xml_date', [
			'date' => $this->date,
		]);
	}

	protected function hasValidDate(SimpleXMLElement $xml)
	{
		$identification = $this->getIdentificationAttributes($xml);
		$current_date_xml = $identification['DATA'] ?? null;

		return $current_date_xml > $this->date;
	}
}
