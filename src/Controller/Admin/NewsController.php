<?php

namespace App\Controller\Admin;

use App\Controller\AppController;
use Cake\Event\EventInterface;

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

        $this->Categories = $this->getDbTable('Categories');
        $this->Products = $this->getDbTable('Products');
        $this->ProductImages = $this->getDbTable('ProductImages');
        $this->News = $this->getDbTable('News');
    }

    public function index() {

        $products = $this->Products->find('all')->where()->toArray();

        $this->set('products', $products);

    }

    public function add() {

        if ($this->request->is('post')) {
            if ($this->request->getData()) {

                $requestData = $this->request->getData();

                $extension=array("jpeg","jpg","png");

                if (!empty($requestData['news_image'])) {

                    $file_name= $requestData['news_image']->getClientFilename();
                    $ext = pathinfo($file_name,PATHINFO_EXTENSION);

                    $image_name = 'news-' . strtotime(date('Y-m-d H:i:s'));
                    $targetPath = WWW_ROOT . 'img' . DS . 'news_images' . DS . $image_name . '.' . $ext;

                    if(in_array($ext,$extension)) {
                        if(!file_exists($targetPath)) {
                            $requestData['news_image']->moveTo($targetPath);
                            $newsSave = $this->News->newEmptyEntity();
                            $newPrepareData['news_image'] = 'img' . DS . 'news_images' . DS . $image_name . '.' . $ext;
                            $newPrepareData['title'] = $requestData['title'];
                            $newPrepareData['display_name'] = $requestData['display_name'];
                            $newPrepareData['content'] = $requestData['content_text'];
                            $newsSave = $this->News->patchEntity($newsSave, $newPrepareData);
                            $this->News->save($newsSave);
                        } else {
                            $this->Flash->success('Please upload jpg,png,jpeg image format!', ['key' => 'success']);
                            $this->redirect('/admin/news/add');
                        }
                    }
                    $this->Flash->success('News has been saved!', ['key' => 'success']);
                    $this->redirect('/admin/dashboard');
                } else {
                    $this->Flash->error('Oops News not has been saved!', ['key' => 'error']);
                    $this->redirect('/admin/news/add');
                }
            }
        }

    }

}
