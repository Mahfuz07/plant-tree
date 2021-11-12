<?php

namespace App\Controller\Api;

use Cake\Datasource\ConnectionManager;
use Cake\Event\EventInterface;
use Cake\Utility\Security;
use ManageUser\Controller\AppController;

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

        $this->mode = $this->Common->getLocalServerDeviceMode();
    }

    public function beforeFilter(EventInterface $event)
    {
        $this->log($this->request->getParam('action'));
        parent::beforeFilter($event);
        //$this->getEventManager()->off($this->Csrf);
        $this->Session= $this->getRequest()->getSession();
        $this->Auth->allow([
            'login', 'getTokenByRefreshToken', 'logout'
        ]);
        $actions =  array(
            'login', 'getTokenByRefreshToken', 'logout'
        );
        $this->Security->setConfig('unlockedActions', $actions);
    }

    public function login()
    {
        if($this->request->is('post')){

            $request_data = file_get_contents("php://input");
            $request_data = $this->json_decode($request_data, true);
            $this->log($request_data);

            $username = isset($request_data['User']['username']) ? $request_data['User']['username']:'';
            $password = isset($request_data['User']['password']) ? $request_data['User']['password']:'';

            $udid = isset($request_data['User']['udid'])?$request_data['User']['udid']:'';
            $device_name = isset($request_data['User']['device_name'])?$request_data['User']['device_name']:'';

            if(!empty($username) && !empty($password)){

                $users = $this->Users->find()->where([
                    'username' => $username,
                    'password' => Security::hash($password, null, true),
                    'status' => true
                ])->first();

//                dd($users);
                if($users){
                    $users['Role'] = $this->Roles->find()->where([
                        'id' => $users['role_id']
                    ])->first();

                    $token = $this->AccessToken->getAccessToken();
//                    dd($token);
                        //set user login details

//                        $this->getComponent('CommonFunction')->UserLoginDetailsSave($users['id'], $udid, $device_name, $token['access_token'], $users['university_id']);
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

}