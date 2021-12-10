<?php

namespace App\Controller\Component;

use Cake\Event\Event;
use Cake\ORM\TableRegistry;

class ProductComponent extends BaseComponent
{

    var $controller;
    var $Session;

    function startup(Event $event)
    {
        $this->controller = $this->_registry->getController();
        $this->Session = $this->controller->getRequest()->getSession();

        $this->Products = TableRegistry::getTableLocator()->get('Products');
        $this->ProductDeliveryAddress = TableRegistry::getTableLocator()->get('ProductDeliveryAddress');
        $this->FavouritesProduct = TableRegistry::getTableLocator()->get('FavouritesProduct');
        $this->ProductRecentlyView = TableRegistry::getTableLocator()->get('ProductRecentlyView');
    }

    public function saveFavouritesProduct($product_id, $user_id, $favourite) {

        if (!empty($product_id)) {
            $product = $this->Products->find()->where(['id' => $product_id, 'published' => 1])->first();
            if (!empty($product)) {
                $favouritesProducts = $this->FavouritesProduct->find()->where(['product_id' => $product_id, 'user_id' => $user_id])->first();
                if (empty($favouritesProducts)) {
                    $favouritesProduct = $this->FavouritesProduct->newEmptyEntity();
                    $favouriteParams['product_id'] = $product['id'];
                    $favouriteParams['user_id'] = $user_id;
                    $favouriteParams['favourite'] = $favourite;
                    $favouritesProduct = $this->FavouritesProduct->patchEntity($favouritesProduct, $favouriteParams);
                    if($this->FavouritesProduct->save($favouritesProduct)) {
                        return true;
                    }
                } else {
                    return true;
                }

            } else {
                return false;
            }

        } else {
            return false;
        }
    }

    public function recentlyViewSave($product_id, $user_id): bool
    {

        $productRecentlyView = $this->ProductRecentlyView->find()->where(['product_id' => $product_id, 'user_id' => $user_id])->first();

        if (empty($productRecentlyView)) {

            $productRecentlyView = $this->ProductRecentlyView->newEmptyEntity();
            $recentlyView['product_id'] = $product_id;
            $recentlyView['user_id'] = $user_id;
            $recentlyView['date_time'] = date('Y-m-d H:i:s');
            $recentlyView['recently_view'] = true;
        } else {
            $recentlyView['date_time'] = date('Y-m-d H:i:s');
        }

        $productRecentlyView = $this->ProductRecentlyView->patchEntity($productRecentlyView, $recentlyView);
        if ($this->ProductRecentlyView->save($productRecentlyView)) {
            return true;
        } else {
            return false;
        }

    }

}
