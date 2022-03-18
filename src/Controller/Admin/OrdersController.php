<?php

namespace App\Controller\Admin;

use App\Controller\AppController;
use Cake\Datasource\ConnectionManager;
use Cake\Event\EventInterface;
use phpDocumentor\Reflection\Types\This;
use PHPMailer\PHPMailer\Exception;

class OrdersController extends AppController
{
    public function initialize(): void
    {
        parent::initialize();
        $this->Auth->allow(['login', 'logout', 'checkEmail', 'resetPassword']);
        $this->Security->setConfig('unlockedActions', ['add', 'checkEmail', 'edit', 'uploadImage', 'view', 'orderEdit', 'orderProductEdit']);
    }

    public function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event);
//        $this->Security->setConfig('unlockedActions', ['add', 'checkEmail', 'edit', 'uploadImage']);

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
    }

    public function index() {

        $all_orders = $this->Orders->find('all')->where()->orderDesc('id')->toArray();
        $cancel_orders = $this->Orders->find('all')->where(['order_stage' => 'Cancel'])->orderDesc('id')->toArray();
        $processing_orders = $this->Orders->find('all')->where(['order_stage' => 'Processing'])->orderDesc('id')->toArray();
        $complete_orders = $this->Orders->find('all')->where(['order_stage' => 'Complete'])->orderDesc('id')->toArray();

        $this->set('processing_orders', $processing_orders);
        $this->set('complete_orders', $complete_orders);
        $this->set('cancel_orders', $cancel_orders);
        $this->set('all_orders', $all_orders);

    }

    public function cancelOrderView() {

        $orders = $this->Orders->find('all')->where(['order_stage' => 'Cancel'])->orderDesc('id')->toArray();

        $this->set('orders', $orders);
    }

    public function view($id) {

        if (!empty($id)) {
            $orders = $this->Orders->find()->where(['id' => $id])->first();

            if (!empty($orders)) {
                $orderProducts = $this->OrderProducts->find()->where(['order_id' => $id])->toArray();

                if (!empty($orderProducts)) {
                    $orderProductAddress = $this->OrderProductAddress->find()->where(['order_product_id in (SELECT id FROM order_products WHERE order_id = "'. $id .'")'])->toArray();

                    $this->set('order_product_address', $orderProductAddress);
                    $this->set('order_product', $orderProducts);
                    $this->set('orders', $orders);
                }
            }
        }

    }

    public function cancelOrder($id) {

        if (!empty($id)) {
            $orders = $this->Orders->find()->where(['id' => $id])->first();

            if (!empty($orders)) {
                $orderParams['order_stage'] = 'Cancel';
                $orders = $this->Orders->patchEntity($orders, $orderParams);
                $orders = $this->Orders->save($orders);
                if ($orders->id) {
                    try {
                        $this->getComponent('EmailHandler')->cancelOrderEmail($orders);
                    } catch (Exception $e) {
                        $this->log($e->getMessage());
                    }
                    $this->Flash->success('Order has been cancelled', ['key'=>'success']);
                    $this->redirect('/admin/orders');
                } else {
                    $this->Flash->success('Oops something wrong!', ['key'=>'success']);
                    $this->redirect('/admin/orders');
                }
            }
        }
    }

    public function completeOrder($id) {

        if (!empty($id)) {
            $orders = $this->Orders->find()->where(['id' => $id])->first();

            if (!empty($orders)) {
                $orderParams['order_stage'] = 'Complete';
                $orders = $this->Orders->patchEntity($orders, $orderParams);
                $orders = $this->Orders->save($orders);
                if ($orders->id) {
                    try {
                        $this->getComponent('EmailHandler')->cancelOrderEmail($orders);
                    } catch (Exception $e) {
                        $this->log($e->getMessage());
                    }
                    $this->Flash->success('Order has been cancelled', ['key'=>'success']);
                    $this->redirect('/admin/orders');
                } else {
                    $this->Flash->success('Oops something wrong!', ['key'=>'success']);
                    $this->redirect('/admin/orders');
                }
            }
        }

    }

    public function orderEdit($id) {
        if (!empty($id)) {
            $orders = $this->Orders->find()->where(['id' => $id])->first();
            if ($this->request->is('put')) {
                $orders = $this->Orders->patchEntity($orders, $this->request->getData());
                if ($this->Orders->save($orders)) {
                    $this->Flash->success('Order data has been save successfully', ['key'=>'success']);
                    $this->redirect('/admin/orders/view/' . $id);
                } else {
                    $this->Flash->success('Oops something wrong', ['key'=>'success']);
                    $this->redirect('/admin/orders/order-edit/' . $id);
                }
            }
            $this->set('order', $orders);
        }
    }

    public function orderProductEdit($id) {
        if (!empty($id)) {
            $orderProducts = $this->OrderProducts->find()->where(['id' => $id])->first();
            $orderProductsAddress = $this->OrderProductAddress->find()->where(['order_product_id' => $orderProducts['id']])->first();
            if ($this->request->is('put')) {
                $orderProducts = $this->OrderProducts->patchEntity($orderProducts, $this->request->getData());
                if ($this->OrderProducts->save($orderProducts)) {
                    $postData['address'] = $this->request->getData()['address'];
                    $orderProductsAddress = $this->OrderProductAddress->patchEntity($orderProductsAddress, $postData);
                    $this->OrderProductAddress->save($orderProductsAddress);
                    $this->Flash->success('Order data has been save successfully', ['key'=>'success']);
                    $this->redirect('/admin/orders/view/' . $orderProducts['order_id']);
                } else {
                    $this->Flash->success('Oops something wrong', ['key'=>'success']);
                    $this->redirect('/admin/orders/order-edit/' . $id);
                }
            }
            $this->set('order_product', $orderProducts);
            $this->set('order_product_address', $orderProductsAddress['address']);
        }
    }

}
