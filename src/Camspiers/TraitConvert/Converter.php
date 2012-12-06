<?php

namespace Camspiers\TraitConvert;

class Converter 
{

	protected $classes;
	protected $outputDirectory;

	public function __construct($classes)
	{
		if (is_array($classes)) {
			$this->classes = $classes;
		} else {
			throw new InvalidArgumentException('$classes must be an array of classes');			
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

			array_splice($file, $obj->getEndLine() - 1, 0, $content);

			// foreach ($file as $key => $value) {
			// 	if (is_array($traits)) {
			// 		foreach ($traits as $trait) {
			// 		}
			// 	}
			// }

			file_put_contents($obj->getFileName(), implode(PHP_EOL, $file));

		}

	}

}