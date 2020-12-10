<?php

namespace Sysvale\ValidationRules\Rules;

use SimpleXMLElement;
use Illuminate\Contracts\Validation\Rule;
use Sysvale\ValidationRules\Support\ZipWithXMLHandler;

class ZipHasValidCnesXML implements Rule
{
	private $expected_ibge_code;
	private $last_date_xml;

	public function __construct($expected_ibge_code, $last_date_xml = null)
	{
		$this->expected_ibge_code = $expected_ibge_code;
		$this->last_date_xml = $last_date_xml;
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
		$zip_handler = resolve(ZipWithXMLHandler::class)->buildZip($value->path());

		$xml = $zip_handler->getSimpleXMLElement();

		$passes = $this->hasValidIdentification($xml)
			&& $this->hasEstablishmentAndProfessionals($xml);

		if (isset($this->last_date_xml)) {
			$passes = $passes && $this->hasValidDate($xml);
		}

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
		return __('SysvaleValidationRules::messages.zip_has_valid_cnes_xml');
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

	protected function hasEstablishmentAndProfessionals(SimpleXMLElement $xml)
	{
		foreach ($xml->children() as $identification) {
			$children  = array_keys((array) $identification->children());

			$passes = array_reduce($children, function ($carry, $item) {
				return $carry && ($item === 'PROFISSIONAIS' || $item === 'ESTABELECIMENTOS');
			}, true);
		}

		return $passes;
	}

	protected function hasDateValid(SimpleXMLElement $xml)
	{
		$identification = $xml->{'IDENTIFICACAO'};
		$date = array_values((array) $identification->attributes())[0]['DATA'];

		if ($date > $this->last_date_xml) {
			return true;
		}
		return false;
	}
}
