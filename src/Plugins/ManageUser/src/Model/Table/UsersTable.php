<?php
namespace ManageUser\Model\Table;

use App\Model\Table\AppTable;
use App\Model\Entity\User;
use Cake\Datasource\ConnectionManager;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\Utility\Security;
use Cake\Core\Configure;
use Cake\Auth\DefaultPasswordHasher;
use Cake\Routing\Router;
use Cake\Http\Session;
use Cake\Log\Log;

/**
 * Users Model
 *
 * @property \ManageUser\Model\Table\RolesTable|\Cake\ORM\Association\BelongsTo $Roles
 *
 * @method \ManageUser\Model\Entity\User get($primaryKey, $options = [])
 * @method \ManageUser\Model\Entity\User newEntity($data = null, array $options = [])
 * @method \ManageUser\Model\Entity\User[] newEntities(array $data, array $options = [])
 * @method \ManageUser\Model\Entity\User|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \ManageUser\Model\Entity\User|bool saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \ManageUser\Model\Entity\User patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \ManageUser\Model\Entity\User[] patchEntities($entities, array $data, array $options = [])
 * @method \ManageUser\Model\Entity\User findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class UsersTable extends Table
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
        $this->setTable('users');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp', [
            'events' => [
                'Model.beforeSave' => [
                    'created' => 'new',
                    'updated' => 'always',
                ]
            ]
        ]);

        $this->belongsTo('Roles', [
            'foreignKey' => 'role_id',
            'joinType' => 'INNER'
        ]);
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
            ->scalar('username')
            ->maxLength('username', 60)
            ->requirePresence('username', 'create')
            ->allowEmptyString('username', false);

        $validator
            ->scalar('password')
            ->maxLength('password', 100)
            ->requirePresence('password', 'create')
            ->allowEmptyString('password', false);

        $validator
            ->scalar('name')
            ->maxLength('name', 100)
            ->requirePresence('name', 'create')
            ->allowEmptyString('name', false);

        $validator
            ->email('email')
            ->requirePresence('email', 'create')
            ->allowEmptyString('email', false);

        $validator
            ->scalar('image')
            ->maxLength('image', 255)
            ->allowEmptyFile('image');

        $validator
            ->boolean('status')
            ->requirePresence('status', 'create')
            ->allowEmptyString('status', false);

        return $validator;
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
        $rules->add($rules->isUnique(['email']));

        return $rules;
    }

    public function findActiveUser(\Cake\ORM\Query $query, array $options)
    {
        $query->where(['Users.status' => 1]);

        return $query;
    }

    public function get_activation_key($activation_key, $user_id)
    {
        $prefix = $this->getDatabaseTablePrefix('users');
        $conn = ConnectionManager::get('default');
        $sql = "UPDATE " . $prefix . "users SET activation_key = '" . $activation_key . "' WHERE id = " . $user_id;
        $stmt = $conn->query($sql);

    }

    public function check_current_password($data)
    {
        $password = Security::hash($data['current_password'], null, true);
        $user = $this->find('all', ['conditions' => ['Users.username'=>$data['username'], 'Users.password'=> $password ]])->first();
        if (isset($user->id)) {
            return true;
        }
        return false;
    }

    public function verify_password($data)
    {
        if ($data['password'] != $data['verify_password']) {
            return false;
        }
        return true;
    }

    public function reset_password($activation_key, $password, $user_id)
    {

        $prefix = $this->getDatabaseTablePrefix('users');
        $conn = ConnectionManager::get('default');
        $sql = "UPDATE " . $prefix . "users SET activation_key = '" . $activation_key . "', password = '" . $password . "' WHERE id = " . $user_id;

        $stmt = $conn->query($sql);

    }

    public function update_password($password, $user_id)
    {   
        $prefix = $this->getDatabaseTablePrefix('users');
        $conn = ConnectionManager::get('default');
        $sql = "UPDATE " . $prefix . "users SET password = '" . $password . "', updated = NOW() WHERE id = " . $user_id;
        $conn->query($sql);
        return true;
    }

}
