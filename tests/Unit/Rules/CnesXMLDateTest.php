<?php

namespace Sysvale\ValidationRules\Tests\Unit\Rules;

use Illuminate\Http\UploadedFile;
use Sysvale\ValidationRules\Tests\TestCase;
use Sysvale\ValidationRules\Rules\CnesXMLDate;
use Sysvale\ValidationRules\Tests\Support\CnesXMLContentsHandler;
use Sysvale\ValidationRules\Tests\Support\RuleErrorMessageHandler;

class CnesXMLDateTest extends TestCase
{
	use CnesXMLContentsHandler;
	use RuleErrorMessageHandler;

	public function testHasValidDate()
	{
		$this->mockXmlContents(['date' => '2020-10-10']);

		$rule = new CnesXMLDate('2020-10-09');

		$this->assertTrue($rule->passes('file', UploadedFile::fake()->create('xml.zip')));
	}

	public function testHasInvalidDate()
	{
		$this->mockXmlContents(['date' => '2020-10-09']);

		$rule = new CnesXMLDate('2020-10-09');

		$this->assertFalse($rule->passes('file', UploadedFile::fake()->create('xml.zip')));
	}

	public function testHasValidDateErrorMessage()
	{
		$this->assertEquals(
			'A data do XML deve ser posterior a data 2020-10-09.',
			$this->getErrorMessage(CnesXMLDate::class, ['2020-10-09'])
		);
	}
}
