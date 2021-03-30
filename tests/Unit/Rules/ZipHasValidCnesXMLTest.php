<?php

namespace Sysvale\ValidationRules\Tests\Unit\Rules;

use Mockery;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Validator;
use Sysvale\ValidationRules\Tests\TestCase;
use Sysvale\ValidationRules\Rules\ZipHasValidCnesXML;
use Sysvale\ValidationRules\Support\ZipWithXMLHandler;
use Sysvale\ValidationRules\Tests\Support\CnesXMLContentsHandler;

class ZipHasValidCnesXMLTest extends TestCase
{
	use CnesXMLContentsHandler;

	public function setUp(): void
	{
		parent::setUp();

		Config::set('app.locale', 'pt_BR');
	}

	private function getFile()
	{
		return new class {
			public function path()
			{
				return '';
			}
		};
	}

	public function testFileWithInvalidStructureDontPasses()
	{
		$this->mockXmlContents(null, '<root><ImportarXMLCNES></ImportarXMLCNES></root>');

		$file = $this->getFile();
		$validator = Validator::make([
			'file' => UploadedFile::fake()->create('xml.zip'),
		], [
			'file' => [new ZipHasValidCnesXML('')],
		]);

		$passes = $validator->passes();

		$this->assertFalse($passes);
		$this->assertEquals(
			'A estrutura do arquivo XML está inválida.',
			$validator->errors()->first('file')
		);
	}

	public function testFileWithInvalidIdentification()
	{
		$this->mockXmlContents(['ibge_code' => '1234']);

		$validator = Validator::make([
			'file' => UploadedFile::fake()->create('xml.zip'),
		], [
			'file' => [new ZipHasValidCnesXML('0000')],
		]);

		$passes = $validator->passes();

		$this->assertFalse($passes);
		$this->assertEquals(
			'XML com formato inválido. Por favor, verifique o código do IBGE, o campo de ORIGEM, o campo de DESTINO e a se a versão do XML compatível é a 2.1', //phpcs:ignore
			$validator->errors()->first('file')
		);
	}

	public function testFileWithInvalidDate()
	{
		$this->mockXmlContents([
			'ibge_code' => '',
			'date' =>'2020-10-10',
			'version_xsd' => 'VERSION_XSD="2.1"'
		]);

		$validator = Validator::make([
			'file' => UploadedFile::fake()->create('xml.zip'),
		], [
			'file' => [new ZipHasValidCnesXML('', '2020-10-10', $version_xsd = '2.1')],
		]);

		$passes = $validator->passes();

		$this->assertFalse($passes);
		$this->assertEquals(
			'A competência do XML deve ser posterior a data 2020-10-10.',
			$validator->errors()->first('file')
		);
	}

	public function testValidFilePasses()
	{
		$this->mockXmlContents(['ibge_code' => '123456', 'date' => '2020-10-10']);

		$rule = new ZipHasValidCnesXML('123456', '2020-10-09', '2.1');

		$passes = $rule->passes('file', UploadedFile::fake()->create('xml.zip'));

		$this->assertTrue($passes);
	}
}
