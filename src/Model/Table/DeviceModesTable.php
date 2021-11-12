<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * DeviceModes Model
 *
 * @method \App\Model\Entity\DeviceMode get($primaryKey, $options = [])
 * @method \App\Model\Entity\DeviceMode newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\DeviceMode[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\DeviceMode|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\DeviceMode|bool saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\DeviceMode patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\DeviceMode[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\DeviceMode findOrCreate($search, callable $callback = null, $options = [])
 */
class DeviceModesTable extends Table
{

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config = []): void
    {
        parent::initialize($config);
        $this->setTable('device_modes');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');
    }

    public static function defaultConnectionName():string
    {
        return 'default';
    }
    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator):Validator
    {
        $validator
            ->integer('id')
            ->allowEmptyString('id', 'create');

        $validator
            ->scalar('mode')
            ->allowEmptyString('mode');

        $validator
            ->scalar('api_url')
            ->maxLength('api_url', 200)
            ->allowEmptyString('api_url');

        return $validator;
    }
}
