<?php

namespace Sysvale\ValidationRules\Rules;

use Illuminate\Support\Str;
use Illuminate\Contracts\Validation\Rule;
use Sysvale\ValidationRules\Support\ZipWithXMLHandler;

class ZipHasXmlFile implements Rule
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

		$name = $zip_handler->getZip()->getNameIndex(0);
		$zip_handler->closeZip();

		return Str::contains(strtolower($name), '.xml');
	}

	/**
	 * Get the validation error message.
	 *
	 * @return string
	 */
	public function message()
	{
		return __('SysvaleValidationRules::messages.zip_has_xml_file');
	}
}
