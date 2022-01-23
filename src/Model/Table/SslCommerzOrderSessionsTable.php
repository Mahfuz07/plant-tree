<?php

namespace App\Model\Table;

use Cake\ORM\Table;

class SslCommerzOrderSessionsTable extends Table
{

    public function  initialize(array $config = []): void
    {
        parent::initialize($config);
        $this->setTable('ssl_commerz_order_session');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
    }
    public static function defaultConnectionName():string
    {
        return 'default';
    }

}
