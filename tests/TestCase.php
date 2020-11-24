<?php

namespace Sysvale\ValidationRules\Tests;

use Sysvale\ValidationRules\Providers\ServiceProvider;

class TestCase extends \Orchestra\Testbench\TestCase
{
	protected function getPackageProviders($app)
	{
		return [
			ServiceProvider::class,
		];
	}
}
