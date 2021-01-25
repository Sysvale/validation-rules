<?php

namespace Sysvale\ValidationRules\Tests\Unit\Rules;

use Illuminate\Http\UploadedFile;
use Sysvale\ValidationRules\Tests\TestCase;
use Sysvale\ValidationRules\Rules\CnesXMLStructure;
use Sysvale\ValidationRules\Tests\Support\CnesXMLContentsHandler;
use Sysvale\ValidationRules\Tests\Support\RuleErrorMessageHandler;

class CnesXMLStructureTest extends TestCase
{
	use CnesXMLContentsHandler;
	use RuleErrorMessageHandler;

	public function testHasValidStructure()
	{
		$this->mockXmlContents();

		$rule = new CnesXMLStructure;

		$this->assertTrue($rule->passes('file', UploadedFile::fake()->create('xml.zip')));
	}

	public function testHasInvalidStructure()
	{
		$this->mockXmlContents(null, '<root></root>');

		$rule = new CnesXMLStructure;

		$this->assertFalse($rule->passes('file', UploadedFile::fake()->create('xml.zip')));
	}

	public function testStructureRuleReturnErrorMessage()
	{
		$error_message = $this->getErrorMessage(CnesXMLStructure::class);

		$this->assertSame(
			$error_message,
			'A estrutura do arquivo XML está inválida.'
		);
	}
}
