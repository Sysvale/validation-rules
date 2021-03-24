<?php

namespace Sysvale\ValidationRules\Tests\Unit\Rules;

use Illuminate\Http\UploadedFile;
use Sysvale\ValidationRules\Tests\TestCase;
use Sysvale\ValidationRules\Tests\Support\CnesXMLContentsHandler;
use Sysvale\ValidationRules\Rules\CnesXMLIdentification;
use Sysvale\ValidationRules\Tests\Support\RuleErrorMessageHandler;

class CnesXMLIdentificationTest extends TestCase
{
	use RuleErrorMessageHandler;
	use CnesXMLContentsHandler;

	public function testPasses()
	{
		$ibge_code = '12345';
		$version_xsd = '2.1';

		$this->mockXmlContents(['ibge_code' => $ibge_code, 'version_xsd' => "VERSION_XSD=\"$version_xsd\""]);

		$rule = new CnesXMLIdentification($ibge_code, $version_xsd);

		$this->assertTrue($rule->passes('file', UploadedFile::fake()->create('xml.zip')));
	}

	public function testNotPassesVersionXSD()
	{
		$ibge_code = '12345';

		$this->mockXmlContents(['ibge_code' => $ibge_code]);

		$rule = new CnesXMLIdentification($ibge_code, '2.1');

		$this->assertFalse($rule->passes('file', UploadedFile::fake()->create('xml.zip')));
	}

	public function testNotPassesVersionXSDDiff2_1()
	{
		$ibge_code = '12345';
		$version_xsd = '3.0';

		$this->mockXmlContents(['ibge_code' => $ibge_code, 'version_xsd' => "VERSION_XSD=\"$version_xsd\""]);

		$rule = new CnesXMLIdentification($ibge_code, '2.1');

		$this->assertFalse($rule->passes('file', UploadedFile::fake()->create('xml.zip')));
	}

	/**
	 * @phpcs:disable Generic.Files.LineLength.TooLong
	 */
	public function testIdentificationErrorMessage()
	{
		$this->assertSame(
			'XML com formato inválido. Por favor, verifique o código do IBGE, o campo de ORIGEM, o campo de DESTINO e a se a versão do XML compatível é a 2.1',
			$this->getErrorMessage(CnesXMLIdentification::class, ['0000', '2.1'])
		);
	}
}
