<?php

namespace App\Model\Table;

use Cake\ORM\Table;

class ProductRecentlyViewTable extends Table {

    public function  initialize(array $config = []): void
    {
        parent::initialize($config);
        $this->setTable('product_recently_view');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
    }
    public static function defaultConnectionName():string
    {
        return 'default';
    }

}
