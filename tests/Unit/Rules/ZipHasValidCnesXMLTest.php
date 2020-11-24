<?php

namespace Tests\Feature\App\Rules;

use Mockery;
use Sysvale\ZipWithXMLValidations\Tests\TestCase;
use Sysvale\ZipWithXMLValidations\Rules\ZipHasValidCnesXML;
use Sysvale\ZipWithXMLValidations\Support\ZipWithXMLHandler;

class ZipHasValidCnesXMLTest extends TestCase
{
	public function testInvalidFileDontPasses()
	{
		$this->mockXmlContents(null, '<root><ImportarXMLCNES></ImportarXMLCNES></root>');

		$rule = new ZipHasValidCnesXML('');

		$file = new class {
			public function path()
			{
				return '';
			}
		};

		$passes = $rule->passes('dummy', $file);

		$this->assertFalse($passes);
	}

	public function testValidFilePasses()
	{
		$this->mockXmlContents('foobar');

		$rule = new ZipHasValidCnesXML('foobar');
		$file = new class {
			public function path()
			{
				return '';
			}
		};
		$passes = $rule->passes('dummy', $file);

		$this->assertTrue($passes);
	}

	public function testValidFileWithIncorretIbgeCodeDontPasses()
	{
		$this->mockXmlContents('bar');

		$file = new class {
			public function path()
			{
				return '';
			}
		};

		$rule = new ZipHasValidCnesXML('foo');
		$passes = $rule->passes('dummy', $file);

		$this->assertFalse($passes);
	}

	private function mockXmlContents($code, $contents = null)
	{
		if (is_null($contents)) {
			$contents = "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?>
			<ImportarXMLCNES>
				<IDENTIFICACAO ORIGEM=\"PORTAL\" DESTINO=\"ESUS_AB\" CO_IBGE_MUN=\"$code\">\r\n
					<ESTABELECIMENTOS>\r\n
					</ESTABELECIMENTOS>\r\n
					<PROFISSIONAIS>\r\n
					</PROFISSIONAIS>\r\n
				</IDENTIFICACAO>\r\n
			</ImportarXMLCNES>\r\n";
		}

		$handler_mock = Mockery::mock(ZipWithXMLHandler::class)->makePartial();
		$handler_mock->shouldReceive('closeZip')->andReturn(true);
		$handler_mock->shouldReceive('buildZip')->andReturn(Mockery::self());
		$handler_mock->shouldReceive('getXmlContent')
			->andReturn($contents);

		app()->instance(ZipWithXMLHandler::class, $handler_mock);
	}
}
