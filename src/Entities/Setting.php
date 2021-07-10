<?php

namespace Tatter\Settings\Entities;

use CodeIgniter\Entity\Entity;

class Setting extends Entity
{
    protected $table = 'settings';

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
        $this->setContentCast($data['datatype'] ?? 'string');

        parent::__construct($data);
    }

    /**
     * Sets the cast datatype for
     * the content field.
     *
     * @param string $datatype
     *
     * @return $this
     */
    public function setContentCast(string $datatype = 'string')
    {
        $this->casts['content'] = $datatype;

        return $this;
    }
}
