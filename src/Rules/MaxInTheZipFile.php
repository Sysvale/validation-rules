<?php

namespace Sysvale\ZipWithXMLValidations\Rules;

use Illuminate\Contracts\Validation\Rule;
use Sysvale\ZipWithXMLValidations\Support\ZipWithXMLHandler;

class MaxInTheZipFile implements Rule
{
	private $quantity;

	/**
	 * Create a new rule instance.
	 *
	 * @return void
	 */
	public function __construct($quantity)
	{
		$this->quantity = $quantity;
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

		$passes = $zip_handler->getZip()->count() <= $this->quantity;

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
		return __('ZipWithXMLValidations::messages.max_in_the_zip_file', [
			'quantity' => $this->quantity,
		]);
	}
}
