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
        $this->Security->setConfig('unlockedActions', ['add', 'checkEmail']);
        $this->Categories = $this->getDbTable('Categories');
        $this->Products = $this->getDbTable('Products');

    }

    public function index() {

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

}