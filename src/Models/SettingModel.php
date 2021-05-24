<?php namespace Tatter\Settings\Models;

use CodeIgniter\Model;

class SettingModel extends Model
{
	protected $table      = 'settings';
	protected $primaryKey = 'id';
	protected $returnType = 'object';

	protected $useTimestamps  = true;
	protected $useSoftDeletes = true;
	protected $skipValidation = false;

	protected $allowedFields = [
		'name',
		'scope',
		'summary',
		'content',
		'protected',
	];

	protected $validationRules = [
		'name'      => 'required|max_length[63]',
		'scope'     => 'required|in_list[global,user,session]',
		'summary'   => 'permit_empty|max_length[255]',
		'content'   => 'permit_empty|max_length[255]',
		'protected' => 'in_list[0,1]',
	];
}
