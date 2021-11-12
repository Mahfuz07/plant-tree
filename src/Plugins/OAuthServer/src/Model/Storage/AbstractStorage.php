<?php
namespace OAuthServer\Model\Storage;

use Cake\Datasource\ModelAwareTrait;
use Cake\ORM\TableRegistry;
use League\OAuth2\Server\Storage\AbstractStorage as BaseAbstractStorage;

abstract class AbstractStorage extends BaseAbstractStorage
{
    use ModelAwareTrait;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->modelFactory('Table', [TableRegistry::getTableLocator(), 'get']);
//        $this->modelFactory('Table', ['Cake\ORM\Locator\TableLocator', 'get']);
    }
}
