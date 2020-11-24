<?php

namespace Sysvale\ZipWithXMLValidations\Tests\Unit\Rules;

use Mockery;
use ZipArchive;
use Sysvale\ZipWithXMLValidations\Tests\TestCase;
use Sysvale\ZipWithXMLValidations\Rules\MaxInTheZipFile;
use Sysvale\ZipWithXMLValidations\Support\ZipWithXMLHandler;

class MaxInTheZipFileTest extends TestCase
{
	/**
	 * @dataProvider comparableValuesProvider
	 */
	public function testCompareZipFilesCountAndMaxQuantity($zip_files_count, $max_quantity, $passes)
	{
		$zip_mock = Mockery::mock(ZipArchive::class);
		$zip_mock->shouldReceive('count')
			->andReturn($zip_files_count);
		$handler_mock = Mockery::mock(ZipWithXMLHandler::class);
		$handler_mock->shouldReceive('closeZip')->andReturn(true);
		$handler_mock->shouldReceive('buildZip')->andReturn(Mockery::self());
		$handler_mock->shouldReceive('getZip')
			->andReturn($zip_mock);

		app()->instance(ZipWithXMLHandler::class, $handler_mock);

		$rule = new MaxInTheZipFile($max_quantity);

		$file_instance = new class {
			public function path()
			{
				return '';
			}
		};

		$this->assertSame($passes, $rule->passes('dummy_attribute', $file_instance));
	}

	public function comparableValuesProvider()
	{
		return [
			'it do not passes when zip files count is bigger' => [
				2, 1, false
			],
			'it passes when zip files count is smaller' => [
				1, 2, true
			],
			'it passes when zip files count is equal' => [
				1, 1, true
			],
		];
	}
}
