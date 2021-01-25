<?php

namespace Sysvale\ValidationRules\Tests\Support;

use Mockery;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Validator;

trait RuleErrorMessageHandler
{
	public function getErrorMessage($rule_class)
	{
		Config::set('app.locale', 'pt_BR');

		$mock_rule = Mockery::mock($rule_class)->makePartial();
		$mock_rule->shouldReceive('passes')->andReturn(false);

		$validator = Validator::make(['file' => 'foobar'], ['file' => $mock_rule]);

		return $validator->errors()->first('file');
	}
}
