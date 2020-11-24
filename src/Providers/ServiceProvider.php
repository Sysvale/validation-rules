<?php

namespace Sysvale\ZipWithXMLValidations\Providers;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
	public function boot()
	{
		if ($this->app->runningInConsole()) {
			$this->registerPublishing();
		}

		$this->loadTranslationsFrom(__DIR__.'/../../resources/lang/', 'ZipWithXMLValidations');
	}

	protected function registerPublishing()
	{
		$this->publishes([
			__DIR__.'/../../resources/lang' => resource_path('lang/vendor/ZipWithXMLValidations'),
		], 'zip-with-xml-validations-messages');
	}
}
