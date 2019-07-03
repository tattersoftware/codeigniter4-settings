<?php namespace Tatter\Settings\Exceptions;

use CodeIgniter\Exceptions\ExceptionInterface;
use CodeIgniter\Exceptions\FrameworkException;

class SettingsException extends FrameworkException implements ExceptionInterface
{
	public static function forMissingName()
	{
		return new static(lang('Settings.missingName'));
	}
	
	public static function forUnmatchedName(string $name)
	{
		return new static(lang('Settings.unmatchedName', [$name]));
	}
	
	public static function forProtectionViolation(string $name)
	{
		return new static(lang('Settings.protectionViolation', [$name]));
	}
}
