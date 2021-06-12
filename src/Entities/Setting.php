<?php namespace Tatter\Settings\Entities;

use CodeIgniter\Entity\Entity;

class Setting extends Entity
{
	protected $table      = 'settings';
	protected $primaryKey = 'id';

	protected $dates = [
		'created_at',
		'updated_at',
		'deleted_at',
	];

	protected $casts = [
		'id'        => '?int',
		'protected' => 'bool',
	];

	/**
	 * Forces the content to cast
	 * to its predefined datatype.
	 *
	 * @param array|null $data
	 */
	public function __construct(array $data = null)
	{
		$this->casts['content'] = $data['datatype'] ?? 'string';

		parent::__construct($data);
	}

	/**
	 * Ensures correct casts
	 * for array datatypes.
	 *
	 * @param string $value
	 */
	protected function setContent(string $value)
	{
		// If the setting content is already encoded (i.e., a string) when
		// writing or reading from the database, the 'set' cast must be skipped.
		if(in_array($this->casts['content'], ['array', 'json-array']) && is_string($value))
		{
			$value = $this->castAs($value, 'content');
		}

		$this->attributes['content'] = $value;
	}

}
