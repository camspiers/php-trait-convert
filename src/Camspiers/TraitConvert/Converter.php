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

		if (is_writable($ouputDirectory)) {
			$this->outputDirectory = $outputDirectory;
		} else {
			throw new InvalidArgumentException('$outputDirectory must be a writable directory');		
		}
	}

	public function convert()
	{

		foreach ($this->classes as $class) {
			
			$obj = new ReflectionClass($class);
			$traits = $obj->getTraits();
			$content = '';

			if (is_array($traits)) {
				foreach ($traits as $trait) {
					$trait = new ReflectionClass($trait);
					$content .= implode('', array_slice(file($trait->getFileName()), $trait->getStartLine(), $trait->getEndLine()));
				}

			}

			$file = file($obj->getFileName());

			array_splice($file, $obj->getEndLine(), 0, $content);

			file_put_contents($obj->getFileName(), implode(PHP_EOL, $file));

		}

	}

}