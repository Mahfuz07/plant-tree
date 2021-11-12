<?php
namespace ManageUser\Controller;

use Cake\Controller\Component\AuthComponent;
use Cake\Core\Configure;
use Cake\Event\EventInterface;
use Cake\Routing\Router;
use Cake\Network\Session;
use Cake\Utility\Security;
use ManageUser\Controller\AppController;
use Cake\Auth\Auth;
use Cake\Log\Log;

/**
 * Users Controller
 *
 * @property \ManageUser\Model\Table\UsersTable $Users
 *
 * @method \ManageUser\Model\Entity\User[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class UsersController extends AppController
{
    public $maximumRequest = false;

    public $default_components = ['CommonFunction', 'Security'];

    public $paginate = [
        'limit' => 20
    ];

    public function initialize(): void
    {
        parent::initialize();

        $this->Users = $this->getDbTable('ManageUser.Users');
        $this->Roles = $this->getDbTable('ManageUser.Roles');

        $this->Auth->allow(['login', 'logout', 'checkEmail', 'resetPassword']);
    }

    public function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event); // TODO: Change the autogenerated stub
        //$this->getEventManager()->off($this->Csrf);
        $this->Security->setConfig('unlockedActions', ['login']);
    }

//    public function beforeRender(Event $event)
//    {
//        // Set the layout.
////        $this->viewBuilder()->setLayout('admin_login');
//
//        return parent::beforeRender($event); // TODO: Change the autogenerated stub
//    }

    public function index()
    {
        $this->set('title_for_layout', ('Users'));
    }

    public function login() {

        $this->set('title_for_layout', 'Log in');
        if ($this->request->is('post')) {

            $data = $this->request->getData();

            $username = trim($data['username']);
            if (!empty($this->request->getParam('_ext')) && $this->request->getParam('_ext') == 'json') {
                $uid = $this->sessionRead('Auth.User.id');
                if (!empty($uid)) {
                    $this->Auth->logout();
                }
            }

            $password = $data['password'] ?? '';
            $role_alias = 'Admin';
            $success = false;

            if (!empty($username) && !empty($password)) {
                $role = $this->Roles->find()->where([
                    'alias' => $role_alias
                ])->first();

                $role_id = $role ? $role->id : '';
                $conditions = [
                    'username' => $username,
                    'password' => Security::hash($password, null, true),
                    'status' => true,
                    'role_id' => $role_id,
                ];
                $loggedIn = [];
                $users = $this->Users->find()->where($conditions)->first();

                $users['Role'] = $role;
                $response = json_decode($users, true);
                if( !empty($response) && isset($response['status'])){
                    $loggedIn = $response;
                }

                if (!empty($loggedIn['Role'])) {
                    $this->sessionWrite('Auth', $loggedIn);
                    $this->Auth->setUser($loggedIn);
                    $success = true;
                }

                if ($success) {
                    return $this->redirect('admin/dashboard');
                } else {
                    $this->Flash->error('Email or password doesn\'t match', ['key' => 'error']);
                }
            }
        }
    }

}