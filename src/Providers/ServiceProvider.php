<?php

namespace Sysvale\ValidationRules\Providers;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
	public function boot()
	{
		if ($this->app->runningInConsole()) {
			$this->registerPublishing();
		}

		$this->loadTranslationsFrom(__DIR__.'/../../resources/lang/', 'SysvaleValidationRules');
	}

	protected function registerPublishing()
	{
		$this->publishes([
			__DIR__.'/../../resources/lang' => resource_path('lang/vendor/SysvaleValidationRules'),
		], 'sysvale-validation-rules-messages');
	}
}
