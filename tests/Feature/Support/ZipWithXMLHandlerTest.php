<?php

namespace Sysvale\ZipWithXMLValidations\Tests\Feature\Support;

use Mockery;
use ZipArchive;
use SimpleXMLElement;
use Sysvale\ZipWithXMLValidations\Tests\TestCase;
use Sysvale\ZipWithXMLValidations\Support\ZipWithXMLHandler;

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
		$content = '<root><child>child name</child></root>';

		$zip_filename = __DIR__.'dummy.zip';

		$zip = new ZipArchive;
		if ($zip->open($zip_filename, ZipArchive::CREATE) === true) {
			$zip->addFromString('xml.xml', $content);
			$zip->close();
		}

		$zip_handler = new ZipWithXMLHandler;
		$zip_handler->buildZip($zip_filename);

		$this->assertSame($content, $zip_handler->getXmlContent());
		unlink($zip_filename);
	}
}
