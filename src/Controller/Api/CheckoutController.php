<?php

namespace App\Controller\Api;

use App\Controller\AppController;
use Cake\Datasource\ConnectionManager;
use Cake\Event\EventInterface;

class CheckoutController extends AppController
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
        $this->OrderSessions = $this->getDbTable('OrderSessions');

        $this->mode = $this->Common->getLocalServerDeviceMode();
    }

    public function beforeFilter(EventInterface $event)
    {
        $this->log($this->request->getParam('action'));
        parent::beforeFilter($event);
        //$this->getEventManager()->off($this->Csrf);
        $this->Session= $this->getRequest()->getSession();
        $this->Auth->allow([
            'index'
        ]);
        $actions =  array(
            'index'
        );
        $this->Security->setConfig('unlockedActions', $actions);
    }

    public function index () {

        if ($this->AccessToken->verify()) {


            if ($this->request->is('get')) {
                $request_data = $this->request->getQueryParams();

                if (!empty($request_data)) {
                    $user_id = isset($request_data['user_id']) ? $request_data['user_id'] : '';

                    $getUser = $this->getComponent('CommonFunction')->getUserInfo();

                    $getOrderSession = $this->OrderSessions->find()->where(['user_id' => $getUser['id'], 'order_status' => 0])->first();

                    if (!empty($getOrderSession)) {
                        return $this->getResponse()
                            ->withStatus(200)
                            ->withType('application/json')
                            ->withStringBody(json_encode(array(
                                'status' => 'success',
                                'order_session' => json_decode($getOrderSession['session_order']),
                                'mode' => $this->mode)));
                    } else {
                        return $this->getResponse()
                            ->withStatus(200)
                            ->withType('application/json')
                            ->withStringBody(json_encode(array(
                                'status' => 'success',
                                'msg' => 'Cart Empty!',
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
