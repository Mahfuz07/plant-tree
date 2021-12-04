<?php

namespace App\Controller\Api;

use Cake\Datasource\ConnectionManager;
use Cake\Event\EventInterface;
use Cake\Routing\Router;
use Cake\Utility\Security;
use ManageUser\Controller\AppController;
use phpDocumentor\Reflection\Types\This;

class LocalDevicesController extends AppController
{

    public $default_components = ['AccessToken'];
    public $mode;

    public function initialize(): void
    {
        parent::initialize();
        $database       = ConnectionManager::getConfig('default')['database'];
        $this->loadComponent('RequestHandler');
        $this->loadComponent('Common');

        $this->Users = $this->getDbTable('ManageUser.Users');
        $this->Roles =  $this->getDbTable('ManageUser.Roles');
        $this->Categories = $this->getDbTable('Categories');
        $this->Products = $this->getDbTable('Products');
        $this->ProductImages = $this->getDbTable('ProductImages');

        $this->mode = $this->Common->getLocalServerDeviceMode();
    }

    public function beforeFilter(EventInterface $event)
    {
        $this->log($this->request->getParam('action'));
        parent::beforeFilter($event);
        //$this->getEventManager()->off($this->Csrf);
        $this->Session= $this->getRequest()->getSession();
        $this->Auth->allow([
            'login', 'getTokenByRefreshToken', 'logout', 'createUser', 'getAllProductsByCategory'
        ]);
        $actions =  array(
            'login', 'getTokenByRefreshToken', 'logout', 'createUser', 'getProduct', 'getAllProductsByCategory'
        );
        $this->Security->setConfig('unlockedActions', $actions);
    }

    public function login()
    {
        if($this->request->is('post')){

            $request_data = file_get_contents("php://input");
            $request_data = $this->json_decode($request_data, true);
            $this->log($request_data);

            $email = isset($request_data['User']['email']) ? $request_data['User']['email']:'';
            $password = isset($request_data['User']['password']) ? $request_data['User']['password']:'';

            $udid = isset($request_data['User']['udid'])?$request_data['User']['udid']:'';
            $device_name = isset($request_data['User']['device_name'])?$request_data['User']['device_name']:'';

            if(!empty($email) && !empty($password)){

                $users = $this->Users->find()->where([
                    'email' => $email,
                    'password' => Security::hash($password, null, true),
                    'status' => true
                ])->first();

                if($users){
                    $users['Role'] = $this->Roles->find()->where([
                        'id' => $users['role_id']
                    ])->first();

                    $token = $this->AccessToken->getAccessToken();

                        $this->getComponent('CommonFunction')->UserLoginDetailsSave($users['id'], $udid, $device_name, $token['access_token'], $token['refresh_token']);
//                        $dataLogTable =  $this->getDbTable('UserLoginDetails');
//                        $data_log_entities = $dataLogTable->newEmptyEntity();
//                        $data_log_entities->user_id = $users['id'];
//                        $data_log_entities->created = date('Y-m-d H:i:s');
//                        $data_log_entities->udid = $udid;
//                        $data_log_entities->device_name = $device_name;
//                        $data_log_entities->access_token = $token['access_token'];
//                        $data_log_entities->login_status = (int)true;
//                        $dataLogTable->save($data_log_entities);

                    $this->set(array(
                        'status' => 'success',
                        'user' => $users,
                        'token' => $token,
                        'mode' => $this->mode,
                        '_serialize' => array('status', 'user', 'token', 'mode')
                    ));
                    return $this->getResponse()
                        ->withStatus(200)
                        ->withType('application/json')
                        ->withStringBody(json_encode(array(
                            'status' => 'success',
                            'user' => $users,
                            'token' => $token,
                            'mode' => $this->mode)));
                }else{
                    return $this->getResponse()
                        ->withStatus(200)
                        ->withType('application/json')
                        ->withStringBody(json_encode(array(
                            'status' => 'error',
                            'msg' => 'Invalid username or password',
                            'mode' => $this->mode)));
                }

            }else{
                $this->set(array(
                    'status' => 'error',
                    'msg' => 'Invalid username or password',
                    'mode' => $this->mode,
                    '_serialize' => array('status', 'msg', 'mode')
                ));
            }

        }else{
            $this->set(array(
                'status' => 'error',
                'msg' => 'Invalid request method',
                'mode' => $this->mode,
                '_serialize' => array('status', 'msg', 'mode')
            ));
        }
    }

