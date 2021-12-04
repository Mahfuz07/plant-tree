<?php

namespace App\Controller\Component;

use Cake\Core\Configure;
use Cake\Datasource\ConnectionManager;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;

class CommonFunctionComponent extends BaseComponent
{

    var $controller;
    var $Session;

    function startup(Event $event)
    {
        $this->controller = $this->_registry->getController();
        $this->Session = $this->controller->getRequest()->getSession();
    }

    public function apiLogin($user)
    {

        $data = array(
            "User" => array(
                "username" => $user['username'],
                "password" => $user['password'],
                "remember" => 1
            )
        );
        if (isset($user['role_alias'])) {
            $data['User']['role_alias'] = $user['role_alias'];
        }
        $data_string = json_encode($data);
        if (isset($user['role_alias'])) {
            $url = Configure::read('API_HOST') . "api/users/users/api_user_role_login.json";
        } else {
            $url = Configure::read('API_HOST') . "api/users/users/api_user_login.json";
        }

        $header = array(
            'Content-Type: application/json'
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $result = curl_exec($ch);
        $response = json_decode($result, true);
        if( !empty($response) && isset($response['status']) && $response['status'] == 'success' ){
            $user['User'] = $response['user'];
        } else {
            $this->log('User login error = '. json_encode($response));
        }

        curl_close($ch);

        return $user;
    }

    public function UserLoginDetailsSave($user_id, $udid, $device_name, $access_token, $refresh_token) {

        if (empty($udid)) {
            $udid = "empty";
        }

        if (empty($device_name)) {
            $device_name = "empty";
        }

        $conn = ConnectionManager::get('default');
        $sql = "INSERT INTO user_login_details SET user_id=" . $user_id . ", udid='". $udid . "', device_name='" . $device_name . "', access_token='" . $access_token . "', refresh_token='" . $refresh_token . "', login_status=" . 1;
        $conn->query($sql);

    }

    public function getUserInfo () {

        $accessToken = $this->controller->getRequest()->getHeader('Authorization');
        $accessToken = explode('Bearer ', $accessToken[0]);

        if(count($accessToken) == 2 && !empty($accessToken[1])) {

            $userLoginDetails =  TableRegistry::getTableLocator()->get('UserLoginDetails');
            $getUserLoginDetails = $userLoginDetails->find()
                ->where([
                    'access_token' => trim($accessToken[1])
                ])->first();

            $this->Users = $this->getDbTable('ManageUser.Users');
            $getUserDetails = $this->Users->find()->where(['id' => $getUserLoginDetails['user_id']])->first();

            return $getUserDetails;
        } else {
            return false;
        }
    }

}
