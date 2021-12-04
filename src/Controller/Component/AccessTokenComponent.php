<?php

namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Controller\ComponentRegistry;
use Cake\Core\Configure;
use Cake\Datasource\ConnectionManager;
use Cake\Log\Log;
use Cake\ORM\TableRegistry;
use Cake\Routing\Router;
use Cake\Http\ServerRequest;



class AccessTokenComponent extends BaseComponent
{
    public $baseURI = '';
    public $controller;

    public function __construct(ComponentRegistry $registry, array $config = [])
    {
        parent::__construct( $registry,  $config );
        $this->controller = $this->getController();
        $this->baseURI = Router::url('/', true);

    }

    public function processViaCurl($url,$data=array(),$method="post",$contentType="application/x-www-form-urlencoded"){
        $ssh_mode = FALSE;
        $ch = curl_init();
        if(empty($data['_method'])){
            if(strtolower($method)=="post"){
                curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, "POST" );
            }
            else if(strtolower($method)=="delete"){
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
            }
            else if(strtolower($method)=="put"){
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT"); // note the PUT here
            }
            else if(strtolower($method)=="get"){
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
            }
            else{
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper($method));
            }
        }


        $postString = $this->getPostFieldString($data);
        $header[] = 'Content-length: '.strlen($postString);
        $header[] = "Content-Type: ".$contentType;
        if(!isset($data['access_token'])){
            $header[] = 'Authorization: FALSE';
        }
        else{
            $header[] = 'Authorization: Bearer '.$data['access_token'];
        }

        curl_setopt( $ch, CURLOPT_URL, $url );
        curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, $ssh_mode );
        curl_setopt( $ch, CURLOPT_POST, true );
        curl_setopt( $ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $ch, CURLOPT_POSTFIELDS, $postString );
        $result = curl_exec($ch);
        curl_close($ch);

        if( json_decode($result) && json_last_error() === JSON_ERROR_NONE){
            $result = json_decode($result,true) ;
        }


        return $result;
    }

    public function getPostFieldString($data){
        if(!is_array($data)){
            return $data;
        }

        $data = $this->prepareData($data);
        if(empty($data) || !is_array($data)){
            return "";
        }

        $fields_string = "";
        foreach($data as $key => $value){
            if(empty($value)){
                $data[$key] = "";
            } else{
                $data[$key] = urlencode($value);
            }
            $fields_string .= $key . "=" . $data[$key] . "&";
        }

        $fields_string = rtrim($fields_string,"&");
        return $fields_string;
    }

    public function prepareData($array,$prepend="")
    {
        $results = [];
        foreach ($array as $key => $value) {
            if (is_array($value) && ! empty($value)) {
                if($prepend==""){
                    $results = array_merge($results, $this->prepareData($value, $key));
                } else{
                    $results = array_merge($results, $this->prepareData($value, $prepend."_".urlencode($key)));
                }
            } else {
                if($prepend==""){
                    $results[urlencode($key)] = $value;
                } else{
                    $results[$prepend."_".urlencode($key)] = $value;
                }
            }
        }
        return $results;
    }

    public function parseJson($str,$assoc = true){
        $u1 = strpos($str,"{u'");
        $u2 = strpos($str,"{u\"");
        if($u1==0 && $u1!== false){
            $str = str_replace("\'","\\\\'",$str);
            $str = str_replace('"','\"',$str);
            $str = str_replace("'","\"",$str);
            $str = str_replace('\\\\"',"'",$str);
            $u2 = 0;
        }

        if($u2==0 && $u2!== false){
            $str = str_replace('u"',"\"",$str);
        }
        return json_decode($str,$assoc);
    }

    public function showCurlData($url = null, $params = null, $result = null  )
    {
        echo "<div  style='max-width:80%; margin-left: 280px;max-height:320px;'>";
        echo "<pre style='width:100%;max-height:300px;overflow-y: auto; '>";

        echo "<b>API URL : </b>".$url;

        echo "\n\n<b>POST VALUES: </b>\n";
        echo print_r($params,true);

        echo "\n\n<b>POST VALUE STR: </b>\n";
        echo $this->getPostFieldString($params);

        echo "\n\n<b>RESPONSE: </b>\n";
        var_dump($result);

        echo "</pre>";
        echo "</div>";

        echo "<div style='clear: both'></div>";
        echo '<hr>';
    }

    public function getToken()
    {
        $url = $this->baseURI."oauth/web_portal_access_token.json";

        $data = array();
        $data['grant_type'] = "web_portal";
        $data['client_id'] = "1";
        $data['client_secret'] = "client_secret";
        $data['redirect_uri'] = "redirect_uri";
        $result = $this->processViaCurl($url,$data);
        return $result;
    }

    public function getAccessToken()
    {
        $siteUrl = 'http://plant-tree.com/';
        $url = $siteUrl."oauth/web_portal_access_token.json";

        $data = array();
        $data['grant_type'] = "web_portal";
        $data['client_id'] = "1";
        $data['client_secret'] = "client_secret";
        $data['redirect_uri'] = "redirect_uri";

        $result = $this->processViaCurl($url,$data);
        return $result;
    }

    public function getTokenByRefreshToken( $refresh_token)
    {
        if(!empty($refresh_token)){
            $siteUrl = 'http://plant-tree.com/';
            $url = $siteUrl."oauth/access_token.json";

            $data = array();
            $data['grant_type'] = "refresh_token";
            $data['client_id'] = "1";
            $data['client_secret'] = "client_secret";
            $data['redirect_uri'] = "http://cake4oauth.com"; //redirect_uri
            $data['refresh_token'] = $refresh_token;
            $result = $this->processViaCurl($url,$data);

        }else{
            $result = false;
        }
        return $result;
    }

    public function tokenValidation($token)
    {

        $connection = ConnectionManager::get('default');
        $getDatas = "SELECT oauth_token, expires from oauth_access_tokens WHERE oauth_token = '".$token."';";

        $result = $connection->execute($getDatas)->fetch('assoc');

        if($result && isset($result['expires']) && intval($result['expires']) > strtotime(date('Y-m-d H:i:s'))){
            return true;
        }else{
            $this->updateUserLoginDetails($token, 0);
            return false;
        }
    }

    public function verify()
    {
        $accessToken = $this->controller->getRequest()->getHeader('Authorization');
        if (!empty($accessToken)) {
            $accessToken = explode('Bearer ', $accessToken[0]);

            if(count($accessToken) == 2 && !empty($accessToken[1])) {
                return $this->tokenValidation($accessToken[1]);
            }else{
                return false;
            }
        } else {
            return false;
        }

    }

    public function updateUserLoginDetails($token=null, $status=0)
    {
        if(!empty($token)){
            $userLoginDetails =  TableRegistry::getTableLocator()->get('UserLoginDetails');
            $udOldData = $userLoginDetails->find()
                ->where([
                    'access_token' => trim($token)
                ])->first();

            if($udOldData && count($udOldData->toArray()) > 0){
                $udOldData['login_status'] = $status;
                $userLoginDetails->save($udOldData);
            }
        }
        return true;
    }

    public function updateUserLoginDetailsForGradPos($token=null, $university_id, $status=0)
    {
        if(!empty($token)){

            $conn = ConnectionManager::get('default');
            $prefix = $this->getComponent('CommonFunction')->getDatabaseTablePrefix('user_login_details',$university_id);

            $sql = "SELECT * FROM " . $prefix . "user_login_details where access_token='" . $token ."'";

            $result = $conn->execute($sql)->fetchAll('assoc');
            if (!empty($result)) {
                $id = $result[0]['id'];
                $query = "UPDATE " . $prefix . "user_login_details SET login_status=". $status ." WHERE id=" . $id;
                $conn->query($query);
            }
        }
        return true;
    }

}
