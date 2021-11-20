<?php

namespace App\Controller\Admin;

use App\Controller\AppController;
use Cake\Event\EventInterface;
use Cake\Utility\Security;


class ProductsController extends AppController
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
    }

    public function index() {

        $products = $this->Products->find('all')->where()->toArray();

        $this->set('products', $products);

    }

    public function add ()
    {

        $categoryList = $this->Categories->find()->where(['published' => 1])->select(['id', 'title'])->orderDesc('id')->toArray();

        if ($this->request->getData()) {

            $requestData = $this->request->getData();
            $slug = $this->Categories->find()->where(['slug' => $requestData['slug']])->first();
            if (empty($slug)) {
                if (!empty($requestData)) {
                    $products = $this->Products->newEmptyEntity();
                    $products = $this->Products->patchEntity($products, $requestData);
                    $products = $this->Products->save($products);
                    if ($products->id) {
                        $this->Flash->success('Product has been saved!', ['key' => 'success']);
                        $this->redirect('/admin/dashboard');
                    } else {
                        $this->Flash->error('Oops Product not has been saved!', ['key' => 'error']);
                        $this->redirect('/admin/products/add');
                    }
                } else {
                    $this->Flash->error('Slug already exit!', ['key' => 'error']);
                    $this->redirect('/admin/categories/add');
                }
            }
        }
        $this->set('categoryList', $categoryList);
    }

    public function edit ($id = null)
    {
        $categoryList = $this->Categories->find()->where(['published' => 1])->select(['id', 'title'])->orderDesc('id')->toArray();

        $product = $this->Products->find()->where(['id' => $id])->first();

        if ($this->request->getData()) {

            $requestData = $this->request->getData();
//            $slug = $this->Categories->find()->where(['slug' => $requestData['slug']])->first();
//            if (empty($slug)) {
                if (!empty($requestData)) {
                    $products = $this->Products->get($id);
                    $products = $this->Products->patchEntity($products, $requestData);
                    $products = $this->Products->save($products);
                    if ($products->id) {
                        $this->Flash->success('Product has been saved!', ['key' => 'success']);
                        $this->redirect('/admin/dashboard');
                    } else {
                        $this->Flash->error('Oops Product not has been saved!', ['key' => 'error']);
                        $this->redirect('/admin/products/add');
                    }
                } else {
                    $this->Flash->error('Slug already exit!', ['key' => 'error']);
                    $this->redirect('/admin/products/edit/'. $id);
                }
//            }
        }

        if (!empty($product)) {
            $this->set('categoryList', $categoryList);
            $this->set('product', $product);
        }

    }

}
