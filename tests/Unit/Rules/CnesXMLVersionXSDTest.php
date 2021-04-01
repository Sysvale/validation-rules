<?php

namespace Sysvale\ValidationRules\Tests\Unit\Rules;

use Illuminate\Http\UploadedFile;
use Sysvale\ValidationRules\Tests\TestCase;
use Sysvale\ValidationRules\Rules\CnesXMLVersionXSD;
use Sysvale\ValidationRules\Rules\CnesXMLIdentification;
use Sysvale\ValidationRules\Tests\Support\CnesXMLContentsHandler;
use Sysvale\ValidationRules\Tests\Support\RuleErrorMessageHandler;

class CnesXMLVersionXSDTest extends TestCase
{
	use RuleErrorMessageHandler;
	use CnesXMLContentsHandler;

	public function testPasses()
	{
		$version = '2.1';

		$this->mockXmlContents(['version_xsd' => "VERSION_XSD=\"$version\""]);

		$rule = new CnesXMLVersionXSD('2.1');

		$this->assertTrue($rule->passes('file', UploadedFile::fake()->create('xml.zip')));
	}

	public function testNotPasses()
	{
		$version = '2';

		$this->mockXmlContents(['version_xsd' => "VERSION_XSD=\"$version\""]);

		$rule = new CnesXMLVersionXSD('2.1');

		$this->assertFalse($rule->passes('file', UploadedFile::fake()->create('xml.zip')));
	}

	public function testIdentificationErrorMessage()
	{
		$this->assertSame(
			'A versão do XML deve ser compatível com a 2.1',
			$this->getErrorMessage(CnesXMLVersionXSD::class, ['2.1'])
		);
	}
}
