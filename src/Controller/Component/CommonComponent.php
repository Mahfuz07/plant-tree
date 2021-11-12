<?php
namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Controller\ComponentRegistry;
use Cake\Core\Configure;
use Cake\Datasource\ConnectionManager;
use Cake\Log\Log;
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;
use Cake\Core\Configure\Engine\JsonConfig;

//use ReflectionClass;
//use ReflectionMethod;

/**
 * Common component
 */
class CommonComponent extends BaseComponent
{

    /**
     * Default configuration.
     *
     * @var array
     */
    protected $_defaultConfig = [];

    public $table_prefix;

    public function __construct()
    {

        $database       = ConnectionManager::getConfig('default')['database'];

//        $this->Orders = TableRegistry::getTableLocator()->get('Orders');
//        $this->Settings = TableRegistry::getTableLocator()->get('Settings');
    }


    public function setLog($data = null)
    {
        $dataLogTable = TableRegistry::getTableLocator()->get('DataLog');
        $data_log_entities = $dataLogTable->newEmptyEntity();
        $data_log_entities->data = json_encode($data);
        return $dataLogTable->save($data_log_entities);
    }

    public function getLog($limit=1)
    {
        $dataLogTable = TableRegistry::getTableLocator()->get('DataLog');
        $logs = $dataLogTable->find()->order(['id'=>'DESC'])->limit($limit)->toArray();
        $data = array();
        if(count($logs) > 0){
            foreach ($logs as $log){
                $data[] = array(
                    //'time' => $log->created,
                    'data' => json_decode($log->data)
                );
            }
        }
        return $data;
    }

    public function getSettingValue()
    {
        $queryOrderTickets = $this->Settings->find()->where(['setting_key' => 'Site.timezone'])->first();
        if (!empty($queryOrderTickets)) {
            return $queryOrderTickets['setting_value'];
        } else {
           return false;
        }
    }

    public function getLocalServerDeviceMode()
    {
        $deviceModeTable = TableRegistry::getTableLocator()->get('DeviceModes');
        $tableData = $deviceModeTable->find()->where(['id'=>1])->order(['id'=>'DESC'])->first();

        if(isset($tableData) && !empty($tableData)){
            $data = array(
                'type' => $tableData->mode,
                'url' => $tableData->api_url
            );
        }else{
            $data = array(
                'type'=>'live',
                'url' => ''
            );
        }
        return $data;
    }

    private function getControllers() {
        $files = scandir('../src/Controller/');
        $results = [];
        $ignoreList = [
            '.',
            '..',
            'Component',
            'AppController.php',
            'ErrorController.php'
        ];
        foreach($files as $file){
            if(!in_array($file, $ignoreList)) {
                $controller = explode('.', $file)[0];
                array_push($results, str_replace('Controller', '', $controller));
            }
        }
        return $results;
    }

    private function getActions($controllerName) {
        $className = 'App\\Controller\\'.$controllerName.'Controller';
        $class = new ReflectionClass($className);
        $actions = $class->getMethods(ReflectionMethod::IS_PUBLIC);
        $results = array();
        $ignoreList = [
            'beforeFilter',
            'afterFilter',
            'initialize',
            'beforeRender'
        ];
        foreach($actions as $action){
            if($action->class == $className && !in_array($action->name, $ignoreList)){
                //array_push($results[$controllerName], $action->name);
                $results[$controllerName][] = $action->name;
            }
        }
        return $results;
    }

    public function getControllerActions() {
        $controllers = $this->getControllers();
        $resources = [];
        foreach($controllers as $controller){
            $actions = $this->getActions($controller);
            //array_push($resources, $actions);
            $resources[$controller] = $actions[$controller];

        }
        return $resources;
    }

    public function uploadFile(&$data, $upload_location =  WWW_ROOT .'uploads'){

        if(!empty($data['image_path']['name'])) {
            $tempFile = $data['image_path']['tmp_name'];
            $fileName = date('Ymdhis').'_'.$data['image_path']['name'];
            $targetFile = rtrim($upload_location,'/') . DS . $fileName;
            $fileTypes = array('jpg','jpeg','gif','png');
            $fileParts = pathinfo($data['image_path']['name']);

            if (in_array($fileParts['extension'],$fileTypes)) {
                move_uploaded_file($tempFile,$targetFile);
                $data['image_path'] = $fileName;
            }else{
                $data['image_path'] = '';
            }
        }else{
            unset($data['image_path']);
        }
    }

    public function get_ip_address()
    {
        if (isset($_SERVER)) {
            if (isset($_SERVER["HTTP_X_FORWARDED_FOR"]) && ip2long($_SERVER["HTTP_X_FORWARDED_FOR"]) !== false) {
                $ipaddress = $_SERVER["HTTP_X_FORWARDED_FOR"];
            } elseif (isset($_SERVER["HTTP_CLIENT_IP"]) && ip2long($_SERVER["HTTP_CLIENT_IP"]) !== false) {
                $ipaddress = $_SERVER["HTTP_CLIENT_IP"];
            } else {
                $ipaddress = $_SERVER["REMOTE_ADDR"];
            }
        } else {
            if (getenv('HTTP_X_FORWARDED_FOR') && ip2long(getenv('HTTP_X_FORWARDED_FOR')) !== false) {
                $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
            } elseif (getenv('HTTP_CLIENT_IP') && ip2long(getenv('HTTP_CLIENT_IP')) !== false) {
                $ipaddress = getenv('HTTP_CLIENT_IP');
            } else {
                $ipaddress = getenv('REMOTE_ADDR');
            }
        }
        return $ipaddress;
    }

}
