<?php

namespace App\Controller\Admin;

use App\Controller\AppController;
use Cake\Datasource\ConnectionManager;
use Cake\Event\EventInterface;

class OrdersController extends AppController
{
    public function initialize(): void
    {
        parent::initialize();
        $this->Auth->allow(['login', 'logout', 'checkEmail', 'resetPassword']);
        $this->Security->setConfig('unlockedActions', ['add', 'checkEmail', 'edit', 'uploadImage', 'view']);
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

        $orders = $this->Orders->find('all')->where()->orderDesc('id')->toArray();

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
        } else {

        }
    }

}
