<?php

namespace Sysvale\ZipWithXMLValidations\Tests;

use Sysvale\ZipWithXMLValidations\Providers\ServiceProvider;

class TestCase extends \Orchestra\Testbench\TestCase
{
	protected function getPackageProviders($app)
	{
		return [
			ServiceProvider::class,
		];
	}
}
