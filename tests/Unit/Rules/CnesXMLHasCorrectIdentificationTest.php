<?php

namespace Sysvale\ValidationRules\Tests\Unit\Rules;

use Mockery;
use Illuminate\Http\UploadedFile;
use Sysvale\ValidationRules\Tests\TestCase;
use Sysvale\ValidationRules\Support\ZipWithXMLHandler;
use Sysvale\ValidationRules\Rules\CnesXMLHasCorrectIdentification;
use Sysvale\ValidationRules\Tests\Support\RuleErrorMessageHandler;

class CnesXMLHasCorrectIdentificationTest extends TestCase
{
	use RuleErrorMessageHandler;

	public function testPasses()
	{
		$ibge_code = '12345';
		$this->mockXmlContents($ibge_code);

		$rule = new CnesXMLHasCorrectIdentification($ibge_code);

		$this->assertTrue($rule->passes('file', UploadedFile::fake()->create('xml.zip')));
	}

	/**
	 * @phpcs:disable Generic.Files.LineLength.TooLong
	 */
	private function mockXmlContents($ibge_code)
	{
		$contents = "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?>
			<ImportarXMLCNES>
				<IDENTIFICACAO DATA=\"2020-10-10\" ORIGEM=\"PORTAL\" DESTINO=\"ESUS_AB\" CO_IBGE_MUN=\"$ibge_code\">\r\n
					<ESTABELECIMENTOS>\r\n
					</ESTABELECIMENTOS>\r\n
					<PROFISSIONAIS>\r\n
					</PROFISSIONAIS>\r\n
				</IDENTIFICACAO>\r\n
			</ImportarXMLCNES>\r\n";


		$handler_mock = Mockery::mock(ZipWithXMLHandler::class)->makePartial();
		$handler_mock->shouldReceive('closeZip')->andReturn(true);
		$handler_mock->shouldReceive('buildZip')->andReturnSelf();
		$handler_mock->shouldReceive('getXmlContent')
			->andReturn($contents);

		app()->instance(ZipWithXMLHandler::class, $handler_mock);
	}

	public function testIdentificationErrorMessage()
	{
		$this->assertSame(
			'XML com formato inválido. Por favor, verifique o código do IBGE, o campo de ORIGEM e o campo de DESTINO.',
			$this->getErrorMessage(CnesXMLHasCorrectIdentification::class)
		);
	}
}
