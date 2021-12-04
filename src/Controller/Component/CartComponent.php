<?php

namespace App\Controller\Component;

use Cake\Event\Event;
use Cake\ORM\TableRegistry;

class CartComponent extends BaseComponent
{

    var $controller;
    var $Session;

    function startup(Event $event)
    {
        $this->controller = $this->_registry->getController();
        $this->Session = $this->controller->getRequest()->getSession();

//        $this->Users = $this->getDbTable('ManageUser.Users');
//        $this->Roles =  $this->getDbTable('ManageUser.Roles');
//        $this->Categories = $this->getDbTable('Categories');
        $this->Products = TableRegistry::getTableLocator()->get('Products');
        $this->ProductDeliveryAddress = TableRegistry::getTableLocator()->get('ProductDeliveryAddress');
    }

    function getCurrentAddToCartProductInfo ($data) {

        $product_id = $data['product']['product_id'];

        $productData = $this->Products->find()->where(['products.id' => $product_id])->first();

        $productInfo = [];
        if (!empty($productData)) {
//            $productInfo['id'] = $productData['id'];
        } else {
            $_SESSION['Message']['flash']['message']    =   'No Product Found.';
        }

        if(empty($data['product']['product_quantity']) || $data['product']['product_quantity'] <= 0){
            $quantity = 1;
        }else{
            $quantity = $data['product']['product_quantity'];
        }

        $productInfo['id'] = $productData['id'];
        $productInfo['quantity'] = $quantity;
        $productInfo['name'] = $productData['title'];
        $productInfo['slug'] = $productData['slug'];
        $productInfo['product_image'] = '';
        $productInfo['price'] = intval($productData['price']);
        $productInfo['final_price'] = (intval($productData['price']) * $quantity);

        if (!empty($data['product']['product_delivery_address_id'])) {
            $ProductDeliveryAddress = $this->ProductDeliveryAddress->find()->where(['id' => $data['product']['product_delivery_address_id']])->first();
            if (!empty($ProductDeliveryAddress)) {
                $productInfo['product_delivery_address_id'] = $ProductDeliveryAddress->id;
                $productInfo['product_delivery_address'] = json_decode($ProductDeliveryAddress);
            }
        }

//        dd($productInfo);
        return $productInfo;
    }

    public function calculatePrice($cartData , $newAddToCartData) {

        $productData = $this->Products->find()->where(['products.id' => $newAddToCartData['id']])->first();
        if (!empty($productData)) {

            $cartFinalPriceQuantity = [];
            if(empty($cartData->quantity) || $cartData->quantity > 0){
                $cartFinalPriceQuantity['quantity'] = (intval($cartData->quantity) + intval($newAddToCartData['quantity']));
            }

            $cartFinalPriceQuantity['final_price'] = (intval($productData['price']) *$cartFinalPriceQuantity['quantity']);
            return $cartFinalPriceQuantity;
        } else {
            return false;
        }
    }

}
