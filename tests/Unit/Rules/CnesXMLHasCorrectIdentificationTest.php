<?php

namespace Sysvale\ValidationRules\Tests\Unit\Rules;

use Mockery;
use Illuminate\Http\UploadedFile;
use Sysvale\ValidationRules\Tests\TestCase;
use Sysvale\ValidationRules\Support\ZipWithXMLHandler;
use Sysvale\ValidationRules\Tests\Support\CnesXMLContentsHandler;
use Sysvale\ValidationRules\Rules\CnesXMLHasCorrectIdentification;
use Sysvale\ValidationRules\Tests\Support\RuleErrorMessageHandler;

class CnesXMLHasCorrectIdentificationTest extends TestCase
{
	use RuleErrorMessageHandler;
	use CnesXMLContentsHandler;

	public function testPasses()
	{
		$ibge_code = '12345';
		$this->mockXmlContents(['ibge_code' => $ibge_code]);

		$rule = new CnesXMLHasCorrectIdentification($ibge_code);

		$this->assertTrue($rule->passes('file', UploadedFile::fake()->create('xml.zip')));
	}

	/**
	 * @phpcs:disable Generic.Files.LineLength.TooLong
	 */
	public function testIdentificationErrorMessage()
	{
		$this->assertSame(
			'XML com formato inválido. Por favor, verifique o código do IBGE, o campo de ORIGEM e o campo de DESTINO.',
			$this->getErrorMessage(CnesXMLHasCorrectIdentification::class, ['0000'])
		);
	}
}
