<?php

namespace App\Controller\Admin;

use App\Controller\AppController;
use Cake\Event\EventInterface;
use Cake\Utility\Security;

class CategoriesController extends AppController
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

        $categories = $this->Categories->find('all')->where()->toArray();

        $this->set('categories', $categories);

    }

    public function add () {

        if ($this->request->is('post')) {
            $requestData = $this->request->getData();
            if ($requestData) {
                $slug = $this->Categories->find()->where(['slug' => $requestData['slug']])->first();
                if (empty($slug)) {
                    $categories = $this->Categories->newEmptyEntity();
                    $categories = $this->Categories->patchEntity($categories, $requestData);
                    $categories = $this->Categories->save($categories);
                    if ($categories->id) {
                        $this->Flash->success('Categories has been saved!', ['key'=>'success']);
                        $this->redirect('/admin/dashboard');
                    } else {
                        $this->Flash->error('Oops Category not has been saved!', ['key'=>'error']);
                        $this->redirect('/admin/categories/add');
                    }
                } else {
                    $this->Flash->error('Slug already exit!', ['key'=>'error']);
                    $this->redirect('/admin/categories/add');
                }
            }
        }
    }

    public function edit ($id = null) {

        $category = $this->Categories->find()->where(['id' => $id])->first();

        if ($this->request->getData()) {
            $requestData = $this->request->getData();
            if (!empty($requestData)) {
                $categories = $this->Categories->find()->where(['id' => $id])->first();
                $categories = $this->Categories->patchEntity($categories, $requestData);
                $categories = $this->Categories->save($categories);
                if ($categories->id) {
                    $this->Flash->success('Categories has been saved!', ['key'=>'success']);
                    $this->redirect('/admin/categories');
                } else {
                    $this->Flash->error('Oops Category not has been saved!', ['key'=>'error']);
                    $this->redirect('/admin/categories/add');
                }
            }
        }
        if (!empty($category)) {
            $this->set('category', $category);
        }

    }

}
