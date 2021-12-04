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
        $this->ProductImages = $this->getDbTable('ProductImages');
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

                        $extension=array("jpeg","jpg","png");
                        foreach($requestData["upload_image"] as $key=>$tmp_name) {
                            $file_name= $tmp_name->getClientFilename();
                            $ext = pathinfo($file_name,PATHINFO_EXTENSION);

                            $image_name = $key . '-' .$requestData['slug'] . '-' . strtotime(date('Y-m-d H:i:s'));
                            $targetPath = WWW_ROOT . 'img' . DS . 'product_images' . DS . $image_name . '.' . $ext;

                            if(in_array($ext,$extension)) {
                                if(!file_exists($targetPath)) {
                                    $tmp_name->moveTo($targetPath);
                                    $productImages = $this->ProductImages->newEmptyEntity();
                                    $productImage['product_id'] = $products->id;
                                    $productImage['image_path'] = 'img' . DS . 'product_images' . DS . $image_name . '.' . $ext;
                                    $productImages = $this->ProductImages->patchEntity($productImages, $productImage);
                                    $productImages = $this->ProductImages->save($productImages);
                                } else {
//                                $filename=basename($file_name,$ext);
//                                $newFileName=$filename.time().".".$ext;
//                                move_uploaded_file($file_tmp=$_FILES["files"]["tmp_name"][$key],"photo_gallery/".$txtGalleryName."/".$newFileName);
                                }
                            }
                            else {
                                array_push($error,"$file_name, ");
                            }
                        }
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
