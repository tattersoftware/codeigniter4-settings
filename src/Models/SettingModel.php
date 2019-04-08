<?php namespace Tatter\Settings\Models;

use CodeIgniter\Model;

class SettingModel extends Model
{
	protected $table      = 'settings';
	protected $primaryKey = 'id';

	protected $returnType = 'object';
	protected $useSoftDeletes = true;

	protected $allowedFields = ['name', 'scope', 'content', 'summary', 'protected'];

	protected $useTimestamps = true;

	protected $validationRules    = [];
	protected $validationMessages = [];
	protected $skipValidation     = false;

}