    public function getTokenByRefreshToken()
    {
        if($this->request->is('post')){

            $request_data = file_get_contents("php://input");
            $request_data = $this->json_decode($request_data, true);
            $this->log($request_data);

//            $this->Common->setLog(['request_refresh_token_get'=>$this->request->getQuery('university_id'), 'request_post'=>$request_data]);
            $refresh_token = $request_data['refresh_token'];
            $this->log('refresh token request :');
            $this->log($request_data);
            $this->log('refresh token header request:');
            $this->log( $this->request->getHeader('Authorization'));

            if(isset($refresh_token) && !empty($refresh_token)){

                $result = $this->AccessToken->getTokenByRefreshToken($refresh_token);

                $this->UserLoginDetails =  $this->getDbTable('UserLoginDetails');
                $data_log_entities = $this->UserLoginDetails->find()->where(['refresh_token' => $refresh_token])->first();
                $data_log['access_token'] = $result['access_token'];
                $data_log['refresh_token'] = $result['refresh_token'];
                $data_log_entities = $this->UserLoginDetails->patchEntity($data_log_entities, $data_log);
                $this->UserLoginDetails->save($data_log_entities);

                if(isset($result['error'])){
                    return $this->getResponse()
                        ->withStatus(200)
                        ->withType('application/json')
                        ->withStringBody(json_encode(array(
                            'status' => 'error',
                            'msg' => $result['message'],
                            'mode' => $this->mode)));
                }else{
                    return $this->getResponse()
                        ->withStatus(200)
                        ->withType('application/json')
                        ->withStringBody(json_encode(array(
                            'status' => 'success',
                            'token' => array(
                                'access_token'=> $result['access_token'],
                                'token_type'=> $result['token_type'],
                                'expires_in'=> $result['expires_in'],
                                'refresh_token'=> $result['refresh_token']
                            ),
                            'mode' => $this->mode)));
                }
            }else{
                return $this->getResponse()
                    ->withStatus(200)
                    ->withType('application/json')
                    ->withStringBody(json_encode(array(
                        'status' => 'error',
                        'msg' => 'The refresh token is invalid.',
                        'mode' => $this->mode,
                        $this->viewBuilder()->setOption('serialize', true))));
            }
        }else{
            return $this->getResponse()
                ->withStatus(200)
                ->withType('application/json')
                ->withStringBody(json_encode(array(
                'status' => 'error',
                'msg' => 'Invalid request method',
                'mode' => $this->mode)));
        }
    }

    public function createUser () {

        if($this->request->is('post')){
            $request_data = file_get_contents("php://input");
            $request_data = $this->json_decode($request_data, true);
            $this->log($request_data);

            if (!empty($request_data)) {
                $email = isset($request_data['User']['email']) ? $request_data['User']['email']:'';
                $displayName = isset($request_data['User']['display_name']) ? $request_data['User']['display_name']:'';
                $password = isset($request_data['User']['password']) ? $request_data['User']['password']:'';

                $errorMessage = [];
                if (empty($displayName)) {
                    array_push($errorMessage,['Required field display name is missing']);
                }
                if (empty($email)) {
                    array_push($errorMessage,['Required field email is missing']);
                }
                if (empty($password)) {
                    array_push($errorMessage, ['Required field password is missing']);
                }

                if (count($errorMessage) == 0) {
                    $getEmail = $this->Users->find()->where(['email' => $email])->first();
                    if (empty($getEmail)) {
                        $roles = $this->Roles->find()->all()->toArray();
                        $users = $this->Users->newEmptyEntity();
                        $userData['display_name'] = $displayName;
                        $userData['email'] = $email;
                        $userData['password'] = Security::hash($password, null, true);
                        $userData['role_id'] = $roles[1]['id'];
                        $userData['status'] = 1;

                        $users = $this->Users->patchEntity($users, $userData);
                        $user = $this->Users->save($users);
                        if ($user->id) {
                            return $this->getResponse()
                                ->withStatus(200)
                                ->withType('application/json')
                                ->withStringBody(json_encode(array(
                                    'status' => 'success',
                                    'msg' => 'Account Create Successfully',
                                    'mode' => $this->mode)));
                        } else{
                            return $this->getResponse()
                                ->withStatus(200)
                                ->withType('application/json')
                                ->withStringBody(json_encode(array(
                                    'status' => 'error',
                                    'msg' => 'Oops user create failed',
                                    'mode' => $this->mode)));
                        }
                    } else {
                        return $this->getResponse()
                            ->withStatus(404)
                            ->withType('application/json')
                            ->withStringBody(json_encode(array(
                                'status' => 'error',
                                'msg' => 'Email Already Exist!',
                                'mode' => $this->mode)));
                    }
                } else {
                    return $this->getResponse()
                        ->withStatus(404)
                        ->withType('application/json')
                        ->withStringBody(json_encode(array(
                            'status' => 'error',
                            'msg' => $errorMessage,
                            'mode' => $this->mode)));
                }
            } else {
                return $this->getResponse()
                    ->withStatus(200)
                    ->withType('application/json')
                    ->withStringBody(json_encode(array(
                        'status' => 'error',
                        'msg' => 'Invalid request method',
                        'mode' => $this->mode)));
            }
        }else{
            return $this->getResponse()
                ->withStatus(200)
                ->withType('application/json')
                ->withStringBody(json_encode(array(
                    'status' => 'error',
                    'msg' => 'Invalid request method',
                    'mode' => $this->mode)));
        }
    }

