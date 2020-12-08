<?php

namespace Sysvale\ValidationRules\Tests\Unit\Rules;

use Mockery;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Validator;
use Sysvale\ValidationRules\Tests\TestCase;
use Sysvale\ValidationRules\Rules\ZipHasValidCnesXML;
use Sysvale\ValidationRules\Support\ZipWithXMLHandler;

class ZipHasValidCnesXMLTest extends TestCase
{

	private function getFile()
	{
		return new class {
			public function path()
			{
				return '';
			}
		};
	}

	public function testInvalidFileDontPasses()
	{
		$this->mockXmlContents(null, '<root><ImportarXMLCNES></ImportarXMLCNES></root>');

		$file = $this->getFile();
		$rule = new ZipHasValidCnesXML('');
		$passes = $rule->passes('dummy', $file);

		$this->assertFalse($passes);
	}

	public function testValidFilePasses()
	{
		$this->mockXmlContents('foobar');

		$rule = new ZipHasValidCnesXML('foobar');
		$file = $file = $this->getFile();

		$passes = $rule->passes('dummy', $file);

		$this->assertTrue($passes);
	}

	public function testValidFileWithIncorretIbgeCodeDontPasses()
	{
		$this->mockXmlContents('bar');

		$file = $file = $this->getFile();
		$rule = new ZipHasValidCnesXML('foo');
		$passes = $rule->passes('dummy', $file);

		$this->assertFalse($passes);
	}

	private function mockXmlContents($code, $contents = null, $date = '2020-10-17')
	{
		if (is_null($contents)) {
			$contents = "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?>
			<ImportarXMLCNES>
				<IDENTIFICACAO DATA=\"$date\" ORIGEM=\"PORTAL\" DESTINO=\"ESUS_AB\" CO_IBGE_MUN=\"$code\">\r\n
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

	public function testReturnCorrectlyMessage()
	{
		Config::set('app.locale', 'pt_BR');

		$mock_rule = Mockery::mock(ZipHasValidCnesXML::class)->makePartial();
		$mock_rule->shouldReceive('passes')->andReturn(false);

		$validator = Validator::make(['file' => 'foobar'], ['file' => $mock_rule]);

		$this->assertSame(
			'O XML apresentou inconsistências. Pedimos que o envie novamente.',
			$validator->errors()->first('file')
		);
	}

	public function testValidFileWithIncorretDatePasses()
	{
		$this->mockXmlContents('bar');

		$file = $this->getFile();
		$rule = new ZipHasValidCnesXML('bar', '2020-10-18');
		$passes = $rule->passes('dummy', $file);

		$this->assertFalse($passes);
	}

	public function testValidFileWithCorretDatePasses()
	{
		$this->mockXmlContents('bar', null, '2020-10-19');

		$file = $this->getFile();
		$rule = new ZipHasValidCnesXML('bar', '2020-10-18');
		$passes = $rule->passes('dummy', $file);

		$this->assertTrue($passes);
	}
}
