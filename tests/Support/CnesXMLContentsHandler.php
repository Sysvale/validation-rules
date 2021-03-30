<?php

namespace Sysvale\ValidationRules\Tests\Support;

use Mockery;
use Sysvale\ValidationRules\Support\ZipWithXMLHandler;

trait CnesXMLContentsHandler
{
	/**
	 * @phpcs:disable Generic.Files.LineLength.TooLong
	 */
	private function mockXmlContents($data = [], $contents = null)
	{
		if (is_null($contents)) {
			$ibge_code = $data['ibge_code'] ?? '';
			$date = $data['date'] ?? '';
			$version_xsd = $data['version_xsd'] ?? 'VERSION_XSD="2.1"';

			$contents = "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?>
				<ImportarXMLCNES>
					<IDENTIFICACAO DATA=\"$date\" ORIGEM=\"PORTAL\" DESTINO=\"ESUS_AB\" CO_IBGE_MUN=\"$ibge_code\" $version_xsd>\r\n
						<ESTABELECIMENTOS>\r\n
						</ESTABELECIMENTOS>\r\n
						<PROFISSIONAIS>\r\n
						</PROFISSIONAIS>\r\n
					</IDENTIFICACAO>\r\n
				</ImportarXMLCNES>\r\n";
		}

		$handler_mock = Mockery::mock(ZipWithXMLHandler::class)->makePartial();
		$handler_mock->shouldReceive('closeZip')->andReturn(true);
		$handler_mock->shouldReceive('buildZip')->andReturnSelf();
		$handler_mock->shouldReceive('getXmlContent')
			->andReturn($contents);

		app()->instance(ZipWithXMLHandler::class, $handler_mock);
	}
}
