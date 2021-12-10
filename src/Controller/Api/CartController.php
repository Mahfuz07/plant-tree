<?php

namespace App\Controller\Api;

use App\Controller\AppController;
use Cake\Datasource\ConnectionManager;
use Cake\Event\EventInterface;

class CartController extends AppController
{
    public $default_components = ['AccessToken', 'Cart'];
    public $mode;

    public function initialize(): void
    {
        parent::initialize();
        $database       = ConnectionManager::getConfig('default')['database'];
        $this->loadComponent('RequestHandler');
        $this->loadComponent('Common');
        $this->loadComponent('Cart');

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
            'index', 'add', 'change', 'remove'
        ]);
        $actions =  array(
            'index', 'add', 'change', 'remove'
        );
        $this->Security->setConfig('unlockedActions', $actions);
    }

    public function index() {

    }

    public function add () {

        if ($this->AccessToken->verify()) {

            if ($this->request->is('post')) {
                $request_data = file_get_contents("php://input");
                $request_data = $this->json_decode($request_data, true);

                if (!empty($request_data)) {
                    $getUser = $this->getComponent('CommonFunction')->getUserInfo();

                    $current_product_info = $this->getComponent('Cart')->getCurrentAddToCartProductInfo($request_data);

                    $setCart = $this->cartSet($current_product_info, $getUser, 'products');

                    if (!empty($setCart)) {
                        return $this->getResponse()
                            ->withStatus(200)
                            ->withType('application/json')
                            ->withStringBody(json_encode(array(
                                'status' => 'success',
                                'products' => $setCart,
                                'mode' => $this->mode)));
                    } else {
                        return $this->getResponse()
                            ->withStatus(200)
                            ->withType('application/json')
                            ->withStringBody(json_encode(array(
                                'status' => 'error',
                                'msg' => 'Invalid Data.',
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

    public function change () {

        if ($this->AccessToken->verify()) {

            if ($this->request->is('post')) {
                $request_data = file_get_contents("php://input");
                $request_data = $this->json_decode($request_data, true);

                if (!empty($request_data)) {

                    $product_id = isset($request_data['Product']['product_id']) ? $request_data['Product']['product_id']:'';
                    $product_slug = isset($request_data['Product']['product_slug']) ? $request_data['Product']['product_slug']:'';
                    $user_id = isset($request_data['Product']['user_id']) ? $request_data['Product']['user_id']:'';

                    $getUser = $this->getComponent('CommonFunction')->getUserInfo();

                    $getOrderSession = $this->OrderSessions->find()->where(['user_id' => $getUser['id'], 'order_status' => 0])->first();

                    if (!empty($getOrderSession)) {

                        $getProducts = json_decode($getOrderSession['session_order']);
                        foreach ($getProducts->products as $key=>$orderSession) {
                            if ($orderSession->id == $product_id && $orderSession->slug == $product_slug) {

                                if ($orderSession->quantity == 1) {
                                    return $this->getResponse()
                                        ->withStatus(200)
                                        ->withType('application/json')
                                        ->withStringBody(json_encode(array(
                                            'status' => 'success',
                                            'msg' => 'Already Quantity 1',
                                            'mode' => $this->mode)));
                                }
                                $orderSession->quantity = ($orderSession->quantity - 1);
                                $orderSession->final_price = ($orderSession->price * $orderSession->quantity);
//                                $getProducts->products[$key] = $orderSession;
                                $orderInfo = $this->orderInfo(json_encode($getProducts->products));
                                $getProducts->info = $orderInfo;
                                $updateOrderSession['session_order'] = json_encode($getProducts);
                                $getOrderSession = $this->OrderSessions->patchEntity($getOrderSession, $updateOrderSession);
                                $getOrderSession = $this->OrderSessions->save($getOrderSession);
                                if (!empty($getOrderSession)) {
                                    return $this->getResponse()
                                        ->withStatus(200)
                                        ->withType('application/json')
                                        ->withStringBody(json_encode(array(
                                            'status' => 'success',
                                            'msg' => true,
                                            'mode' => $this->mode)));
                                }
                            }
                        }
                    }

                    if (!empty($setCart)) {
                        return $this->getResponse()
                            ->withStatus(200)
                            ->withType('application/json')
                            ->withStringBody(json_encode(array(
                                'status' => 'success',
                                'products' => $setCart,
                                'mode' => $this->mode)));
                    } else {
                        return $this->getResponse()
                            ->withStatus(200)
                            ->withType('application/json')
                            ->withStringBody(json_encode(array(
                                'status' => 'error',
                                'msg' => 'Invalid Data.',
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

    public function remove() {

    }

    public function cartSet($data = array(), $userInfo, $type) {

        $getOrderSession = $this->OrderSessions->find()->where(['user_id' => $userInfo['id'], 'order_status' => 0])->first();

        if (!empty($data)) {
            if (!empty($getOrderSession)) {
                if ($type == 'products') {
                    $index = '';
                    $cartProductNotExit = 0;
                    $getProducts = json_decode($getOrderSession['session_order']);
                    foreach ($getProducts->products as $key=>$orderSession) {
                        if ($orderSession->slug == $data['slug']) {
                            $calcutePrice = $this->getComponent('Cart')->calculatePrice($orderSession, $data);
                            $orderSession->quantity = $calcutePrice['quantity'];
                            $orderSession->final_price = $calcutePrice['final_price'];
                            $index = $key;
                            $getProducts->products[$key] = $orderSession;
                            $orderInfo = $this->orderInfo(json_encode($getProducts->products));
                            $getProducts->info = $orderInfo;
                            $updateOrderSession['session_order'] = json_encode($getProducts);
                            $getOrderSession = $this->OrderSessions->patchEntity($getOrderSession, $updateOrderSession);
                            $getOrderSession = $this->OrderSessions->save($getOrderSession);
                            if (!empty($getOrderSession)) {
                                return $getOrderSession;
                            }
                        } else {
                            $cartProductNotExit = 1;
                        }
                    }

                    if ($cartProductNotExit) {
                        $getProducts->products[count($getProducts->products)] = $data;
                        $orderInfo = $this->orderInfo(json_encode($getProducts->products));
                        $getProducts->info = $orderInfo;
                        $updateOrderSession['session_order'] = json_encode($getProducts);
                        $getOrderSession = $this->OrderSessions->patchEntity($getOrderSession, $updateOrderSession);
                        $getOrderSession = $this->OrderSessions->save($getOrderSession);
                        if (!empty($getOrderSession)) {
                            return $getOrderSession;
                        }
                    }
                }
            } else {

                if ($type == 'products') {
                    if (!empty($data)) {
                        $cartInfo['products'] = [$data];
                        $cartInfo['user_info'] = $userInfo;
                        $cartInfo['info'] = $this->orderInfo([], $data['final_price']);
                        $orderSessions = $this->OrderSessions->newEmptyEntity();
                        $orderSession['session_order'] = json_encode($cartInfo);
                        $orderSession['session_user_info'] = json_encode($userInfo);
                        $orderSession['user_id'] = $userInfo['id'];
                        $orderSession['order_status'] = 0;
                        $orderSession['is_sync'] = 0;
                        $orderSessions = $this->OrderSessions->patchEntity($orderSessions, $orderSession);
                        $saveOrderSession = $this->OrderSessions->save($orderSessions);
                        if (!empty($saveOrderSession)) {
                            return $saveOrderSession;
                        }
                    } else {
                        return false;
                    }
                } else {
                    return false;
                }
            }
        } else {
            return false;
        }

    }

    public function orderInfo($products = array(), $final_price = null) {

        if (!empty($products)) {

            $products = json_decode($products);
            $total_price = 0;
            foreach ($products as $product) {
                $total_price += intval($product->final_price);
            }

            $info['subtotal'] = $total_price;
            $info['total'] = $total_price;
            $info['date_purchased'] = date('Y-m-d h:i:s');

            return $info;
        }
        if (!empty($final_price)) {
            $info['subtotal'] = $final_price;
            $info['total'] = $final_price;
            $info['date_purchased'] = date('Y-m-d h:i:s');

            return $info;
        }
    }

}
