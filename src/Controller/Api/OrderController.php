<?php

namespace App\Controller\Api;

use Cake\Datasource\ConnectionManager;
use Cake\Event\EventInterface;
use Cake\Routing\Router;
use Cake\Utility\Security;
use ManageUser\Controller\AppController;

class OrderController extends AppController
{

    public $default_components = ['AccessToken', 'Product'];
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
        $this->ProductDeliveryAddress = $this->getDbTable('ProductDeliveryAddress');
        $this->FavouritesProduct = $this->getDbTable('FavouritesProduct');
        $this->ProductRecentlyView = $this->getDbTable('ProductRecentlyView');
        $this->ProductDeliveryAddress = $this->getDbTable('ProductDeliveryAddress');
        $this->OrderProductAddress = $this->getDbTable('OrderProductAddress');
        $this->OrderSessions = $this->getDbTable('OrderSessions');
        $this->SslCommerzOrderSessions = $this->getDbTable('SslCommerzOrderSessions');
        $this->Orders = $this->getDbTable('Orders');
        $this->OrderProducts = $this->getDbTable('OrderProducts');

        $this->mode = $this->Common->getLocalServerDeviceMode();
    }

    public function beforeFilter(EventInterface $event)
    {
        $this->log($this->request->getParam('action'));
        parent::beforeFilter($event);
        //$this->getEventManager()->off($this->Csrf);
        $this->Session= $this->getRequest()->getSession();
        $this->Auth->allow([
            'index', 'processOrder', 'orderHistory', 'getOrder', 'orderCancel', 'saveOrderNote'
        ]);
        $actions =  array(
            'index', 'processOrder', 'orderHistory', 'getOrder', 'orderCancel', 'saveOrderNote'
        );
        $this->Security->setConfig('unlockedActions', $actions);
    }

    public function saveOrderNote() {

        if ($this->AccessToken->verify()) {

            if ($this->request->is('post')) {

                $request_data = file_get_contents("php://input");
                $request_data = $this->json_decode($request_data, true);
                $this->log($request_data);

                if (!empty($request_data)) {
                    $user_id = isset($request_data['Order']['user_id']) ? $request_data['Order']['user_id']:'';
                    $order_note = isset($request_data['Order']['order_note']) ? $request_data['Order']['order_note']:'';
                    $order_id = isset($request_data['Order']['order_id']) ? $request_data['Order']['order_id']:'';

                    $errorMessage = [];
                    if (empty($user_id)) {
                        $errorMessage[] = ['Required field user id is missing'];
                    }
                    if (empty($order_note)) {
                        $errorMessage[] = ['Required field order note is missing'];
                    }
                    if (empty($order_id)) {
                        $errorMessage[] = ['Required field order id is missing'];
                    }

                    if (count($errorMessage) == 0) {

                        $getUser = $this->getComponent('CommonFunction')->getUserInfo();

                        $getOrders = $this->Orders->find()->select(['id', 'order_id', 'order_stage', 'user_id', 'date_purchased', 'order_sub_total', 'order_total', 'order_note'])->where(['user_id' => $getUser['id'], 'order_id' => $order_id])->first();

                        $orderStatus['order_note'] = $order_note;
                        $getOrders = $this->Orders->patchEntity($getOrders,$orderStatus);
                        $getOrders = $this->Orders->save($getOrders);

                        if (!empty($getOrders)) {
                            return $this->getResponse()
                                ->withStatus(200)
                                ->withType('application/json')
                                ->withStringBody(json_encode(array(
                                    'status' => 'success',
                                    'order' => $getOrders,
                                    'mode' => $this->mode)));
                        } else {
                            return $this->getResponse()
                                ->withStatus(200)
                                ->withType('application/json')
                                ->withStringBody(json_encode(array(
                                    'status' => 'error',
                                    'order' => array(),
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
                            'msg' => 'Missing data!',
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

    public function orderCancel() {

        if ($this->AccessToken->verify()) {

            if ($this->request->is('post')) {

                $request_data = file_get_contents("php://input");
                $request_data = $this->json_decode($request_data, true);
                $this->log($request_data);

                if (!empty($request_data)) {
                    $user_id = isset($request_data['Order']['user_id']) ? $request_data['Order']['user_id']:'';
//                    $order_status = isset($request_data['Order']['order_status']) ? $request_data['Order']['order_status']:'';
                    $order_id = isset($request_data['Order']['order_id']) ? $request_data['Order']['order_id']:'';

                    $errorMessage = [];
                    if (empty($user_id)) {
                        $errorMessage[] = ['Required field user id is missing'];
                    }
//                    if (empty($order_status)) {
//                        $errorMessage[] = ['Required field order status is missing'];
//                    }
                    if (empty($order_id)) {
                        $errorMessage[] = ['Required field order id is missing'];
                    }

                    if (count($errorMessage) == 0) {

                        $getUser = $this->getComponent('CommonFunction')->getUserInfo();

                        $getOrders = $this->Orders->find()->select(['id', 'order_id', 'order_stage', 'user_id', 'date_purchased', 'order_sub_total', 'order_total', 'order_note'])->where(['user_id' => $getUser['id'], 'order_id' => $order_id])->first();

                        $orderStatus['order_stage'] = 'Cancel';
                        $getOrders = $this->Orders->patchEntity($getOrders,$orderStatus);
                        $getOrders = $this->Orders->save($getOrders);

                        if (!empty($getOrders)) {
                            return $this->getResponse()
                                ->withStatus(200)
                                ->withType('application/json')
                                ->withStringBody(json_encode(array(
                                    'status' => 'success',
                                    'order' => $getOrders,
                                    'mode' => $this->mode)));
                        } else {
                            return $this->getResponse()
                                ->withStatus(200)
                                ->withType('application/json')
                                ->withStringBody(json_encode(array(
                                    'status' => 'error',
                                    'order' => array(),
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
                            'msg' => 'Missing data!',
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

    public function orderHistory() {

        if ($this->AccessToken->verify()) {

            if ($this->request->is('post')) {

                $request_data = $this->request->getQueryParams();

                if (!empty($request_data)) {
                    $user_id = isset($request_data['user_id']) ? $request_data['user_id']:'';

                    $getUser = $this->getComponent('CommonFunction')->getUserInfo();

                    $getOrders = $this->Orders->find()->select(['id', 'order_id', 'order_stage','user_id', 'date_purchased', 'order_total', 'order_note'])->where(['user_id' => $getUser['id']])->toArray();

                    if (!empty($getOrders)) {
                        return $this->getResponse()
                            ->withStatus(200)
                            ->withType('application/json')
                            ->withStringBody(json_encode(array(
                                'status' => 'success',
                                'orders' => $getOrders,
                                'mode' => $this->mode)));
                    } else {
                        return $this->getResponse()
                            ->withStatus(200)
                            ->withType('application/json')
                            ->withStringBody(json_encode(array(
                                'status' => 'error',
                                'orders' => array(),
                                'mode' => $this->mode)));
                    }

                } else {
                    return $this->getResponse()
                        ->withStatus(200)
                        ->withType('application/json')
                        ->withStringBody(json_encode(array(
                            'status' => 'error',
                            'msg' => 'Missing data!',
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

    public function getOrder() {

        if ($this->AccessToken->verify()) {

            if ($this->request->is('post')) {

                $request_data = $this->request->getQueryParams();

                if (!empty($request_data)) {
                    $user_id = isset($request_data['user_id']) ? $request_data['user_id']:'';
                    $order_id = isset($request_data['order_id']) ? $request_data['order_id']:'';

                    $getUser = $this->getComponent('CommonFunction')->getUserInfo();

                    $getOrders = $this->Orders->find()->select(['id', 'order_id', 'order_stage','user_id', 'date_purchased', 'order_sub_total', 'order_total', 'order_note'])->where(['user_id' => $getUser['id'], 'order_id' => $order_id])->first();

                    if (!empty($getOrders)) {
                        return $this->getResponse()
                            ->withStatus(200)
                            ->withType('application/json')
                            ->withStringBody(json_encode(array(
                                'status' => 'success',
                                'order' => $getOrders,
                                'mode' => $this->mode)));
                    } else {
                        return $this->getResponse()
                            ->withStatus(200)
                            ->withType('application/json')
                            ->withStringBody(json_encode(array(
                                'status' => 'error',
                                'order' => array(),
                                'mode' => $this->mode)));
                    }

                } else {
                    return $this->getResponse()
                        ->withStatus(200)
                        ->withType('application/json')
                        ->withStringBody(json_encode(array(
                            'status' => 'error',
                            'msg' => 'Missing data!',
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

    public function processOrder($tran_id, $requestData) {

        if (!empty($tran_id)) {

            try {

                $sslCommerzOrderSessions = $this->SslCommerzOrderSessions->find()->where(['tran_id' => $tran_id])->first();

                if (!empty($sslCommerzOrderSessions)) {
                    $orderSessions = $this->OrderSessions->find()->where(['id' => $sslCommerzOrderSessions['order_session_id']])->first();

                    if (!empty($orderSessions)) {
                        $orderSessionsJsonDecode = $this->json_decode($orderSessions['session_order'], true);

                        $user_id = $orderSessionsJsonDecode['user_info']['id'];
                        $setOrderData = $this->setOrder($orderSessionsJsonDecode, $requestData);

                        $order = $this->saveOrderData($setOrderData);

                        $orderProducts = $this->setOrderProduct($orderSessionsJsonDecode['products'], $order['id']);

                        $success = false;
                        foreach ($orderProducts['products'] as $key => $products) {

                            $order_product_id = $this->saveOrderProductData($products);
                            if (!empty($order_product_id)) {
                                $setProductDeliveryAddress = $this->setOrderProductAddress($orderProducts['product_delivery_address'][$key], $order_product_id, $user_id);

                                if (!empty($setProductDeliveryAddress)) {

                                    $this->saveOrderProductAddress($setProductDeliveryAddress);
                                }
                            }

                            $success = true;
                        }

                        if ($success == true) {
                            $orderStatus['is_sync'] = 1;
                            $orderStatus['order_status'] = 1;
                            $orderSessions = $this->OrderSessions->patchEntity($orderSessions, $orderStatus);
                            if ($this->OrderSessions->save($orderSessions)) {

                                $this->getComponent('EmailHandler')->OrderCreateEmail($order);
                                return $order['id'];
                            }else {
                                return false;
                            }
                        } else {
                            return false;
                        }

                    }
                }
            } catch (\Exception $exception) {
                $this->saveLog('sslPayment', 'order_create', $exception->getMessage());
            }
        }

    }

    public function setOrder($orders, $requestData) {

        if (!empty($orders)) {

            $parseData['user_id'] = $orders['user_info']['id'];
            $parseData['order_id'] = substr(uniqid(),0,8);
            $parseData['date_purchased'] = $orders['info']['date_purchased'];
            $parseData['order_status'] = 'Success';
            $parseData['order_stage'] = 'Processing';
            $parseData['order_sub_total'] = $orders['info']['subtotal'];
            $parseData['order_total'] = $orders['info']['total'];
            $parseData['customer_name'] = $orders['user_info']['display_name'];
            $parseData['customer_email'] = $orders['user_info']['email'];
            $parseData['customer_phone'] = $orders['user_info']['phone_no'];
            $parseData['customer_address_line1'] = $orders['user_info']['address'];
            $parseData['card_type'] = $requestData['card_type'];
            $parseData['tran_id'] = $requestData['tran_id'];
            $parseData['payment_status'] = $requestData['status'];
            $parseData['tran_date'] = $requestData['tran_date'];
            $parseData['is_sync'] = 1;
            $parseData['is_email_sent'] = 0;

            return $parseData;
        } else {
            return false;
        }

    }

    public function saveOrderData($ordersData) {

        if (!empty($ordersData)) {
            $Orders = $this->Orders->newEmptyEntity();
            $Orders = $this->Orders->patchEntity($Orders, $ordersData);
            $Orders = $this->Orders->save($Orders);
            if ($Orders) {
                return $Orders;
            } else {
                return false;
            }

        } else {
            return false;
        }

    }

    public function setOrderProduct($products, $orderId) {

        if (!empty($products) && !empty($orderId)) {

            $orderProducts = array();
            foreach ($products as $key => $product) {

                $orderProduct['order_id'] = $orderId;
                $orderProduct['product_id'] = $product['id'];
                $orderProduct['product_slug'] = $product['slug'];
                $orderProduct['product_category_id'] = $product['category_id'];
                $orderProduct['product_category_name'] = $product['category_name'];
                $orderProduct['product_name'] = $product['name'];
                $orderProduct['product_price'] = $product['price'];
                $orderProduct['product_final_price'] = $product['final_price'];
                $orderProduct['product_quantity'] = $product['quantity'];
                $orderProduct['product_status'] = 'success';
                $orderProduct['is_sync'] = 1;

                $orderProducts['products'][$key] = $orderProduct;
                $productDeliveryAddress['product_delivery_address_id'] = $product['product_delivery_address_id'];
                $productDeliveryAddress['address'] = $product['product_delivery_address']['address_line'];
                $orderProducts['product_delivery_address'][$key] = $productDeliveryAddress;

            }

            return $orderProducts;

        } else {
            return false;
        }

    }

    public function saveOrderProductData($orderProductData) {

        if (!empty($orderProductData)) {
            $orderProduct = $this->OrderProducts->newEmptyEntity();
            $orderProduct = $this->OrderProducts->patchEntity($orderProduct, $orderProductData);
            $orderProduct = $this->OrderProducts->save($orderProduct);
            if (!empty($orderProduct)) {
                return $orderProduct['id'];
            } else {
                return false;
            }
        } else {
            return false;
        }

    }

    public function setOrderProductAddress($deliveryAddress, $orderProductId, $userId) {

        if (!empty($deliveryAddress)) {

            $address['order_product_id'] = $orderProductId;
            $address['user_id'] = $userId;
            $address['product_delivery_address_id'] = $deliveryAddress['product_delivery_address_id'];
            $address['address'] = $deliveryAddress['address'];

            return $address;
        } else {
            return false;
        }
    }

    public function saveOrderProductAddress($productAddressData) {

        if (!empty($productAddressData)) {
            $orderProductAddress = $this->OrderProductAddress->newEmptyEntity();
            $orderProductAddress = $this->OrderProductAddress->patchEntity($orderProductAddress, $productAddressData);
            $orderProductAddress = $this->OrderProductAddress->save($orderProductAddress);
            if (!empty($orderProductAddress)) {
                return $orderProductAddress['id'];
            } else {
                return false;
            }
        } else {
            return false;
        }

    }

}
