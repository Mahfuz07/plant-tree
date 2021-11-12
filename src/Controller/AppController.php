<?php
declare(strict_types=1);

/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link      https://cakephp.org CakePHP(tm) Project
 * @since     0.2.9
 * @license   https://opensource.org/licenses/mit-license.php MIT License
 */
namespace App\Controller;

use App\Traits\PolyfillTrait;
use Cake\Controller\Controller;
use Cake\Event\EventInterface;
use Cake\Http\Response;
use Cake\Log\Log;
use Cake\Routing\Router;

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @link https://book.cakephp.org/4/en/controllers.html#the-app-controller
 */
Abstract class AppController extends Controller
{
    use PolyfillTrait;

    /**
     * Initialization hook method.
     *
     * Use this method to add common initialization code like loading components.
     *
     * e.g. `$this->loadComponent('FormProtection');`
     *
     * @return void
     */
    public  $default_components = ['Flash'];

    public function initialize(): void
    {
        $class = get_called_class();
        while ($class = get_parent_class($class)) {
            $parent_vars = get_class_vars($class);
            if(!empty($parent_vars['default_components']) && is_array($parent_vars['default_components'])){
                foreach($parent_vars['default_components'] as $k=>$v){
                    if(is_numeric($k)){
                        if(!isset($this->default_components[$v]) && array_search($v,$this->default_components)===false){
                            $this->default_components[] = $v;
                        }

                    } else {
                        if(!isset($this->default_components[$k]) && array_search($k,$this->default_components)===false){
                            $this->default_components[$k] = $v;
                        }
                    }
                }
            }
        }

        $this->sessionWrite('securepaytest','yes');
        parent::initialize();

        $req = $this->getRequest();
        $req->getController = &$this;
        $this->setRequest($req);

        $this->loadAllComponents($this->default_components ?: []);


        $this->loadComponent('RequestHandler', [
            'enableBeforeRedirect' => false,
        ]);
        $this->loadComponent('CommonFunction');
        $this->loadComponent('Flash');

        $this->loadComponent('Security');

        $AuthConfig = [
            'authorize' => ['Controller'],
            'authenticate' => [
                'Form' => [
                    'userModel' => 'ManageUser.Users',
                    'fields' => [
                        'username' => 'username',
                        'password' => 'password'
                    ],
                    //'finder' => 'ActiveUser',
                    'passwordHasher' => [
                        'className' => 'Fallback',
                        'hashers' => [
                            'Default',
                            'Weak' => ['hashType' => 'sha1']
                        ]
                    ]
                ]

            ],
            'loginAction' => [
                'plugin' => 'ManageUser',
                'controller' => 'Users',
                'action' => 'login',
                'prefix' => 'admin'
            ],
            'loginRedirect' => [
                'plugin' => 'ManageUser',
                'controller' => 'Users',
                'action' => 'index',
                'prefix' => 'admin'
            ],

            'unauthorizedRedirect' => [
                'plugin' => 'ManageUser',
                'controller' => 'Users',
                'action' => 'login',
                'prefix' => 'admin'
            ]
        ];

        $this->loadComponent('Auth', $AuthConfig);
        $this->Auth->allow(['display']);
    }

    public function isAction(string $action): bool
    {
        if(parent::isAction($action)){
            $baseClass = new \ReflectionClass(self::class);
            if( $baseClass->hasMethod($action)
                || ( isset($this->allow_actions) && !in_array($action, $this->allow_actions) )
                || ( isset($this->deny_actions) && in_array($action, $this->deny_actions) )
            ){
                return false;
            }

            return true;
        }
        return false;
    }


    public function isAuthorized($user=null)       // Checking is admin or not
    {
        if ($this->getRequestParam('prefix') == 'admin'){

            $this->Auth->config("loginRedirect","/admin");
            if(empty($user['role_id']) || !in_array((int)$user['role_id'],array(1,8,9))){
                return false;
            }
        }
        return true;
    }

    public function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event);
        $this->set('version_no','1.0');

        if($this->request->is('ajax')){
            $this->viewBuilder()->disableAutoLayout();
            $this->viewBuilder()->setLayout('ajax');
        }
        $params = $this->request->getQueryParams();
        if (isset($params['request-type'])) {
            $request_type = strtolower(trim($params['request-type']));
            if ($request_type == 'ajax') {
                $this->viewBuilder()->disableAutoLayout();
                $this->viewBuilder()->setLayout('ajax');
            }
        }
    }

    public function beforeRender(EventInterface $event)
    {
        if (!array_key_exists('_serialize', $this->viewBuilder()->getVars()) && in_array($this->response->getType(), ['application/json', 'application/xml'])) {
            $this->set('_serialize', true);
        }
    }

    public function saveLog($path="",$file="",$data = array(),$logType = 'debug'){

        $path = trim($path);
        $file = trim($file);

        $pattern = '/[^a-zA-Z0-9_ - \.\/]/i';
        $replacement = '/';

        if(!empty($path)){
            $path = preg_replace($pattern, $replacement, str_replace(" ","_",$path));

        }
        else{
        }
        $path = LOGS .  trim($path,"/") . "/";
        if(empty($file)){
            $file = "log";
        }
        $pattern = '/[^a-zA-Z0-9_-]/i';
        $replacement = '_';
        $file = preg_replace($pattern, $replacement, $file);

        $key = preg_replace($pattern, $replacement, $path.$file);

        if(!is_dir($path)){
            mkdir($path,0755,true);
        }
        try{
            try{

                Log::setConfig($key, [
                    'className' => 'File',
                    'path' => $path,
                    'levels' => [$logType],
                    'file'=>$file
                ]);

            }
            catch (\Exception $e){

            }

            $this->log( $data,$logType,[$key]);;
            $this->log("\n\n",$logType, [$key]);;
        }
        catch (\Exception $e){
        }
    }

    public function redirect($url, int $status = 302, $forceExit = true): ?Response
    {
        $this->disableAutoRender();
        if ($status) {
            $this->response = $this->response->withStatus($status);
        }
        $event = $this->dispatchEvent('Controller.beforeRedirect', [$url, $this->response]);
        if ($event->getResult() instanceof Response) {
            return $this->response = $event->getResult();
        }
        if ($event->isStopped()) {
            return null;
        }
        $response = $this->response;
        if (!$response->getHeaderLine('Location')) {
            $response = $response->withLocation(Router::url($url, true));
        }

        if ($forceExit) {
            $this->response = $response;
            $result = $this->shutdownProcess();
            if ($result instanceof ResponseInterface) {
                $response =  $result;
            }
            $responseEmitter = new \Cake\Http\ResponseEmitter();
            $responseEmitter->emit($response);
            exit(0);
        } else {
            return $this->response = $response;
        }
    }
}
