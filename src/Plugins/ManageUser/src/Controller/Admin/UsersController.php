<?php
namespace ManageUser\Controller\Admin;

use Cake\ORM\TableRegistry;
use ManageUser\Controller\AppController;
use Cake\Core\Configure;
use Cake\Cache\Cache;
use Cake\Log\Log;
use Cake\Utility\Security;
use Cake\Http\Cookie\CookieCollection;
use Cake\Http\Cookie\Cookie;

use Cake\Event\EventInterface;
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
        $this->Orders = $this->getDbTable('Orders');
        $this->Products = $this->getDbTable('Products');

         $this->Auth->allow(['login', 'logout', 'checkEmail', 'resetPassword']);
    }

    public function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event); // TODO: Change the autogenerated stub
        //$this->getEventManager()->off($this->Csrf);
        $this->Security->setConfig('unlockedActions', ['login']);
    }

    /**
     * Index method
     *
     * @return \Cake\Http\Response|void
     */
    public function index()
    {
        $this->set('title_for_layout', ('Users'));
    }

    public function dashboard()
    {

        $totalProduct = $this->Products->find()->where()->count();
        $totalOrder = $this->Orders->find()->where()->count();
        $totalCustomer = $this->Users->find()->where(['role_id' => 2])->count();

        $this->set('total_order', $totalOrder);
        $this->set('total_product', $totalProduct);
        $this->set('total_customer', $totalCustomer);

    }

    /**
     * View method
     *
     * @param string|null $id User id.
     * @return \Cake\Http\Response|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $user = $this->Users->get($id, [
            'contain' => ['Roles']
        ]);

        $this->set('user', $user);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {

    }

    /**
     * Admin edit
     *
     * @param integer $id
     * @return void
     * @access public
     */
    public function edit($id = null)
    {

    }

    /**
     * Delete method
     *
     * @param string|null $id User id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {

        $this->request->allowMethod(['post', 'delete']);
        $user = $this->Users->get($id);

        $response = $this->beforeDelete($id, $user);

        if(!empty($response)){
            if ($this->Users->delete($user)) {
                $this->Flash->adminSuccess('The user has been deleted.', ['key'=>'admin_success']);
            } else {
                $this->Flash->adminError('The user could not be deleted. Please, try again.', ['key'=>'admin_error']);
            }
        }else{
            $this->Flash->adminError('The user could not be deleted. Please, try again.', ['key'=>'admin_error']);
        }

        return $this->redirect(['action' => 'index']);
    }

     public function login() {

        $this->set('title_for_layout', 'Log in');

         $user = $this->sessionRead('Auth.User');

         $this->log('$user 001');
         $this->log($user);

         if(!empty($user)){
             return $this->redirect('/admin/dashboard');
         }

        if ($this->request->is('post')) {

            $data = $this->request->getData();

            $username = trim($data['email']);
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
                    'email' => $username,
                    'password' => Security::hash($password, null, true),
                    'status' => true,
                    'role_id' => $role_id,
                ];
                $loggedIn = [];
                $users = $this->Users->find()->where($conditions)->first();

                $users['Role'] = $role;
                $response = json_decode($users, true);
                if( !empty($response) && isset($response['status'])){
                    $loggedIn['User'] = $response;
                }

                if (!empty($loggedIn['User']['Role'])) {
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

    public function logout()
    {
        $this->request->getSession()->delete('Flash');
        $this->_deleteCookie();
        $this->Flash->adminSuccess('You are loggedout', ['key' => 'admin_success']);
        $this->Auth->logout();
        $this->request->getSession()->destroy();
        return $this->redirect('/admin/login');
    }

    protected function _deleteCookie() {
        $cookies = new \Cake\Http\Cookie\CookieCollection();
        $cookies->remove('RememberMe');

        return true;
    }

    public function forgotPassword() {
        $this->layout = 'ManageUser.loginLayout';

        if (!empty($this->getRequestData()) && !empty($this->getRequestData('username'))) {
            $sub_domain = $this->CommonFunction->getSubDomainName();
            $data = array(
                'User' => $this->getRequestData()
            );
            $data['User']['subdomain']= $sub_domain;

            $data_string = http_build_query($data);
            $url = Configure::read('API_HOST') . "users/users/api_forgot_password.json";

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $result = curl_exec($ch);
            curl_close($ch);

            $response = json_decode($result, true);
            if ($response['status'] == 'success') {
                $this->Flash->adminSuccess($response['msg'], ['key' => 'admin_success']);
            } else {
                $this->Flash->adminError($response['msg'], ['key' => 'admin_error']);

                $this->redirect('/admin/login');
            }
        }
    }

    // for super admin login
    //redirects to dashboard



    public function changeState($country_name='')
    {
        $states =array();
        if($country_name==''){
            $country   =   $this->request->getQuery()['Student']['country'];
        }else{
            $country   =   $country_name;
        }
        $country = $this->Users->getCountryInfoByName($country);
        if(isset($country)&&!empty($country)){
            $country_id = $country['id'];
            $states = $this->Users->getStateList($country_id);
        }

        $this->set('states', $states);
    }

    public function checkUserName()
    {
        $username = $this->request->getQuery()['username'];
        if (empty($username)) {
            echo 'This field is required.';
            exit();
        }
        $users = $this->Users->find()->where(['username' => $username])->enableHydration(false)->first();
        if (!empty($users)) {
            echo 'Our system already has a record of this username.';
        } else {
            echo '';
        }
        exit();
    }

    public function checkEmail()
    {
        $email = $this->request->getQuery()['email'];
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $users = $this->Users->find()->where(['OR' => array('email' => $email, 'username' => $email)])->enableHydration(false)->first();
            if (!empty($users)) {
                echo 'Our system already has a record of this email address - please try logging in with same email address.';
            } else {
                //echo 'Congratulation. Email address is empty!';
                echo '';
            }
        } else {
            // invalid address
            echo 'Not Valid.';
        }
        exit();
    }

    function _buildContentWhere($data = array())
    {

        $conditions = [];

        //START: Filter
        if (isset($data['name']) && $data['name']) {
            $conditions = ['OR' => [
                ['Users.name LIKE' => '%' . $data['name'] . '%'],
                ['Users.username LIKE' => '%' . $data['name'] . '%']
            ]];
        }
        //END: Filter

        //START: Status
        if (isset($data['website']) && $data['website'] != "") {
            $conditions['Users.website LIKE'] = '%' . $data['website'] . '%';
        }

        //END: Status

        //START: Category
        if (isset($data['role_id']) && $data['role_id'] != "") {
            $conditions['Users.role_id'] = $data['role_id'];
        }
        //END: Category

        return $conditions;
    }


    public function resetPassword($id = null)
    {
        $request_data = $this->request->getData();
        if (!$id && empty($this->getRequestData())) {
            $this->Flash->adminError('Invalid User', ['key' => 'admin_error']);
            $this->redirect(array('action' => 'index'));
        }
        if (!empty($request_data)) {
//debug($request_data);
            if ($request_data['password'] == $request_data['confirm_password']) {
                //$this->loadModel('Users');
                $user = $this->Users->find()->where(['Users.id' =>$request_data['user_id']])->first();
                if (!isset($user->id)) {
                    $this->Flash->adminError('Invalid User.', ['key' => 'admin_error']);

                }else{
                    $password = Security::hash($request_data['password'], null, true);
                    //echo SITE_PREFIX; die('sssssssss');
                    if ($this->Users->update_password($password, $user->id)) {
                        $this->Flash->adminSuccess('Password has been reset.', ['key' => 'admin_success']);
                        $this->redirect(array('action' => 'index'));
                    }
                    else{
                        $this->Flash->adminError('Password could not be reset. Please, try again.', ['key' => 'admin_error']);

                    }
                }

            }else{
                $this->Flash->adminError('Password and confirm password didn\'t match .', ['key' => 'admin_error']);
                $this->redirect(array('action' => 'index'));
            }

        }
        /*if (empty($this->request->data)) {
            $this->request->data = $this->Users->find()->where(['Users.id' =>$id])->first();
        }*/
        $this->set('user_id', $id);
    }

    public function resetPassword_prev($id = null)
    {

        if (!empty($this->request->getData()) && isset($this->request->getData()['password'])) {
            $data = array(
                'User' => $this->request->getData()
            );

            $data_string = http_build_query($data);

            $url = Configure::read('API_HOST') . "api/users/users/api-change-password.json";
echo $url;die;
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $result = curl_exec($ch);
            curl_close($ch);

            $response = json_decode($result, true);

            if ($response['status'] == 'success') {
                $this->Users->reset_password($response['key'], $response['password'], $this->request->getData()['user_id']);
                $this->Flash->adminSuccess($response['msg'], ['key' => 'admin_success']);
                return $this->redirect(['action' => 'resetPassword', $this->request->getData()['user_id']]);
            } else if ($response['msg'] == 'Passwords do not match. Please, try again.') {
                $this->Session->setFlash($response['msg'], 'default', array('class' => 'error'));
            } else {
                $this->Flash->adminError($response['msg'], ['key' => 'admin_error']);
                return $this->redirect(['action' => 'resetPassword', $this->request->getData()['id']]);
            }
        }

        $this->set('user_id', $id);
    }

    public function beforeDelete($id, $user)
    {
        $this->Roles = TableRegistry::get('Roles');
        $this->loadModel('Users');
        $admin_role = $this->Roles->find()->where(['Roles.alias'=>'admin'])->enableHydration(false)->first();
        $adminRoleId = $admin_role['id'];

        $current = $this->request->getSession()->read('Auth.User');

        if (!empty($current['id']) && $current['id'] == $id) {
            return false;
        }
        if ($user->role_id == $adminRoleId) {
            $count = $this->Users->find()->where(['Users.id <>' => $id, 'Users.role_id' => $adminRoleId, 'Users.status' => true])->count();
            return ($count > 0);
        }

        $this->Users->deleteStudentInfo($id);

        return true;
    }

    public function resetCache(){
        Cache::clear(false, 'default');
        Cache::clear(false, '_cake_core_');
        Cache::clear(false, '_cake_model_');
        echo 'Cache clear done.';
        die();
    }

    public function userPermissionUniversitiesProducts($user_id = null)
    {
        $this->University = $this->loadModel('Universities');

        if (!empty($this->request->getData()['university_id']) && is_numeric($this->request->getData()['university_id'])) {

            $userId = $this->request->getData()['userId'];
            $university_id = $this->request->getData()['university_id'];
            $db_prefix = $this->University->find()->where(['id' => (int)trim($university_id)])->first();
//            $category_array = $this->CommonFunction->getCategoriesbyUniversity($db_prefix['url']);
            //$category_array = $this->CommonFunction->getCategoriesbyUniversity($db_prefix['table_prefix'],$userId);
            $category_array = $this->CommonFunction->getCatsWithPermsByUser($db_prefix['table_prefix'],$userId);
            //pr($category_array; die('zzzzzzzzzzzzz');
            // $categories = array();
            // $i = 0;
            // foreach ($category_array as $item){
            //     $categories[$i]['id'] = $item['id'];
            //     $categories[$i]['title'] = $item['title'];
            //     $categories[$i]['slug'] = $item['slug'];
            //     $categories[$i]['product_type'] = $item['product_type'];
            //     $i++;
            // }

            //$user_products = $this->CommonFunction->getProductsbyUser($db_prefix['table_prefix'], $userId);
            //$user_products = $this->CommonFunction->getProductsWithCatsByUser($db_prefix['table_prefix'], $userId);
//            foreach ($user_products as $item) {
//                $ff[$i] = $item['category_id'];
//            }

            $this->set($dd=array(
                'status' => 'success',
                'available_statuses' => $this->CommonFunction->getStatusByCatType(),
                'categories' => $category_array,
                //'user_products' => $user_products,
                '_serialize' => array('status', 'available_statuses', 'categories', 'userId', 'user_products')
            ));
            //pr($dd);die();
            /*
             * Products by category list
             *

            */
        } else {

            $universities = $this->University->find()->all()->toArray();

            $user_universities = $this->CommonFunction->getUniversitybyUser($user_id);
//            $user_universities = $this->CommonFunction->getCategoriesbyUser($user_id);dd($user_universities);
            $i = 0;
            $user = array();
            $university_array = array();
            if(!empty($user_universities)) {
                foreach ($user_universities as $university) {
//                $db_prefix = $this->University->find('first', array(
//                    'conditions' => array('University.id' => (int)trim($university['universities']['university_id']))
//                ));
                    $db_prefix = $this->University->find()->where(['id' => (int)trim($university['university_id'])])->first();
                    $user['university_products'][$i]['university'] = $university['university_id'];
                    $university_array[] = $university['university_id'];
                    $user_products = $this->CommonFunction->getProductsWithCatsByUser($db_prefix['table_prefix'], $user_id);
                    // foreach ($user_products as $item) {
                    //     $user['university_products'][$i]['products'][] = $item['category_id'];
                    // }
                    $user['university_products'][$i]['products'] = $user_products;
                }
            }

            $this->set(array(
                'user_id' => (int)$user_id,
                'user' => $user,
                'university_array' => $university_array,
                'universities' => $universities
            ));
        }
    }

    public function processUserPermissionUniversitiesProducts()
    {
        //pr($this->request->getData()); die();
        $this->University = $this->loadModel('Universities');
        if (!empty($this->request->getData()['universities'])) {
            $universities = $this->request->getData()['universities'];
            $user_id = (int)$this->request->getData()['user_id'];
            $this->CommonFunction->deleteUserbyUniversity($user_id,$universities);
            foreach ($universities as $university) {
                $db_prefix = $this->University->find()->where(['id' => $universities[0]])->first();
                $university_products = $this->request->getData('university_' . (int)trim($university) . '_products');
                if (!empty($university_products) ){
                    $product_permissions = $this->request->getData('university_' . (int)trim($university) . '_permissions');
//                    $this->CommonFunction->setUniversityProductsbyUser($db_prefix['University']['url'], $user_id, $university_products);
                    $this->CommonFunction->createUniversityProductsbyUser($db_prefix['table_prefix'], $user_id, $university_products, $product_permissions);
                    /* foreach ($university_products as $item) {
                        $this->Product->setProductbyUser($db_prefix['University']['table_prefix'], (int)trim($item), $user_id);
                    } */
                }

                $this->CommonFunction->setUserbyUniversity((int)trim($university), $user_id);
            }

            $this->Flash->adminSuccess('You have successfully modified the permissions.',['key' => 'admin_success']);
            //$this->redirect('/admin/users/');
            $this->redirect('/admin/users/user-permission-universities-products/'.$user_id);
        } else {
            $this->Flash->adminError('Please select products and universities',['key' => 'admin_error']);
            $this->redirect('/admin/users/user_permission_universities_products/');
        }
    }

}
