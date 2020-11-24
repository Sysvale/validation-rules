<?php

namespace Sysvale\ZipWithXMLValidations\Rules;

use Illuminate\Contracts\Validation\Rule;
use Sysvale\ZipWithXMLValidations\Support\ZipWithXMLHandler;

class NotEmptyZip implements Rule
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
		$zip_handler = resolve(ZipWithXMLHandler::class)->buildZip($value->path());

		$passes = $zip_handler->getZip()->count() !== 0;
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
		return 'O arquivo zip não pode ser vazio.';
	}
}
