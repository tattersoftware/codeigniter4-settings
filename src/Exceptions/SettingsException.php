<?php namespace Tatter\Settings\Exceptions;

use CodeIgniter\Exceptions\ExceptionInterface;
use CodeIgniter\Exceptions\FrameworkException;

class SettingsException extends FrameworkException implements ExceptionInterface
{
	public static function forMissingName()
	{
		return new static("No name provided for setting lookup");
	}
	
	public static function forUnmatchedName(string $name)
	{
		return new static("No setting template named '{$name}'");
	}
	
	public static function forProtectionViolation(string $name)
	{
		return new static("Modify attempt on protected setting '{$name}'");
	}
}
