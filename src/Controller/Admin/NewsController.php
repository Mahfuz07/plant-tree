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
        $this->Security->setConfig('unlockedActions', ['add', 'checkEmail', 'edit', 'delete']);

        $this->Categories = $this->getDbTable('Categories');
        $this->Products = $this->getDbTable('Products');
        $this->ProductImages = $this->getDbTable('ProductImages');
        $this->News = $this->getDbTable('News');
    }

    public function index() {

        $news = $this->News->find('all')->where()->toArray();

        $this->set('newsList', $news);

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
                        }
                    } else {
                        $this->Flash->success('Please upload jpg,png,jpeg image format!', ['key' => 'success']);
                        $this->redirect('/admin/news/add');
                    }
                    $this->Flash->success('News has been saved!', ['key' => 'success']);
                    $this->redirect('/admin/news');
                } else {
                    $this->Flash->error('Oops News not has been saved!', ['key' => 'error']);
                    $this->redirect('/admin/news/add');
                }
            }
        }
    }

    public function edit($id) {
        $news = $this->News->find()->where(['id' => $id])->first();

        if ($this->request->getData()) {
            $requestData = $this->request->getData();
            if (!empty($requestData['news_image'])) {

                $news = $this->News->find()->where(['id' => $id])->first();
                if (!empty($news['news_image'])) {
                    if (is_file(WWW_ROOT . $news['news_image'])) {
                        unlink(WWW_ROOT . $news['news_image']);
                    }

                }
                $extension=array("jpeg","jpg","png");
                $file_name= $requestData['news_image']->getClientFilename();
                $ext = pathinfo($file_name,PATHINFO_EXTENSION);

                $image_name = 'news-' . strtotime(date('Y-m-d H:i:s'));
                $targetPath = WWW_ROOT . 'img' . DS . 'news_images' . DS . $image_name . '.' . $ext;

                if(in_array($ext,$extension)) {
                    if(!file_exists($targetPath)) {
                        $requestData['news_image']->moveTo($targetPath);
                        $newPrepareData['news_image'] = 'img' . DS . 'news_images' . DS . $image_name . '.' . $ext;
                        $newPrepareData['title'] = $requestData['title'];
                        $newPrepareData['display_name'] = $requestData['display_name'];
                        $newPrepareData['content'] = $requestData['content'];
                        $news = $this->News->patchEntity($news, $newPrepareData);
                        $this->News->save($news);
                    }
                } else {
                    $this->Flash->success('Please upload jpg,png,jpeg image format!', ['key' => 'success']);
                    $this->redirect('/admin/news/add');
                }
                if ($news->id) {
                    $this->Flash->success('News has been saved!', ['key'=>'success']);
                    $this->redirect('/admin/news');
                } else {
                    $this->Flash->error('Oops News not has been saved!', ['key'=>'error']);
                    $this->redirect('/admin/news/edit/' . $id);
                }
            }
        }
        if (!empty($news)) {
            $this->set('news', $news);
        }
    }

    public function delete($id) {

        if (!empty($id)) {
            $getNews = $this->News->find()->where(['id' => $id])->first();

            if (!empty($getNews)) {
                if ($this->News->delete($getNews)) {
                    $this->Flash->success('News has been deleted!', ['key'=>'success']);
                    $this->redirect('/admin/news');
                } else {
                    $this->Flash->error('Oops News not has been deleted!', ['key'=>'error']);
                    $this->redirect('/admin/news');
                }
            }
        }
    }

}
