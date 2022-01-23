<?php

namespace App\Controller\Component;

use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\Routing\Router;

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
        $this->Categories = $this->getDbTable('Categories');
        $this->Products = TableRegistry::getTableLocator()->get('Products');
        $this->ProductDeliveryAddress = TableRegistry::getTableLocator()->get('ProductDeliveryAddress');
        $this->ProductImages = $this->getDbTable('ProductImages');
    }

    function getCurrentAddToCartProductInfo ($data) {

        $product_id = $data['Product']['product_id'];

        $productData = $this->Products->find()->where(['id' => $product_id])->first();

        $categories = $this->Categories->find()->where(['id' => $productData['category_id']])->first();

        $productInfo = [];
        if (!empty($productData)) {
//            $productInfo['id'] = $productData['id'];
        } else {
            $_SESSION['Message']['flash']['message']    =   'No Product Found.';
        }

        if(empty($data['Product']['product_quantity']) || $data['Product']['product_quantity'] <= 0){
            $quantity = 1;
        }else{
            $quantity = $data['Product']['product_quantity'];
        }

        $image = $this->ProductImages->find()->where(['product_id' => $productData['id']])->first();
        if (!empty($image)) {
            $fullUrl = Router::fullBaseUrl();
            $productInfo['product_image'] = $fullUrl . '/' . $image['image_path'];
        } else {
            $productInfo['product_image'] = '';
        }

        $productInfo['id'] = $productData['id'];
        $productInfo['quantity'] = $quantity;
        $productInfo['category_id'] = $productData['category_id'];
        $productInfo['category_name'] = $categories['title'];
        $productInfo['name'] = $productData['title'];
        $productInfo['slug'] = $productData['slug'];
        $productInfo['price'] = intval($productData['price']);
        $productInfo['final_price'] = (intval($productData['price']) * $quantity);

        if (!empty($data['Product']['product_delivery_address_id'])) {
            $ProductDeliveryAddress = $this->ProductDeliveryAddress->find()->where(['id' => $data['Product']['product_delivery_address_id']])->first();
            if (!empty($ProductDeliveryAddress)) {
                $productInfo['product_delivery_address_id'] = $ProductDeliveryAddress->id;
                $productInfo['product_delivery_address'] = json_decode($ProductDeliveryAddress);
            }
        }

        return $productInfo;
    }

    public function calculatePrice($cartData , $newAddToCartData) {

        $productData = $this->Products->find()->where(['id' => $newAddToCartData['id']])->first();
        if (!empty($productData)) {

            $cartFinalPriceQuantity = [];
            if(empty($cartData['quantity']) || $cartData['quantity'] > 0){
                $cartFinalPriceQuantity['quantity'] = (intval($cartData['quantity']) + intval($newAddToCartData['quantity']));
            }

            $cartFinalPriceQuantity['final_price'] = (intval($productData['price']) *$cartFinalPriceQuantity['quantity']);
            return $cartFinalPriceQuantity;
        } else {
            return false;
        }
    }

}
