<?php

namespace Sysvale\ValidationRules\Tests\Unit\Rules;

use Mockery;
use ZipArchive;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Validator;
use Sysvale\ValidationRules\Tests\TestCase;
use Sysvale\ValidationRules\Rules\ZipHasXmlFile;
use Sysvale\ValidationRules\Support\ZipWithXMLHandler;

class ZipHasXmlFileTest extends TestCase
{

	/**
	 * @dataProvider filenameProvider
	 */
	public function testPassesCorrectly($file_name, $expected)
	{
		$zip_archive_mock = Mockery::mock(ZipArchive::class);
		$zip_archive_mock->shouldReceive('getNameIndex')
			->with(0)
			->andReturn($file_name);

		$handler_mock = Mockery::mock(ZipWithXMLHandler::class);
		$handler_mock->shouldReceive('closeZip')->andReturn(true);
		$handler_mock->shouldReceive('buildZip')->andReturn(Mockery::self());
		$handler_mock->shouldReceive('getZip')
			->andReturn($zip_archive_mock);

		app()->instance(ZipWithXMLHandler::class, $handler_mock);

		$rule = new ZipHasXmlFile;

		$file_instance = new class {
			public function path()
			{
				return '';
			}
		};

		$this->assertSame($expected, $rule->passes('foo', $file_instance));
	}

	public function filenameProvider()
	{
		return [
			'Should passes when file has .xml' => [
				'bar.xml', true
			],

			'Should not passes when file doesnt have .xml' => [
				'bar.txt', false
			],
		];
	}

	public function testReturnCorrectlyMessage()
	{
		Config::set('app.locale', 'pt_BR');

		$mock_rule = Mockery::mock(ZipHasXmlFile::class)->makePartial();
		$mock_rule->shouldReceive('passes')->andReturn(false);

		$validator = Validator::make(['file' => 'foobar'], ['file' => $mock_rule]);

		$this->assertSame(
			'O arquivo zip deve conter um XML.',
			$validator->errors()->first('file')
		);
	}
}
