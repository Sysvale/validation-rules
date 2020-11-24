<?php

namespace Sysvale\ValidationRules\Tests\Unit\Rules;

use Mockery;
use ZipArchive;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Validator;
use Sysvale\ValidationRules\Tests\TestCase;
use Sysvale\ValidationRules\Rules\MaxInTheZipFile;
use Sysvale\ValidationRules\Support\ZipWithXMLHandler;

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

	public function testReturnCorrectlyMessage()
	{
		Config::set('app.locale', 'pt_BR');

		$mock_rule = Mockery::mock(MaxInTheZipFile::class . '[passes]', [2]);
		$mock_rule->shouldReceive('passes')->andReturn(false);

		$validator = Validator::make(['file' => 'foobar'], ['file' => $mock_rule]);

		$this->assertSame(
			'O arquivo zip deve ter no máximo 2 arquivo(s).',
			$validator->errors()->first('file')
		);
	}
}
