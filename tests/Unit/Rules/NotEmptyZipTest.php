<?php

namespace Sysvale\ValidationRules\Tests\Unit\Rules;

use Mockery;
use ZipArchive;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Validator;
use Sysvale\ValidationRules\Tests\TestCase;
use Sysvale\ValidationRules\Rules\NotEmptyZip;
use Sysvale\ValidationRules\Support\ZipWithXMLHandler;

class NotEmptyZipTest extends TestCase
{
	/**
	 * @dataProvider comparableValuesProvider
	 */
	public function testNotEmpty($zip_files_count, $passes)
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

		$rule = new NotEmptyZip();

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
			'Should passes when zip files count isnt 0' => [
				1, true
			],
			'It do not should passes when zip files count is 0' => [
				0, false
			],
		];
	}

	public function testReturnCorrectlyMessage()
	{
		Config::set('app.locale', 'pt_BR');

		$mock_rule = Mockery::mock(NotEmptyZip::class)->makePartial();
		$mock_rule->shouldReceive('passes')->andReturn(false);

		$validator = Validator::make(['file' => 'foobar'], ['file' => $mock_rule]);

		$this->assertSame(
			'O arquivo zip não pode está vazio.',
			$validator->errors()->first('file')
		);
	}
}
