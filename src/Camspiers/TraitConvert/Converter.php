<?php

namespace Camspiers\TraitConvert;

class Converter 
{

	protected $classes;
	protected $outputDirectory;

	public function __construct($classes, $outputDirectory = null)
	{
		if (is_array($classes)) {
			$this->classes = $classes;
		} else {
			throw new \InvalidArgumentException('$classes must be an array of classes');			
		}
		if (!is_null($outputDirectory)) {
			if (file_exists($outputDirectory) && is_writable($outputDirectory)) {
				$this->outputDirectory = realpath($outputDirectory);
			} else {
				throw new \InvalidArgumentException();
			}
		}
	}

	public function convert()
	{

		foreach ($this->classes as $class) {
			
			$obj = new \ReflectionClass($class);
			$traits = $obj->getTraits();
			$content = '';

			if (is_array($traits)) {
				foreach ($traits as $trait) {
					$content .= implode(
						PHP_EOL,
						array_slice(
							file($trait->getFileName(), FILE_IGNORE_NEW_LINES),
							$trait->getStartLine() + 1,
							$trait->getEndLine() - $trait->getStartLine() - 2
						)
					);
				}
			}

			$file = file($obj->getFileName(), FILE_IGNORE_NEW_LINES);

			$useStatements = array();

			foreach ($file as $key => $value) {
				if (preg_match("/(\s)*use /", $value) === 1) {
					$file[$key] = '';
				}
			}

			array_splice($file, $obj->getEndLine() - 1, 0, $content);

			if ($this->outputDirectory) {
				$filename = $this->outputDirectory . '/' . basename($obj->getFileName());
			} else {
				$filename = $obj->getFileName();
			}

			file_put_contents($filename, implode(PHP_EOL, $file));

		}

	}

}