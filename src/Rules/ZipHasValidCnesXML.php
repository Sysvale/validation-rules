<?php

namespace Sysvale\ValidationRules\Rules;

use SimpleXMLElement;
use Illuminate\Contracts\Validation\Rule;
use Sysvale\ValidationRules\Support\ZipWithXMLHandler;

class ZipHasValidCnesXML implements Rule
{
	private $expected_ibge_code;
	private $date;
	protected $message;

	public function __construct($expected_ibge_code, $date = null, $version_xsd = null)
	{
		$this->expected_ibge_code = $expected_ibge_code;
		$this->date = $date;
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
		$passes = $this->hasValidStructure($attribute, $value)
			&& $this->hasValidIdentification($attribute, $value);

		if (isset($this->date)) {
			$passes = $passes && $this->hasValidDate($attribute, $value);
		}

		return $passes;
	}

	/**
	 * Get the validation error message.
	 *
	 * @return string
	 */
	public function message()
	{
		return $this->message;
	}

	protected function hasValidStructure($attribute, $value)
	{
		$rule = new CnesXMLStructure();

		return $this->validateWithRule($rule, $attribute, $value);
	}

	protected function hasValidIdentification($attribute, $value)
	{
		$rule = new CnesXMLIdentification($this->expected_ibge_code, $this->version_xsd);

		return $this->validateWithRule($rule, $attribute, $value);
	}

	protected function hasValidDate($attribute, $value)
	{
		$rule = new CnesXMLDate($this->date);

		return $this->validateWithRule($rule, $attribute, $value);
	}

	private function validateWithRule($rule, $attribute, $value)
	{
		$passes = $rule->passes($attribute, $value);

		if (!$passes) {
			$this->message = $rule->message();
		}

		return $passes;
	}
}
