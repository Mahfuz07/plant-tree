<?php
namespace OAuthServer\Controller;

use App\Controller\AppController;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\Log\Log;
use League\OAuth2\Server\Exception\AccessDeniedException;
use League\OAuth2\Server\Exception\OAuthException;
use League\OAuth2\Server\Util\RedirectUri;
use OAuthServer\Grant\WebPortalsGrant;

use App\Controller\Component\AuthComponent;
use Authentication\Controller\Component\AuthenticationComponent;

use Cake\I18n\Time;
use Cake\Network\Exception\HttpException;
use Cake\Network\Response;
use Cake\ORM\Query;
use League\OAuth2\Server\Grant\AuthCodeGrant;
use Cake\Event\EventInterface;


/**
 * Class OAuthController
 *
 * @property \OAuthServer\Controller\Component\OAuthComponent $OAuth
 */
class OAuthController extends AppController
{
    /**
     * @return void
     */
    public function initialize(): void
    {
        parent::initialize();
        $this->loadComponent('OAuthServer.OAuth', (array)Configure::read('OAuthServer'));
        $this->loadComponent('RequestHandler');
    }

    /**
     * @param \Cake\Event\Event $event Event object.
     * @return void
     */
    public function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event);
        /*if ($this->Auth) {
            $this->Auth->allow(['oauth', 'authorize', 'accessToken','webPortalAccessToken']);
        }*/
        if (!$this->components()->has('Authentication')) {
            if (!$this->components()->has('Auth') || !($this->Auth instanceof AuthenticationComponent)) {
                throw new \RuntimeException("OAuthServer requires Authentication component to be loaded and properly configured");
            } else {
                $this->Authentication = $this->Auth;
            }
        }
        $this->Authentication->addUnauthenticatedActions(['oauth', 'authorize', 'accessToken','webPortalAccessToken']);

        $actions =  array(
            'accessToken',
            'webPortalAccessToken'
        );
        $this->Security->setConfig('unlockedActions', $actions);

        if ($this->request->getParam('action') == 'authorize') {
            // OAuth spec requires to check OAuth authorize params as a first thing, regardless of whether user is logged in or not.
            // AuthComponent checks user after beforeFilter by default, this is the place to do it.
            try {
                $this->authCodeGrant = $this->OAuth->Server->getGrantType('authorization_code');

                $this->authParams = $this->authCodeGrant->checkAuthorizeParams();

//                http://cake4oauth.com/oauth/authorize?client_id=1&redirect_uri=http://localhost/cake4_oauth/users&response_type=code
            } catch (OAuthException $e) {
                // ignoring $e->getHttpHeaders() for now
                // it only sends WWW-Authenticate header in case of InvalidClientException
                throw new \Cake\Http\Exception\HttpException($e->getMessage(), $e->httpStatusCode, $e);
            }
        }
    }

    /**
     * @return void
     */
    public function oauth()
    {
        if ($this->OAuth->checkAuthParams('authorization_code')) {
            if (!$this->Auth->user()) {
                $query = $this->getRequestQuery();
                $query['redir'] = 'oauth';

                $this->redirect(
                    [
                        'plugin' => false,
                        'controller' => 'Users',
                        'action' => 'login',
                        '?' => $query
                    ]
                );
            } else {
                $this->redirect(
                    [
                        'action' => 'authorize',
                        '?' => $this->getRequestQuery()
                    ]
                );
            }
        }
    }

    /**
     * @return \Cake\Network\Response|void
     * @throws \League\OAuth2\Server\Exception\InvalidGrantException
     */
    public function authorize()
    {
        if (!$authParams = $this->OAuth->checkAuthParams('authorization_code')) {
            return;
        }

        if (!$this->Auth->user()) {
            $query = $this->getRequestQuery();
            $query['redir'] = 'oauth';

            return $this->redirect(
                [
                    'plugin' => false,
                    'controller' => 'Users',
                    'action' => 'login',
                    '?' => $query
                ]
            );
        }

        $event = new Event('OAuthServer.beforeAuthorize', $this);
        EventManager::instance()->dispatch($event);

        $serializeKeys = [];
        if (is_array($event->getResult())) {
            $this->set($event->getResult());
            $serializeKeys = array_keys($event->getResult());
        }

        if ($this->request->is('post') && $this->request->getData('authorization') === 'Approve') {
            $ownerModel = $this->request->getData('owner_model')?? 'Users';
            $ownerId = $this->request->getData('owner_id')?? $this->Auth->user('id');
            $redirectUri = $this->OAuth->Server->getGrantType('authorization_code')->newAuthorizeRequest($ownerModel, $ownerId, $authParams);
            $event = new Event('OAuthServer.afterAuthorize', $this);
            EventManager::instance()->dispatch($event);
            return $this->redirect($redirectUri);
        } elseif ($this->request->is('post')) {
            $event = new Event('OAuthServer.afterDeny', $this);
            EventManager::instance()->dispatch($event);

            $error = new AccessDeniedException();

            $redirectUri = RedirectUri::make($authParams['redirect_uri'], [
                'error' => $error->errorType,
                'message' => $error->getMessage()
            ]);

            return $this->redirect($redirectUri);
        }

        $this->set('authParams', $authParams);
        $this->set('user', $this->Auth->user());
        $this->set('_serialize', array_merge(['user', 'authParams'], $serializeKeys));
    }

    /**
     * @return void
     */
    public function accessToken()
    {
        try {
            $response = $this->OAuth->Server->issueAccessToken();
            $this->set($response);
            $this->viewBuilder()->setOption('serialize', true);
        } catch (OAuthException $e) {
//            $this->log('http status code :');
//            $this->log($e->httpStatusCode);
//            $headers = $e->getHttpHeaders();
//            array_shift($headers);
//            $this->response->header($headers);
//            $this->set([
//                'error' => $e->errorType,
//                'message' => $e->getMessage()
//            ]);

//             $this->getResponse()
//                ->withStatus($e->getHttpHeaders())
//                ->withType('application/json')
//                ->withStringBody(json_encode(array(
//                        'error' => $e->errorType,
//                        'message' => $e->getMessage()
//                    )));
            $this->set(array(
                'error' => $e->errorType,
                'message' => $e->getMessage()
            ));
            $this->viewBuilder()->setOption('serialize', true);

        }
    }

    /**
     * @return void
     */
    public function webPortalAccessToken()
    {
        try {
            $webPortalGrant = new WebPortalsGrant();

            $this->OAuth->Server->addGrantType($webPortalGrant);
            $response = $this->OAuth->Server->issueAccessToken();
            $this->set($response);
            $this->viewBuilder()->setOption('serialize', true);
        } catch (OAuthException $e) {
            $this->log('Auth Exception :');
            $this->log($e->getMessage());
            throw new \Cake\Http\Exception\HttpException($e->getMessage(), $e->httpStatusCode, $e);
        }
    }
}
