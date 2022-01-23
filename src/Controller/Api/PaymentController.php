<?php

namespace App\Controller\Api;

use App\Utility\SSLCommerz\SslCommerzNotification;
use Cake\Event\EventInterface;
use App\Controller\AppController;

class PaymentController extends AppController
{

    public $default_components = ['AccessToken', 'Product'];
    public $mode;

    public function initialize(): void
    {
        parent::initialize();
//        $database       = ConnectionManager::getConfig('default')['database'];
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
        $this->OrderSessions = $this->getDbTable('OrderSessions');
        $this->SslCommerzOrderSessions = $this->getDbTable('SslCommerzOrderSessions');

        $this->mode = $this->Common->getLocalServerDeviceMode();
    }

    public function beforeFilter(EventInterface $event)
    {
//        $this->log($this->request->getParam('action'));
        parent::beforeFilter($event);
        //$this->getEventManager()->off($this->Csrf);
        $this->Session= $this->getRequest()->getSession();
        $this->Auth->allow([
            'payViaAjax', 'success', 'fail', 'cancel'
        ]);
        $actions =  array(
            'payViaAjax', 'success', 'fail', 'cancel'
        );
        $this->Security->setConfig('unlockedActions', $actions);
    }

    public function payViaAjax() {

        if ($this->AccessToken->verify()) {

            if ($this->request->is('post')) {

                $getUser = $this->getComponent('CommonFunction')->getUserInfo();

                $getOrderSession = $this->OrderSessions->find()->where(['user_id' => $getUser['id'], 'order_status' => 0])->first();

                if (empty($getOrderSession)) {
                    $sessionOrder = $this->json_decode($getOrderSession['session_order'], true);

                    $uniqTranId = 'tree-' . uniqid();
                    $post_data = array();
                    $post_data['total_amount'] = number_format($sessionOrder['info']['total'], 2); # You cant not pay less than 10
//                    $post_data['total_amount'] = "10"; # You cant not pay less than 10
                    $post_data['currency'] = "BDT";
                    $post_data['tran_id'] = $uniqTranId; // tran_id must be unique

                    # CUSTOMER INFORMATION
                    $post_data['cus_name'] = $getUser['display_name'] ?? '';
                    $post_data['cus_email'] = $getUser['email'] ?? '';
                    $post_data['cus_add1'] = $getUser['address'] ?? '';
//                    $post_data['cus_name'] = 'test';
//                    $post_data['cus_email'] = 'test@gmail.com';
//                    $post_data['cus_add1'] = 'test';
                    $post_data['cus_add2'] = "";
                    $post_data['cus_city'] = "";
                    $post_data['cus_state'] = "";
                    $post_data['cus_postcode'] = "";
                    $post_data['cus_country'] = "Bangladesh";
                    $post_data['cus_phone'] = $getUser['phone_no'] ?? '';
//                    $post_data['cus_phone'] = '01711111111';
                    $post_data['cus_fax'] = "";

                    # SHIPMENT INFORMATION
                    $post_data['ship_name'] = $getUser['display_name'] ?? '';
                    $post_data['ship_add1'] = $getUser['address'] ?? '';
//                    $post_data['ship_name'] = '';
//                    $post_data['ship_add1'] = '';
//                $post_data['ship_add2'] = "Dhaka";
                    $post_data['ship_city'] = "";
//                $post_data['ship_state'] = "Dhaka";
//                $post_data['ship_postcode'] = "1000";
//                $post_data['ship_phone'] = "";
//                $post_data['ship_country'] = "Bangladesh";
                    $post_data['shipping_method'] = "NO";
                    $post_data['product_name'] = "Trees";
                    $post_data['product_category'] = "Goods";
                    $post_data['product_profile'] = "physical-goods";

//                # OPTIONAL PARAMETERS
//                $post_data['value_a'] = "ref001";
//                $post_data['value_b'] = "ref002";
//                $post_data['value_c'] = "ref003";
//                $post_data['value_d'] = "ref004";

//                dd($post_data);

                    $sslCommerzOrderSessions = $this->SslCommerzOrderSessions->newEmptyEntity();
                    $orderSession['order_session_id'] = $getOrderSession['id'];
                    $orderSession['tran_id'] = $uniqTranId;
                    $sslCommerzOrderSessions = $this->SslCommerzOrderSessions->patchEntity($sslCommerzOrderSessions, $orderSession);
                    $sslCommerzOrderSessions = $this->SslCommerzOrderSessions->save($sslCommerzOrderSessions);

                    if (!empty($sslCommerzOrderSessions)) {
                        $sslcz = new SslCommerzNotification();
                        $response = $sslcz->makePayment($post_data, 'checkout', 'json'); // In your controller's action when saving failed

                        $payment_options = (array)json_decode($response);

                        echo "<meta http-equiv='refresh' content='0;url=".$payment_options['data']."'>";
                        exit();
                    } else {
                        return $this->getResponse()
                            ->withStatus(200)
                            ->withType('application/json')
                            ->withStringBody(json_encode(array(
                                'status' => 'error',
                                'msg' => 'session data not saved',
                                'mode' => $this->mode)));
                    }

                } else {

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

    public function success() {
//        echo "Transaction is Successful";

        $requestData = $this->request->getData();
//        dd($requestData);
        $tran_id = $requestData['tran_id'];

        $grand_total = $requestData['amount'];
        $currency = $requestData['currency'];
        $order_status = 'Pending';

        if ($order_status == 'Pending') {
            $sslcz = new SslCommerzNotification();
            $validation = $sslcz->orderValidate($tran_id, $grand_total, $currency, $requestData);

            if ($validation == TRUE) {

                $sslCommerzOrderSessions = $this->SslCommerzOrderSessions->find()->where(['tran_id' => $tran_id])->first();
                if (!empty($sslCommerzOrderSessions)) {

                    $orderController = new OrderController();
                    $status = $orderController->processOrder($tran_id, $requestData);

                    if ($status) {
                        $paymentData['payment_session'] = $this->json_encode($requestData, true);
                        $sslCommerzOrderSessions = $this->SslCommerzOrderSessions->patchEntity($sslCommerzOrderSessions, $paymentData);
                        if ($this->SslCommerzOrderSessions->save($sslCommerzOrderSessions)) {
                            echo "<br >Transaction is successfully Completed.";
                        }
                    }
                }

//                echo "<br >Transaction is successfully Completed.";
                $this->set('success', $requestData);
                $this->render('success');
            } else {

                echo "validation Fail";
            }


        } else if ($order_status == 'Processing' || $order_status == 'Complete') {

            echo "Transaction is successfully Complete";
        } else {
            echo "Invalid Transaction";
        }
        die();
    }

    public function fail() {
        $requestData = $this->request->getData();
//        dd($requestData);
        $tran_id = $requestData['tran_id'];

        $order_status = 'Pending';
        if ($order_status == 'Pending') {
//            echo "Transaction is Falied";

            $this->render('failed');
        } else if ($order_status == 'Processing' || $order_status == 'Complete') {
            echo "Transaction is already Successful";
        } else {
            echo "Transaction is Invalid";
        }
        die();

    }

    public function cancel() {
        $requestData = $this->request->getData();
        $tran_id = $requestData['tran_id'];

        $order_status = 'Pending';
        if ($order_status == 'Pending') {
            echo "Transaction is Cancel";
        } else if ($order_status == 'Processing' || $order_status == 'Complete') {
            echo "Transaction is already Successful";
        } else {
            echo "Transaction is Invalid";
        }

        die();
    }

    public function ipn() {
        $requestData = $this->request->getData();
        $tran_id = isset($requestData['tran_id']) ? $requestData['tran_id'] : null;

        if ($tran_id) {

            $order_status = 'Pending';
            $grand_total = $requestData['amount'];
            $currency = $requestData['currency'];

            if ($order_status == 'Pending') {
                echo "Pending status";

                $sslcz = new SslCommerzNotification();
                $validation = $sslcz->orderValidate($tran_id, $grand_total, $currency, $requestData);

                if ($validation == TRUE) {
                    /*
                    That means IPN worked. Here you need to update order status
                    in order table as Processing or Complete.
                    Here you can also sent sms or email for successfull transaction to customer
                    */
                    #Update Query Start
                    #Update order status as Processing or Complete in order tabel against the transaction id or order id.
                    #Update Query End
                    echo "Transaction is successfully Complete";
                } else {
                    /*
                    That means IPN worked, but Transation validation failed.
                    Here you need to update order status as Failed in order table.
                    */
                    #Update Query Start
                    #Update order status as Falied in order tabel against the transaction id or order id.
                    #Update Query End
                    echo "validation Fail";
                }

            } else if ($order_status == 'Processing' || $order_status == 'Complete') {

                #That means Order status already updated. No need to udate database.

                echo "Transaction is already successfully Complete";
            } else {
                #That means something wrong happened. You can redirect customer to your product page.

                echo "Invalid Transaction";
            }
        } else {
            echo "Inavalid Data";
        }
        die();
    }


}
