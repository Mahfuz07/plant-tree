<?php

namespace App\Model\Table;

use Cake\ORM\Table;

class AccessPermissionsTable extends Table
{

//    public $useDbConfig = 'common';

    public $belongsTo = array(
        'Role' => array(
            'className' => 'ManageUser.Role',
            'foreignKey' => 'role_id',
            'joinType' => 'INNER'
        )
    );

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config = []): void
    {
        parent::initialize($config);

        $this->setTable('access_permissions');
        $this->setPrimaryKey('id');
    }

    public static function defaultConnectionName():string
    {
        return 'default';
    }

}