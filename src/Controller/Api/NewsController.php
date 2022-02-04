<?php

namespace App\Controller\Api;

use Cake\Datasource\ConnectionManager;
use Cake\Event\EventInterface;
use Cake\Routing\Router;
use ManageUser\Controller\AppController;

class NewsController extends AppController
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
        $this->News = $this->getDbTable('News');

        $this->mode = $this->Common->getLocalServerDeviceMode();
    }

    public function beforeFilter(EventInterface $event)
    {
        $this->log($this->request->getParam('action'));
        parent::beforeFilter($event);
        //$this->getEventManager()->off($this->Csrf);
        $this->Session= $this->getRequest()->getSession();
        $this->Auth->allow([
            'newsList'
        ]);
        $actions =  array(
            'newsList'
        );
        $this->Security->setConfig('unlockedActions', $actions);
    }

    public function newsList() {

        if ($this->AccessToken->verify()) {

            if ($this->request->is('post')) {

                $fullUrl = Router::fullBaseUrl();
                $getNews = $this->News->find()->select(['id', 'news_image', 'title', 'display_name'])->orderDesc('id')->toArray();

                $getUser = $this->getComponent('CommonFunction')->getUserInfo();

                if (!empty($getNews)) {

                    foreach ($getNews as $news) {

                        if (!empty($news['news_image'])) {
                            $imageArray = $fullUrl . '/' . $news['news_image'];

                            $news['news_image'] = $imageArray;
                        }
                    }
                }

                if (!empty($getNews)) {
                    return $this->getResponse()
                        ->withStatus(200)
                        ->withType('application/json')
                        ->withStringBody(json_encode(array(
                            'status' => 'success',
                            'news' => $getNews,
                            'mode' => $this->mode)));
                } else {
                    return $this->getResponse()
                        ->withStatus(200)
                        ->withType('application/json')
                        ->withStringBody(json_encode(array(
                            'status' => 'success',
                            'news' => array(),
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
