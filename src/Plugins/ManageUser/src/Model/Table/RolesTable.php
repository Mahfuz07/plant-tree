<?php
namespace ManageUser\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Roles Model
 *
 * @property \ManageUser\Model\Table\UsersTable|\Cake\ORM\Association\HasMany $Users
 *
 * @method \ManageUser\Model\Entity\Role get($primaryKey, $options = [])
 * @method \ManageUser\Model\Entity\Role newEntity($data = null, array $options = [])
 * @method \ManageUser\Model\Entity\Role[] newEntities(array $data, array $options = [])
 * @method \ManageUser\Model\Entity\Role|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \ManageUser\Model\Entity\Role|bool saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \ManageUser\Model\Entity\Role patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \ManageUser\Model\Entity\Role[] patchEntities($entities, array $data, array $options = [])
 * @method \ManageUser\Model\Entity\Role findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class RolesTable extends Table
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
        $this->setTable('roles');

        $this->setDisplayField('title');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->hasMany('Users', [
            'foreignKey' => 'role_id',
            'className' => 'ManageUser.Users'
        ]);
        $this->hasMany('AccessPermissions', [
            'foreignKey' => 'role_id',
            'bindingKey' => 'id',
            'className' => 'AccessPermissions'
        ]);
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules):RulesChecker
    {
        $rules->add($rules->isUnique(['alias']));

        return $rules;
    }
}
