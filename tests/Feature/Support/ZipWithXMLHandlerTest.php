<?php

namespace Sysvale\ValidationRules\Tests\Feature\Support;

use Mockery;
use ZipArchive;
use SimpleXMLElement;
use Sysvale\ValidationRules\Tests\TestCase;
use Sysvale\ValidationRules\Support\ZipWithXMLHandler;

class ZipWithXMLHandlerTest extends TestCase
{
	public function testExpectedExceptionWhenFilenameIsInvalid()
	{
		$zip = new ZipWithXMLHandler;

		$this->expectExceptionMessage('Could not open file');
		$zip->buildZip('foo');
	}

	public function testGetZipReturnException()
	{
		$zip = new ZipWithXMLHandler;

		$this->expectExceptionMessage('ZipArchive should built with buildZip method');
		$zip->getZip();
	}

	public function testGetSimpleXMLElementReturnCorrectInstance()
	{
		$zip = Mockery::mock(ZipWithXMLHandler::class)->makePartial();
		$zip->shouldReceive('getXmlContent')
			->withAnyArgs()
			->andReturn("<root-node><node><child-a>OneWord</child-a></node></root-node>");

		$return = $zip->getSimpleXMLElement(0);

		$this->assertInstanceOf(SimpleXMLElement::class, $return);
	}

	public function testGetXmlContentCorrectly()
	{
		$zip_filename = __DIR__ . '/dummy.zip';

		$zip = new ZipArchive;
		if ($zip->open($zip_filename, ZipArchive::CREATE) === true) {
			$contents = '<root><child>child name</child></root>';
			$zip->addFromString('xml.xml', $contents);
			$zip->close();
		} else {
			throw new \Exception("Doesnt could open the file $zip_filename");
		}

		$zip_handler = new ZipWithXMLHandler;
		$zip_handler->buildZip($zip_filename);

		try {
			$result_contents = $zip_handler->getXmlContent();
		} catch (\Throwable $th) {
			unlink($zip_filename);
			throw $th;
		}

		unlink($zip_filename);

		$this->assertSame($contents, $result_contents);
	}
}