    public function getAllProductsByCategory() {

        if ($this->AccessToken->verify()) {
            if ($this->request->is('get')) {

                $fullUrl = Router::fullBaseUrl();
                $products = $this->Products->find()->where(['category_id in (SELECT id FROM categories WHERE published = 1)', 'published' => 1])->toArray();

                if (!empty($products)) {

                    foreach ($products as $product) {
                        $images = $this->ProductImages->find()->where(['product_id' => $product['id']])->toArray();
                        if (!empty($images)) {
                            $imageArray = [];
                            foreach ($images as $image) {
                                $imageArray[] = $fullUrl . '/' . $image['image_path'];
                            }
                            $product['image'] = $imageArray;
                        }
                    }

                }

                if (!empty($products)) {
                    return $this->getResponse()
                        ->withStatus(200)
                        ->withType('application/json')
                        ->withStringBody(json_encode(array(
                            'status' => 'success',
                            'products' => $products,
                            'mode' => $this->mode)));
                } else {
                    return $this->getResponse()
                        ->withStatus(200)
                        ->withType('application/json')
                        ->withStringBody(json_encode(array(
                            'status' => 'success',
                            'msg' => 'No products have been released yet.',
                            'mode' => $this->mode)));
                }

            } else {
                return $this->getResponse()
                    ->withStatus(200)
                    ->withType('application/json')
                    ->withStringBody(json_encode(array(
                        'status' => 'error',
                        'msg' => 'Invalid request method',
                        'mode' => $this->mode)));
            }
        } else {
            header('HTTP/1.1 401 Unauthorized', true, 401);
            return $this->getResponse()
                ->withStatus(401)
                ->withType('application/json')
                ->withStringBody(json_encode(array(
                    'status' => 'error',
                    'msg' => 'Invalid access token.',
                    'mode' => $this->mode)));
        }
    }

    public function getProduct() {

        if ($this->AccessToken->verify()) {
            if ($this->request->is('post')) {
                $request_data = file_get_contents("php://input");
                $request_data = $this->json_decode($request_data, true);
                $this->log($request_data);

                if (!empty($request_data)) {
                    $product_id = isset($request_data['Product']['product_id']) ? $request_data['Product']['product_id']:'';

                    $fullUrl = Router::fullBaseUrl();
                    $product = $this->Products->find()->where(['id' => $product_id, 'published' => 1])->first();

                    if (!empty($product)) {
                        $images = $this->ProductImages->find()->where(['product_id' => $product['id']])->toArray();
                        if (!empty($images)) {
                            $imageArray = [];
                            foreach ($images as $image) {
                                $imageArray[] = $fullUrl . '/' . $image['image_path'];
                            }
                            $product['image'] = $imageArray;
                        }
                    }

                    if (!empty($product)) {
                        return $this->getResponse()
                            ->withStatus(200)
                            ->withType('application/json')
                            ->withStringBody(json_encode(array(
                                'status' => 'success',
                                'products' => $product,
                                'mode' => $this->mode)));
                    } else {
                        return $this->getResponse()
                            ->withStatus(200)
                            ->withType('application/json')
                            ->withStringBody(json_encode(array(
                                'status' => 'success',
                                'msg' => 'Product Not Published',
                                'mode' => $this->mode)));
                    }
                } else {
                    return $this->getResponse()
                        ->withStatus(200)
                        ->withType('application/json')
                        ->withStringBody(json_encode(array(
                            'status' => 'error',
                            'msg' => 'Missing Input Data!',
                            'mode' => $this->mode)));
                }

            } else {
                return $this->getResponse()
                    ->withStatus(200)
                    ->withType('application/json')
                    ->withStringBody(json_encode(array(
                        'status' => 'error',
                        'msg' => 'Invalid request method',
                        'mode' => $this->mode)));
            }
        } else {
            header('HTTP/1.1 401 Unauthorized', true, 401);
            return $this->getResponse()
                ->withStatus(401)
                ->withType('application/json')
                ->withStringBody(json_encode(array(
                    'status' => 'error',
                    'msg' => 'Invalid access token.',
                    'mode' => $this->mode)));
        }
    }

}
