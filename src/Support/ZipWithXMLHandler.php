<?php

namespace Sysvale\ZipWithXMLValidations\Support;

use ZipArchive;
use SimpleXMLElement;

class ZipWithXMLHandler
{
	private $zip;

	public function buildZip(string $filename): ZipWithXMLHandler
	{
		$this->zip = new ZipArchive;
		$opened = $this->zip->open($filename);

		if ($opened !== true) {
			throw new \RuntimeException('Could not open file');
		}

		return $this;
	}

	public function closeZip(): bool
	{
		return $this->zip->close();
	}

	public function getXmlContent(int $index_file = 0): string
	{
		$name = $this->zip->getNameIndex($index_file);
		$stream = $this->zip->getStream($name);
		$content  = stream_get_contents($stream);

		return $content;
	}

	public function getSimpleXMLElement(int $index_file = 0): SimpleXMLElement
	{
		$content = $this->getXmlContent($index_file);
		$xml = new SimpleXMLElement($content);

		return $xml;
	}

	public function getZip(): ZipArchive
	{
		if (! $this->zip instanceof ZipArchive) {
			throw new \RuntimeException('ZipArchive should built with buildZip method');
		}

		return $this->zip;
	}
}
