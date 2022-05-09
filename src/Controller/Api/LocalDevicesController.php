<?php

namespace App\Controller\Api;

use App\Enum\OrderStage;
use Cake\Datasource\ConnectionManager;
use Cake\Event\EventInterface;
use Cake\ORM\TableRegistry;
use Cake\Routing\Router;
use Cake\Utility\Security;
use ManageUser\Controller\AppController;
use phpDocumentor\Reflection\Types\This;
use Cake\Http\Response;
use PHPMailer\PHPMailer\Exception;
use Symfony\Component\HttpFoundation\File\File;

class LocalDevicesController extends AppController
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
        $this->Orders = $this->getDbTable('Orders');

        $this->mode = $this->Common->getLocalServerDeviceMode();
    }

    public function beforeFilter(EventInterface $event)
    {
        $this->log($this->request->getParam('action'));
        parent::beforeFilter($event);
        //$this->getEventManager()->off($this->Csrf);
        $this->Session= $this->getRequest()->getSession();
        $this->Auth->allow([
            'login', 'getTokenByRefreshToken', 'logout', 'createUser', 'getAllProductsByCategory', 'getProduct', 'getAddress',
            'addToFavouriteProduct', 'getFavouriteProducts', 'getRecentlyView', 'profileImageChange', 'updateProfileInfo', 'saveAddress',
            'getUserInfo', 'changePassword', 'filterProducts'
        ]);
        $actions =  array(
            'login', 'getTokenByRefreshToken', 'logout', 'createUser', 'getProduct', 'getAllProductsByCategory', 'getProduct', 'getAddress',
            'addToFavouriteProduct', 'getFavouriteProducts', 'getRecentlyView', 'profileImageChange', 'updateProfileInfo', 'saveAddress',
            'getUserInfo', 'changePassword', 'filterProducts'
        );
        $this->Security->setConfig('unlockedActions', $actions);
    }

    public function login()
    {
        if($this->request->is('post')){

            $request_data = file_get_contents("php://input");
            $request_data = $this->json_decode($request_data, true);
            $this->log($request_data);

            $email = isset($request_data['User']['email']) ? $request_data['User']['email']:'';
            $password = isset($request_data['User']['password']) ? $request_data['User']['password']:'';

            $udid = isset($request_data['User']['udid'])?$request_data['User']['udid']:'';
            $device_name = isset($request_data['User']['device_name'])?$request_data['User']['device_name']:'';

            if(!empty($email) && !empty($password)){

                $users = $this->Users->find()->where([
                    'email' => $email,
                    'password' => Security::hash($password, null, true),
                    'status' => true
                ])->first();

                if($users){
                    $users['Role'] = $this->Roles->find()->where([
                        'id' => $users['role_id']
                    ])->first();

                    $token = $this->AccessToken->getAccessToken();

                        $this->getComponent('CommonFunction')->UserLoginDetailsSave($users['id'], $udid, $device_name, $token['access_token'], $token['refresh_token']);

                    $this->set(array(
                        'status' => 'success',
                        'user' => $users,
                        'token' => $token,
                        'mode' => $this->mode,
                        '_serialize' => array('status', 'user', 'token', 'mode')
                    ));
                    return $this->getResponse()
                        ->withStatus(200)
                        ->withType('application/json')
                        ->withStringBody(json_encode(array(
                            'status' => 'success',
                            'user' => $users,
                            'token' => $token,
                            'mode' => $this->mode)));
                }else{
                    return $this->getResponse()
                        ->withStatus(200)
                        ->withType('application/json')
                        ->withStringBody(json_encode(array(
                            'status' => 'error',
                            'msg' => 'Invalid username or password',
                            'mode' => $this->mode)));
                }

            }else{
                $this->set(array(
                    'status' => 'error',
                    'msg' => 'Invalid username or password',
                    'mode' => $this->mode,
                    '_serialize' => array('status', 'msg', 'mode')
                ));
            }

        }else{
            $this->set(array(
                'status' => 'error',
                'msg' => 'Invalid request method',
                'mode' => $this->mode,
                '_serialize' => array('status', 'msg', 'mode')
            ));
        }
    }

    public function getTokenByRefreshToken()
    {
        if($this->request->is('post')){

            $request_data = file_get_contents("php://input");
            $request_data = $this->json_decode($request_data, true);
            $this->log($request_data);

            $refresh_token = $request_data['refresh_token'];
            $this->log('refresh token request :');
            $this->log($request_data);
            $this->log('refresh token header request:');
            $this->log( $this->request->getHeader('Authorization'));

            if(isset($refresh_token) && !empty($refresh_token)){

                $result = $this->AccessToken->getTokenByRefreshToken($refresh_token);

                $this->UserLoginDetails =  $this->getDbTable('UserLoginDetails');
                $data_log_entities = $this->UserLoginDetails->find()->where(['refresh_token' => $refresh_token])->first();
                $data_log['access_token'] = $result['access_token'];
                $data_log['refresh_token'] = $result['refresh_token'];
                $data_log_entities = $this->UserLoginDetails->patchEntity($data_log_entities, $data_log);
                $this->UserLoginDetails->save($data_log_entities);

                if(isset($result['error'])){
                    return $this->getResponse()
                        ->withStatus(200)
                        ->withType('application/json')
                        ->withStringBody(json_encode(array(
                            'status' => 'error',
                            'msg' => $result['message'],
                            'mode' => $this->mode)));
                }else{
                    return $this->getResponse()
                        ->withStatus(200)
                        ->withType('application/json')
                        ->withStringBody(json_encode(array(
                            'status' => 'success',
                            'token' => array(
                                'access_token'=> $result['access_token'],
                                'token_type'=> $result['token_type'],
                                'expires_in'=> $result['expires_in'],
                                'refresh_token'=> $result['refresh_token']
                            ),
                            'mode' => $this->mode)));
                }
            }else{
                return $this->getResponse()
                    ->withStatus(200)
                    ->withType('application/json')
                    ->withStringBody(json_encode(array(
                        'status' => 'error',
                        'msg' => 'The refresh token is invalid.',
                        'mode' => $this->mode,
                        $this->viewBuilder()->setOption('serialize', true))));
            }
        }else{
            return $this->getResponse()
                ->withStatus(200)
                ->withType('application/json')
                ->withStringBody(json_encode(array(
                'status' => 'error',
                'msg' => 'Invalid request method',
                'mode' => $this->mode)));
        }
    }

    public function createUser () {

        if($this->request->is('post')){
            $request_data = file_get_contents("php://input");
            $request_data = $this->json_decode($request_data, true);
            $this->log($request_data);

            if (!empty($request_data)) {
                $email = isset($request_data['User']['email']) ? $request_data['User']['email']:'';
                $displayName = isset($request_data['User']['display_name']) ? $request_data['User']['display_name']:'';
                $address = isset($request_data['User']['address']) ? $request_data['User']['address']:'';
                $phone = isset($request_data['User']['phone']) ? $request_data['User']['phone']:'';
                $password = isset($request_data['User']['password']) ? $request_data['User']['password']:'';

                $errorMessage = [];
                if (empty($displayName)) {
                    $errorMessage[] = ['Required field display name is missing'];
                }
                if (empty($email)) {
                    $errorMessage[] = ['Required field email is missing'];
                }
                if (empty($address)) {
                    $errorMessage[] = ['Required field address is missing'];
                }
                if (empty($phone)) {
                    $errorMessage[] = ['Required field phone is missing'];
                }
                if (empty($password)) {
                    $errorMessage[] = ['Required field password is missing'];
                }

                if (count($errorMessage) == 0) {
                    $getEmail = $this->Users->find()->where(['email' => $email])->first();
                    if (empty($getEmail)) {
                        $roles = $this->Roles->find()->all()->toArray();
                        $users = $this->Users->newEmptyEntity();
                        $userData['display_name'] = $displayName;
                        $userData['email'] = $email;
                        $userData['address'] = $address;
                        $userData['phone_no'] = $phone;
                        $userData['password'] = Security::hash($password, null, true);
                        $userData['role_id'] = $roles[1]['id'];
                        $userData['status'] = 1;

                        $users = $this->Users->patchEntity($users, $userData);
                        $user = $this->Users->save($users);
                        if ($user->id) {

                            try {
                                $this->getComponent('EmailHandler')->emailSend($email, $displayName);
                            } catch (Exception $e) {
                                $this->log($e->getMessage());
                            }

                            return $this->getResponse()
                                ->withStatus(200)
                                ->withType('application/json')
                                ->withStringBody(json_encode(array(
                                    'status' => 'success',
                                    'msg' => 'Account Create Successfully',
                                    'mode' => $this->mode)));
                        } else{
                            return $this->getResponse()
                                ->withStatus(200)
                                ->withType('application/json')
                                ->withStringBody(json_encode(array(
                                    'status' => 'error',
                                    'msg' => 'Oops user create failed',
                                    'mode' => $this->mode)));
                        }
                    } else {
                        return $this->getResponse()
                            ->withStatus(404)
                            ->withType('application/json')
                            ->withStringBody(json_encode(array(
                                'status' => 'error',
                                'msg' => 'Email Already Exist!',
                                'mode' => $this->mode)));
                    }
                } else {
                    return $this->getResponse()
                        ->withStatus(404)
                        ->withType('application/json')
                        ->withStringBody(json_encode(array(
                            'status' => 'error',
                            'msg' => $errorMessage,
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
        }else{
            return $this->getResponse()
                ->withStatus(200)
                ->withType('application/json')
                ->withStringBody(json_encode(array(
                    'status' => 'error',
                    'msg' => 'Invalid request method',
                    'mode' => $this->mode)));
        }
    }

    public function getAllProductsByCategory() {

        if ($this->AccessToken->verify()) {
            if ($this->request->is('post')) {

                $fullUrl = Router::fullBaseUrl();
                $products = $this->Products->find()->where(['category_id in (SELECT id FROM categories WHERE published = 1)', 'published' => 1])->orderDesc('id')->toArray();

                $getUser = $this->getComponent('CommonFunction')->getUserInfo();

                if (!empty($products)) {

                    foreach ($products as $product) {

                        $getFavoriteProduct = $this->FavouritesProduct->find()->where(['product_id' => $product['id'], 'user_id' => $getUser['id']])->first();

                        if (!empty($getFavoriteProduct)) {
                            $product['favorite'] = true;
                        } else {
                            $product['favorite'] = false;
                        }

                        $images = $this->ProductImages->find()->where(['product_id' => $product['id']])->toArray();
                        if (!empty($images)) {
                            $imageArray = [];
                            foreach ($images as $image) {
                                $imageArray[] = $fullUrl . '/' . 'img' . DS . 'product_images' . DS . 'thumb' . DS .  $image['image_path'];
                            }
                            $product['image'] = $imageArray;
                        }
                    }
                }

                if (!empty($products)) {
                    return $this->getResponse()
                        ->withStatus(200)
                        ->withType('application/json')
                        ->withStringBody(json_encode(array(
                            'status' => 'success',
                            'products' => $products,
                            'mode' => $this->mode)));
                } else {
                    return $this->getResponse()
                        ->withStatus(200)
                        ->withType('application/json')
                        ->withStringBody(json_encode(array(
                            'status' => 'success',
                            'products' => array(),
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

    public function filterProducts(): Response {

        if ($this->AccessToken->verify()) {
            if ($this->request->is('post')) {
                $request_data = $this->request->getQueryParams();

                if (!empty($request_data)) {
                    $action = isset($request_data['action']) ? $request_data['action']:'';
                    $minPrice = isset($request_data['min_price']) ? $request_data['min_price']:'';
                    $maxPrice = isset($request_data['max_price']) ? $request_data['max_price']:'';
                    $search_product = isset($request_data['search_product']) ? $request_data['search_product']:'';

                    if (!empty($action) && $action == 'lowToHigh') {
                        if (!empty($minPrice) && !empty($maxPrice)) {
                            if (!empty($search_product)) {
                                $products = $this->Products->find()->where(['category_id in (SELECT id FROM categories WHERE published = 1)', 'published' => 1, 'price >=' => $minPrice, 'price <=' => $maxPrice, 'display_name LIKE' => $search_product . '%'])->orderAsc('price')->toArray();
                            } else {
                                $products = $this->Products->find()->where(['category_id in (SELECT id FROM categories WHERE published = 1)', 'published' => 1, 'price >=' => $minPrice, 'price <=' => $maxPrice])->orderAsc('price')->toArray();
                            }
                        } elseif (!empty($minPrice)) {
                            if (!empty($search_product)) {
                                $products = $this->Products->find()->where(['category_id in (SELECT id FROM categories WHERE published = 1)', 'published' => 1, 'price >=' => $minPrice, 'display_name LIKE' => $search_product . '%'])->orderAsc('price')->toArray();
                            } else {
                                $products = $this->Products->find()->where(['category_id in (SELECT id FROM categories WHERE published = 1)', 'published' => 1, 'price >=' => $minPrice])->orderAsc('price')->toArray();
                            }
                        } elseif (!empty($maxPrice)) {
                            if (!empty($search_product)) {
                                $products = $this->Products->find()->where(['category_id in (SELECT id FROM categories WHERE published = 1)', 'published' => 1, 'price <=' => $maxPrice, 'display_name LIKE' => $search_product . '%'])->orderAsc('price')->toArray();
                            } else {
                                $products = $this->Products->find()->where(['category_id in (SELECT id FROM categories WHERE published = 1)', 'published' => 1, 'price <=' => $maxPrice])->orderAsc('price')->toArray();
                            }
                        }else {
                            if (!empty($search_product)) {
                                $products = $this->Products->find()->where(['category_id in (SELECT id FROM categories WHERE published = 1)', 'published' => 1,'display_name LIKE' => $search_product . '%'])->orderAsc('price')->toArray();
                            } else {
                                $products = $this->Products->find()->where(['category_id in (SELECT id FROM categories WHERE published = 1)', 'published' => 1])->orderAsc('price')->toArray();
                            }
                        }
                    } elseif (!empty($action) && $action == 'highToLow') {
                        if (!empty($minPrice) && !empty($maxPrice)) {
                            if (!empty($search_product)) {
                                $products = $this->Products->find()->where(['category_id in (SELECT id FROM categories WHERE published = 1)', 'published' => 1, 'price >=' => $minPrice, 'price <=' => $maxPrice, 'display_name LIKE' => $search_product . '%'])->orderDesc('price')->toArray();
                            } else {
                                $products = $this->Products->find()->where(['category_id in (SELECT id FROM categories WHERE published = 1)', 'published' => 1, 'price >=' => $minPrice, 'price <=' => $maxPrice])->orderDesc('price')->toArray();
                            }
                        } elseif (!empty($minPrice)) {
                            if (!empty($search_product)) {
                                $products = $this->Products->find()->where(['category_id in (SELECT id FROM categories WHERE published = 1)', 'published' => 1, 'price >=' => $minPrice, 'display_name LIKE' => $search_product . '%'])->orderDesc('price')->toArray();
                            } else {
                                $products = $this->Products->find()->where(['category_id in (SELECT id FROM categories WHERE published = 1)', 'published' => 1, 'price >=' => $minPrice])->orderDesc('price')->toArray();
                            }
                        } elseif (!empty($maxPrice)) {
                            if (!empty($search_product)) {
                                $products = $this->Products->find()->where(['category_id in (SELECT id FROM categories WHERE published = 1)', 'published' => 1, 'price <=' => $maxPrice, 'display_name LIKE' => $search_product . '%'])->orderDesc('price')->toArray();

                            } else {
                                $products = $this->Products->find()->where(['category_id in (SELECT id FROM categories WHERE published = 1)', 'published' => 1, 'price <=' => $maxPrice])->orderDesc('price')->toArray();
                            }
                        } else {
                            if (!empty($search_product)) {
                                $products = $this->Products->find()->where(['category_id in (SELECT id FROM categories WHERE published = 1)', 'published' => 1, 'display_name LIKE' => $search_product . '%'])->orderDesc('price')->toArray();

                            } else {
                                $products = $this->Products->find()->where(['category_id in (SELECT id FROM categories WHERE published = 1)', 'published' => 1])->orderDesc('price')->toArray();
                            }
                        }
                    } elseif (!empty($action) && $action == 'bestMatch') {

                        if (!empty($minPrice) && !empty($maxPrice)) {
                            if (!empty($search_product)) {
                                $products = $this->Products->find()->where(['category_id in (SELECT id FROM categories WHERE published = 1)', 'published' => 1, 'price >=' => $minPrice, 'price <=' => $maxPrice, 'display_name LIKE' => $search_product . '%'])->orderAsc('price')->toArray();
                            } else {
                                $products = $this->Products->find()->where(['category_id in (SELECT id FROM categories WHERE published = 1)', 'published' => 1, 'price >=' => $minPrice, 'price <=' => $maxPrice])->orderAsc('price')->toArray();
                            }
                        } elseif (!empty($minPrice)) {
                            if (!empty($search_product)) {
                                $products = $this->Products->find()->where(['category_id in (SELECT id FROM categories WHERE published = 1)', 'published' => 1, 'price >=' => $minPrice, 'display_name LIKE' => $search_product . '%'])->orderAsc('price')->toArray();
                            } else {
                                $products = $this->Products->find()->where(['category_id in (SELECT id FROM categories WHERE published = 1)', 'published' => 1, 'price >=' => $minPrice])->orderAsc('price')->toArray();
                            }
                        } elseif (!empty($maxPrice)) {
                            if (!empty($search_product)) {
                                $products = $this->Products->find()->where(['category_id in (SELECT id FROM categories WHERE published = 1)', 'published' => 1, 'price <=' => $maxPrice, 'display_name LIKE' => $search_product . '%'])->orderAsc('price')->toArray();
                            } else {
                                $products = $this->Products->find()->where(['category_id in (SELECT id FROM categories WHERE published = 1)', 'published' => 1, 'price <=' => $maxPrice])->orderAsc('price')->toArray();
                            }
                        }else {
                            if (!empty($search_product)) {
                                $products = $this->Products->find()->where(['category_id in (SELECT id FROM categories WHERE published = 1)', 'published' => 1, 'display_name LIKE' => $search_product . '%'])->orderAsc('price')->toArray();
                            } else {
                                $products = $this->Products->find()->where(['category_id in (SELECT id FROM categories WHERE published = 1)', 'published' => 1])->orderAsc('price')->toArray();
                            }
                        }
                    } else {
                        if (!empty($search_product)) {
                            $products = $this->Products->find()->where(['category_id in (SELECT id FROM categories WHERE published = 1)', 'published' => 1, 'display_name LIKE' => $search_product . '%'])->orderAsc('price')->toArray();
                        } else {
                            $products = $this->Products->find()->where(['category_id in (SELECT id FROM categories WHERE published = 1)', 'published' => 1])->orderAsc('price')->toArray();
                        }
                    }

                    $getUser = $this->getComponent('CommonFunction')->getUserInfo();

                    if (!empty($products)) {

                        foreach ($products as $product) {

                            $getFavoriteProduct = $this->FavouritesProduct->find()->where(['product_id' => $product['id'], 'user_id' => $getUser['id']])->first();

                            if (!empty($getFavoriteProduct)) {
                                $product['favorite'] = true;
                            } else {
                                $product['favorite'] = false;
                            }

                            $images = $this->ProductImages->find()->where(['product_id' => $product['id']])->toArray();
                            if (!empty($images)) {
                                $imageArray = [];
                                $fullUrl = Router::fullBaseUrl();
                                foreach ($images as $image) {
                                    $imageArray[] = $fullUrl . '/' . $image['image_path'];
                                }
                                $product['image'] = $imageArray;
                            }
                        }
                    }

                    if (!empty($products)) {
                        return $this->getResponse()
                            ->withStatus(200)
                            ->withType('application/json')
                            ->withStringBody(json_encode(array(
                                'status' => 'success',
                                'products' => $products,
                                'mode' => $this->mode)));
                    } else {
                        return $this->getResponse()
                            ->withStatus(200)
                            ->withType('application/json')
                            ->withStringBody(json_encode(array(
                                'status' => 'success',
                                'products' => array(),
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

    public function getProduct() {

        if ($this->AccessToken->verify()) {

            if ($this->request->is('post')) {

                $request_data = $this->request->getQueryParams();

                if (!empty($request_data)) {
                    $product_id = isset($request_data['product_id']) ? $request_data['product_id']:'';
                    $user_id = isset($request_data['user_id']) ? $request_data['user_id']:'';

                    $fullUrl = Router::fullBaseUrl();
                    $product = $this->Products->find()->where(['id' => $product_id, 'published' => 1])->first();
                    $getUser = $this->getComponent('CommonFunction')->getUserInfo();

                    $getFavoriteProduct = $this->FavouritesProduct->find()->where(['product_id' => $product['id'], 'user_id' => $getUser['id']])->first();

                    if (!empty($getFavoriteProduct)) {
                        $product['favorite'] = true;
                    } else {
                        $product['favorite'] = false;
                    }

                    if (!empty($product)) {
                        $images = $this->ProductImages->find()->where(['product_id' => $product['id']])->toArray();
                        if (!empty($images)) {
                            $imageArray = [];
                            foreach ($images as $image) {
                                $imageArray[] = $fullUrl . '/' . 'img' . DS . 'product_images' . DS . $image['image_path'];
                            }
                            $product['image'] = $imageArray;
                        }
                    }

                    $this->getComponent('Product')->recentlyViewSave($product_id, $getUser['id']);

                    if (!empty($product)) {
                        return $this->getResponse()
                            ->withStatus(200)
                            ->withType('application/json')
                            ->withStringBody(json_encode(array(
                                'status' => 'success',
                                'products' => $product,
                                'mode' => $this->mode)));
                    } else {
                        return $this->getResponse()
                            ->withStatus(200)
                            ->withType('application/json')
                            ->withStringBody(json_encode(array(
                                'status' => 'success',
                                'products' => array(),
                                'mode' => $this->mode)));
                    }
                } else {
                    return $this->getResponse()
                        ->withStatus(200)
                        ->withType('application/json')
                        ->withStringBody(json_encode(array(
                            'status' => 'error',
                            'msg' => 'Missing Input Data!',
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

    public function getAddress(): Response {

        if ($this->AccessToken->verify()) {
            if ($this->request->is('post')) {

                $getUser = $this->getComponent('CommonFunction')->getUserInfo();
                $productDeliveryAddress = $this->ProductDeliveryAddress->find()->where(['user_id="' . $getUser['id'].'" OR user_id IS NULL'])->toArray();

                if (!empty($productDeliveryAddress)) {
                    return $this->getResponse()
                        ->withStatus(200)
                        ->withType('application/json')
                        ->withStringBody(json_encode(array(
                            'status' => 'success',
                            'address_list' => $productDeliveryAddress,
                            'mode' => $this->mode)));
                } else {
                    return $this->getResponse()
                        ->withStatus(200)
                        ->withType('application/json')
                        ->withStringBody(json_encode(array(
                            'status' => 'success',
                            'address_list' => array(),
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

    public function addToFavouriteProduct(): Response {

        if ($this->AccessToken->verify()) {

            if ($this->request->is('post')) {

                $request_data = file_get_contents("php://input");
                $request_data = $this->json_decode($request_data, true);
                $this->log($request_data);

                if (!empty($request_data)) {

                    $product_id = isset($request_data['Product']['product_id']) ? $request_data['Product']['product_id'] : '';
                    $user_id = isset($request_data['Product']['user_id']) ? $request_data['Product']['user_id'] : '';
                    $favourite = isset($request_data['Product']['favourite']) ? $request_data['Product']['favourite'] : '';

                    $errorMessage = [];
                    if (empty($product_id)) {
                        $errorMessage[] = ['Required field product id is missing'];
                    }
                    if (empty($user_id)) {
                        $errorMessage[] = ['Required field user id is missing'];
                    }
                    if (empty($favourite)) {
                        $errorMessage[] = ['Required favourite is missing'];
                    }

                    if (empty($user_id)) {
                        $getUser = $this->getComponent('CommonFunction')->getUserInfo();
                        $user_id = $getUser['id'];
                    }

                    $getFavoriteProduct = $this->FavouritesProduct->find()->where(['product_id' => $product_id, 'user_id' => $user_id])->first();

                    if (!empty($getFavoriteProduct)) {
                        if ($this->FavouritesProduct->delete($getFavoriteProduct)) {
                            return $this->getResponse()
                                ->withStatus(200)
                                ->withType('application/json')
                                ->withStringBody(json_encode(array(
                                    'status' => 'success',
                                    'msg' => true,
                                    'mode' => $this->mode)));
                        }
                    }

                    $getFavourite = $this->getComponent('Product')->saveFavouritesProduct($product_id, $user_id, $favourite);

                    if (!empty($getFavourite)) {
                        return $this->getResponse()
                            ->withStatus(200)
                            ->withType('application/json')
                            ->withStringBody(json_encode(array(
                                'status' => 'success',
                                'msg' => true,
                                'mode' => $this->mode)));
                    } else {
                        return $this->getResponse()
                            ->withStatus(404)
                            ->withType('application/json')
                            ->withStringBody(json_encode(array(
                                'status' => 'error',
                                'msg' => $errorMessage,
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

    public function getFavouriteProducts(): Response {
        if ($this->AccessToken->verify()) {
            if ($this->request->is('post')) {

                $request_data = $this->request->getQueryParams();

                if (!empty($request_data)) {
                    $user_id = isset($request_data['user_id']) ? $request_data['user_id']:'';

                    $getUser = $this->getComponent('CommonFunction')->getUserInfo();

                    $products = $this->Products->find()->where(['id in (SELECT product_id FROM favourites_product WHERE user_id = '.$getUser['id'].')', 'published' => 1])->orderDesc('id')->toArray();

                    $fullUrl = Router::fullBaseUrl();
                    foreach ($products as $product) {
                        $images = $this->ProductImages->find()->where(['product_id' => $product['id']])->toArray();
                        if (!empty($images)) {
                            $imageArray = [];
                            foreach ($images as $image) {
                                $imageArray[] = $fullUrl . '/' . 'img' . DS . 'product_images' . DS . 'thumb'. DS . $image['image_path'];
                                break;
                            }
                            $product['image'] = $imageArray;
                        }
                        $product['favorite'] = true;
                    }

                    if (!empty($products)) {
                        return $this->getResponse()
                            ->withStatus(200)
                            ->withType('application/json')
                            ->withStringBody(json_encode(array(
                                'status' => 'success',
                                'products' => $products,
                                'mode' => $this->mode)));
                    } else {
                        return $this->getResponse()
                            ->withStatus(200)
                            ->withType('application/json')
                            ->withStringBody(json_encode(array(
                                'status' => 'success',
                                'products' => array(),
                                'mode' => $this->mode)));
                    }
                } else {
                    return $this->getResponse()
                        ->withStatus(200)
                        ->withType('application/json')
                        ->withStringBody(json_encode(array(
                            'status' => 'error',
                            'msg' => 'Missing Input Data!',
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

    public function getRecentlyView(): Response {

        if ($this->AccessToken->verify()) {
            if ($this->request->is('post')) {

                $request_data = $this->request->getQueryParams();

                if (!empty($request_data)) {
                    $user_id = isset($request_data['user_id']) ? $request_data['user_id'] : '';

                    $getUser = $this->getComponent('CommonFunction')->getUserInfo();
//                    $products = $this->Products->find()->where(['id in (SELECT product_id FROM product_recently_view WHERE user_id = ' . $getUser['id'] . ' order by date_time asc)', 'published' => 1])->limit(20)->toArray();
//                    $products = $this->ProductRecentlyView->find()->where(['id in (SELECT id FROM products WHERE id in (SELECT product_id FROM product_recently_view WHERE user_id = ' . $getUser['id'] . ' order by date_time asc))', 'published' => 1])->orderDesc('date_time')->limit(20)->toArray();
//                     $join = array(
//                         'table' => $this->ProductRecentlyView,
//                         'alias' => 'ProductRecentlyView',
//                         'type' => 'LEFT',
//                         'confitions' => array('Products.id = ProductRecentlyView.product_id')
//                     );

                    $connection = ConnectionManager::get('default');

                    $sql = 'SELECT Products.id, Products.category_id, Products.title title, Products.display_name, Products.image, Products.slug, Products.description, Products.price, Products.published
                            FROM products Products
                            LEFT JOIN product_recently_view recentlyView
                            ON Products.id = recentlyView.product_id
                            WHERE
                            recentlyView.user_id = ' . $getUser['id'] . ' ORDER BY recentlyView.date_time desc ;';

                    $products = $connection->execute($sql)->fetchAll('assoc');

                    $fullUrl = Router::fullBaseUrl();
                    foreach ($products as $key => $product) {

                        $getFavoriteProduct = $this->FavouritesProduct->find()->where(['product_id' => $product['id'], 'user_id' => $getUser['id']])->first();
                        if (!empty($getFavoriteProduct)) {
                            $product['favorite'] = true;
                        } else {
                            $product['favorite'] = false;
                        }

                        $images = $this->ProductImages->find()->where(['product_id' => $product['id']])->toArray();
                        if (!empty($images)) {
                            $imageArray = [];
                            foreach ($images as $image) {
                                $imageArray[] = $fullUrl . '/' . 'img' . DS . 'product_images' . DS . 'thumb'. DS . $image['image_path'];
                                break;
                            }
                            $product['image'] = $imageArray;
                        }
                        $products[$key] = $product;
                    }

                    if (!empty($products)) {
                        return $this->getResponse()
                            ->withStatus(200)
                            ->withType('application/json')
                            ->withStringBody(json_encode(array(
                                'status' => 'success',
                                'products' => $products,
                                'mode' => $this->mode)));
                    } else {
                        return $this->getResponse()
                            ->withStatus(200)
                            ->withType('application/json')
                            ->withStringBody(json_encode(array(
                                'status' => 'success',
                                'products' => array(),
                                'mode' => $this->mode)));
                    }
                } else {
                    return $this->getResponse()
                        ->withStatus(200)
                        ->withType('application/json')
                        ->withStringBody(json_encode(array(
                            'status' => 'error',
                            'msg' => 'Missing Input Data!',
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

    public function profileImageChange () {

        if ($this->AccessToken->verify()) {

            if ($this->request->is('post')) {
                $request_data = $this->request->getData();
                $this->log($request_data);

                if (!empty($request_data)) {
                    $image = isset($request_data['image']) ? $request_data['image'] : '';
                    $user_id = isset($request_data['user_id']) ? $request_data['user_id'] : '';

                    $errorMessage = [];
                    if (empty($image)) {
                        $errorMessage[] = ['Required field image is missing'];
                    }
                    if (empty($user_id)) {
                        $errorMessage[] = ['Required field user id is missing'];
                    }

                    if (count($errorMessage) == 0) {
                        $fullUrl = Router::fullBaseUrl();
                        $getUser = $this->getComponent('CommonFunction')->getUserInfo();
                        $users = $this->Users->find()->where(['id' => $getUser['id']])->first();

                        if (!empty($users['image'])) {
                            if (is_file(WWW_ROOT . $users['image'])) {
                                unlink(WWW_ROOT . $users['image']);
                            }
                        }
                        $extension=array("jpeg","jpg","png");
                        $file_name= $image->getClientFilename();
                        $ext = pathinfo($file_name,PATHINFO_EXTENSION);

                        $image_name = $users['id'] . '-main-image-' . strtotime(date('Y-m-d H:i:s'));
                        $targetPath = WWW_ROOT . 'img' . DS . 'profile_images' . DS . $image_name . '.' . strtolower($ext);

                        if(in_array(strtolower($ext),$extension)) {
                            if(!file_exists($targetPath)) {
                                $image->moveTo($targetPath);
                                $saveImage['image'] = 'img' . DS . 'profile_images' . DS . $image_name . '.' . strtolower($ext);
                                $users = $this->Users->patchEntity($users, $saveImage);
                                $getUserInfo = $this->Users->save($users);
                                if ($getUserInfo->id) {

                                    $getUserInfo['image'] = $fullUrl . '/' . $getUserInfo['image'];
                                    return $this->getResponse()
                                        ->withStatus(200)
                                        ->withType('application/json')
                                        ->withStringBody(json_encode(array(
                                            'status' => 'success',
                                            'user' => $getUserInfo,
                                            'mode' => $this->mode)));

                                } else {
                                    return $this->getResponse()
                                        ->withStatus(404)
                                        ->withType('application/json')
                                        ->withStringBody(json_encode(array(
                                            'status' => 'error',
                                            'msg' => 'Not Save!',
                                            'mode' => $this->mode)));
                                }
                            }
                        }
                    } else {
                        return $this->getResponse()
                            ->withStatus(404)
                            ->withType('application/json')
                            ->withStringBody(json_encode(array(
                                'status' => 'error',
                                'msg' => $errorMessage,
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

    public function updateProfileInfo () {

        if ($this->AccessToken->verify()) {

            if ($this->request->is('post')) {

                $request_data = file_get_contents("php://input");
                $request_data = $this->json_decode($request_data, true);
                $this->log($request_data);

                if (!empty($request_data)) {
                    $user_id = isset($request_data['User']['user_id']) ? $request_data['User']['user_id'] : '';
                    $display_name = isset($request_data['User']['display_name']) ? $request_data['User']['display_name'] : '';
                    $phone_no = isset($request_data['User']['phone_no']) ? $request_data['User']['phone_no'] : '';
                    $address = isset($request_data['User']['address']) ? $request_data['User']['address'] : '';
                    $bio = isset($request_data['User']['bio']) ? $request_data['User']['bio'] : '';

                    $fullUrl = Router::fullBaseUrl();
                    $getUser = $this->getComponent('CommonFunction')->getUserInfo();
                    $users = $this->Users->find()->where(['id' => $getUser['id']])->first();
                    if (!empty($users)) {
                        $updateUserinfo['display_name'] = $display_name ?? '';
                        $updateUserinfo['phone_no'] = $phone_no ?? '';
                        $updateUserinfo['address'] = $address ?? '';
                        $updateUserinfo['bio'] = $bio ?? '';
                        $users = $this->Users->patchEntity($users, $updateUserinfo);
                        $getUserInfo = $this->Users->save($users);
                        if ($getUserInfo->id) {
                            $getUserInfo['image'] = $fullUrl . '/' . $getUserInfo['image'] ?? '';
                            return $this->getResponse()
                                ->withStatus(200)
                                ->withType('application/json')
                                ->withStringBody(json_encode(array(
                                    'status' => 'success',
                                    'user' => $getUserInfo,
                                    'mode' => $this->mode)));
                        }
                    } else {
                        return $this->getResponse()
                            ->withStatus(200)
                            ->withType('application/json')
                            ->withStringBody(json_encode(array(
                                'status' => 'error',
                                'msg' => 'User Not Exist!',
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

    public function saveAddress() {

        if ($this->AccessToken->verify()) {

            if ($this->request->is('post')) {

                $request_data = file_get_contents("php://input");
                $request_data = $this->json_decode($request_data, true);
                $this->log($request_data);

                if (!empty($request_data)) {
                    $user_id = isset($request_data['Address']['user_id']) ? $request_data['Address']['user_id'] : '';
                    $address_name = isset($request_data['Address']['name']) ? $request_data['Address']['name'] : '';

                    $errorMessage = [];
                    if (empty($user_id)) {
                        $errorMessage[] = ['Required field user id is missing'];
                    }
                    if (empty($address_name)) {
                        $errorMessage[] = ['Required field address name is missing'];
                    }

                    if (count($errorMessage) == 0) {
                        $getUser = $this->getComponent('CommonFunction')->getUserInfo();
                        $productDeliveryAddress = $this->ProductDeliveryAddress->find()->where(['user_id' => $getUser['id'], 'address_line' => $address_name])->first();
                        if (empty($productDeliveryAddress)) {
                            $productDeliveryAddress = $this->ProductDeliveryAddress->newEmptyEntity();
                            $newAddress['user_id'] = $getUser['id'];
                            $newAddress['address_line'] = $address_name;
                            $productDeliveryAddress = $this->ProductDeliveryAddress->patchEntity($productDeliveryAddress, $newAddress);
                            $getProductDeliveryAddress = $this->ProductDeliveryAddress->save($productDeliveryAddress);
                            if ($getProductDeliveryAddress->id) {
                                return $this->getResponse()
                                    ->withStatus(200)
                                    ->withType('application/json')
                                    ->withStringBody(json_encode(array(
                                        'status' => 'success',
                                        'address' => $getProductDeliveryAddress,
                                        'mode' => $this->mode)));
                            }
                        } else {
                            return $this->getResponse()
                                ->withStatus(200)
                                ->withType('application/json')
                                ->withStringBody(json_encode(array(
                                    'status' => 'error',
                                    'msg' => 'Address Already Exist!',
                                    'mode' => $this->mode)));
                        }
                    } else {
                        return $this->getResponse()
                            ->withStatus(404)
                            ->withType('application/json')
                            ->withStringBody(json_encode(array(
                                'status' => 'error',
                                'msg' => $errorMessage,
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

    public function getUserInfo() {

        if ($this->AccessToken->verify()) {

            if ($this->request->is('post')) {

                $getUser = $this->getComponent('CommonFunction')->getUserInfo();

                if (!empty($getUser)) {
                    $fullUrl = Router::fullBaseUrl();
                    if (!empty($getUser['image'])) {
                        $getUser['image'] = $fullUrl . DS . $getUser['image'];
                    }

                    $getOrder = $this->Orders->find()->where(['order_stage' => OrderStage::PROCESSING, 'user_id' => $getUser['id']])->enableHydration(false)->count();
                    if (!empty($getOrder)) {
                        $getUser['tree_planted'] = $getOrder;
                    } else {
                        $getUser['tree_planted'] = 0;
                    }

                    $getFavorites = $this->FavouritesProduct->find()->where(['user_id' => $getUser['id']])->enableHydration(false)->count();

                    if (!empty($getFavorites)) {
                        $getUser['favorites'] = $getFavorites;
                    } else {
                        $getUser['favorites'] = 0;
                    }

                    return $this->getResponse()
                        ->withStatus(200)
                        ->withType('application/json')
                        ->withStringBody(json_encode(array(
                            'status' => 'success',
                            'user' => $getUser,
                            'mode' => $this->mode)));
                } else {
                    return $this->getResponse()
                        ->withStatus(200)
                        ->withType('application/json')
                        ->withStringBody(json_encode(array(
                            'status' => 'success',
                            'user' => array(),
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

    public function changePassword() {

        if ($this->AccessToken->verify()) {

            if ($this->request->is('post')) {

                $request_data = file_get_contents("php://input");
                $request_data = $this->json_decode($request_data, true);
                $this->log($request_data);

                if (!empty($request_data)) {
                    $current_password = isset($request_data['User']['current_password']) ? $request_data['User']['current_password'] : '';
                    $new_password = isset($request_data['User']['new_password']) ? $request_data['User']['new_password'] : '';

                    $errorMessage = [];
                    if (empty($current_password)) {
                        $errorMessage[] = ['Required field current password is missing'];
                    }
                    if (empty($new_password)) {
                        $errorMessage[] = ['Required field new password is missing'];
                    }

                    if (count($errorMessage) == 0) {

                        $getUser = $this->getComponent('CommonFunction')->getUserInfo();

                        $users = $this->Users->find()->where(['id' => $getUser['id'], 'password' => Security::hash($current_password, null, true)])->first();

                        if (!empty($users)) {
                            $user['password'] = Security::hash($new_password, null, true);

                            $users = $this->Users->patchEntity($users, $user);
                            $users = $this->Users->save($users);

                            if (!empty($users)) {
                                $fullUrl = Router::fullBaseUrl();
                                if (!empty($users['image'])) {
                                    $users['image'] = $fullUrl . DS . $users['image'];
                                }

                                return $this->getResponse()
                                    ->withStatus(200)
                                    ->withType('application/json')
                                    ->withStringBody(json_encode(array(
                                        'status' => 'success',
                                        'user' => $users,
                                        'mode' => $this->mode)));
                            }

                        }
                        else {
                            return $this->getResponse()
                                ->withStatus(200)
                                ->withType('application/json')
                                ->withStringBody(json_encode(array(
                                    'status' => 'success',
                                    'user' => 'Current Password Not Match!',
                                    'mode' => $this->mode)));
                        }
                    } else {
                        return $this->getResponse()
                            ->withStatus(404)
                            ->withType('application/json')
                            ->withStringBody(json_encode(array(
                                'status' => 'error',
                                'msg' => $errorMessage,
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
