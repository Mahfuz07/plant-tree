<?php

namespace App\Controller\Admin;

use App\Controller\AppController;
use Cake\Core\App;
use Cake\Event\EventInterface;
use Cake\Mailer\Email;
use Cake\Mailer\Mailer;
use Cake\Utility\Security;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

class CustomersController extends AppController
{

    public function initialize(): void
    {
        parent::initialize();

//        $this->Users = $this->getDbTable('ManageUser.Users');
//        $this->Roles = $this->getDbTable('ManageUser.Roles');

        $this->Auth->allow(['login', 'logout', 'checkEmail', 'resetPassword']);
    }

    public function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event);
        $this->Security->setConfig('unlockedActions', ['add', 'checkEmail']);
        $this->Roles = $this->getDbTable('ManageUser.Roles');
        $this->Users = $this->getDbTable('ManageUser.Users');

    }

    public function index () {

        $customers = $this->Users->find()->where(['role_id' => 2])->toArray();

        $this->set('customers', $customers);
    }

    public function add () {

        if ($this->request->is('post')) {
            $requestData = $this->request->getData();
            if ($requestData) {
                $this->Users = $this->getDbTable('ManageUser.Users');
                $this->Roles = $this->getDbTable('ManageUser.Roles');

                $roles = $this->Roles->find()->all()->toArray();

                $users = $this->Users->newEmptyEntity();
                $requestData['password'] = Security::hash($requestData['password'], null, true);
                $requestData['role_id'] = $roles[1]['id'];
                $requestData['status'] = 1;
                unset($requestData['confirm_password']);
                $users = $this->Users->patchEntity($users, $requestData);
                $user = $this->Users->save($users);
                if ($user->id) {
                    $this->Flash->success('User has been saved', ['key'=>'success']);
                    $this->redirect('/admin/dashboard');
                }
            }
        }


//        $email = new Email();
//        $email->settransport('default');
//        $email->setTo('amahfuzanan@gmail.com', 'Mahfuz');
//        $email->setSubject('Test');
//        $email->setEmailFormat('html');
//        $email->setViewVars(['user' => ['username' => 'mahfuz']]);
//        $email->send('<html><h1>Hello</h1></html>');

    }

    public function checkEmail() {

        if ($this->request->is('post')) {
            $getData = $this->request->getData();
            $findUser = $this->Users->find()->where(['email' => $getData['email']])->first();

            if (empty($findUser)) {
                echo $this->json_encode(['success' => true]);
            } else {
                echo $this->json_encode(['success' => false]);
            }
            exit;
        }
    }

}
