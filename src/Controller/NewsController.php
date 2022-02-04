<?php

namespace App\Controller;

use Cake\Event\EventInterface;
use Cake\Routing\Router;

class NewsController extends AppController
{
    public function initialize(): void
    {
        parent::initialize();
        $this->Auth->allow(['login', 'logout', 'checkEmail', 'resetPassword']);
    }

    public function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event);
        $this->Security->setConfig('unlockedActions', ['add', 'checkEmail', 'edit']);

        $this->Auth->allow(['view']);

        $this->Categories = $this->getDbTable('Categories');
        $this->Products = $this->getDbTable('Products');
        $this->ProductImages = $this->getDbTable('ProductImages');
        $this->News = $this->getDbTable('News');
    }

    public function view($id) {

        $this->autoRender = false;

        $getNews = $this->News->find()->where(['id' => $id])->first();

        if (!empty($getNews['news_image'])) {
            $fullUrl = Router::fullBaseUrl();
            $imageArray = $fullUrl . '/' . $getNews['news_image'];

            $getNews['news_image'] = $imageArray;
        }

        $this->set('news', $getNews);
        return $this->render('view');
        die();
    }

}
