<?php

namespace Sysvale\ZipWithXMLValidations\Rules;

use SimpleXMLElement;
use Illuminate\Contracts\Validation\Rule;
use Sysvale\ZipWithXMLValidations\Support\ZipWithXMLHandler;

class ZipHasValidCnesXML implements Rule
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
		$zip_handler = resolve(ZipWithXMLHandler::class)->buildZip($value->path());

		$xml = $zip_handler->getSimpleXMLElement();

		$passes = $this->hasValidIdentification($xml)
			&& $this->hasEstablishmentAndProfessionals($xml);

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
		return 'O XML apresentou inconsistências. Pedimos que o envie novamente.';
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
}
