<?php
namespace OAuthServer\Auth;

use Authentication\UrlChecker\UrlCheckerTrait;
use Cake\Core\App;
use Cake\Database\Exception;
use Cake\Event\Event;
use Cake\Event\EventManager;
use Cake\Network\Exception\BadRequestException;
use Cake\Network\Exception\HttpException;
use Cake\Network\Response;
use Cake\ORM\Locator\TableLocator;
use Cake\ORM\TableRegistry;
use League\OAuth2\Server\Exception\OAuthException;
use OAuthServer\Traits\GetStorageTrait;

use Authentication\Authenticator\AbstractAuthenticator;
use Psr\Http\Message\ServerRequestInterface;
use Authentication\Authenticator\ResultInterface;
use Authentication\Authenticator\Result;
use Authentication\Identifier\IdentifierInterface;
use Authentication\Authenticator\StatelessInterface;
use Cake\Datasource\ConnectionManager;
use Cake\Datasource\FactoryLocator; //RM


class OAuthAuthenticator extends AbstractAuthenticator implements StatelessInterface
{
    use UrlCheckerTrait;
    use GetStorageTrait;


    /**
     * @var \League\OAuth2\Server\ResourceServer
     */
    public $Server;

    /**
     * Exception that was thrown by oauth server
     *
     * @var \League\OAuth2\Server\Exception\OAuthException
     */
    protected $_exception;

    /**
     * @var array
     */
    protected $_defaultConfig = [
        'continue' => false,
        'storages' => [
            'session' => [
                'className' => 'OAuthServer.Session'
            ],
            'accessToken' => [
                'className' => 'OAuthServer.AccessToken'
            ],
            'client' => [
                'className' => 'OAuthServer.Client'
            ],
            'scope' => [
                'className' => 'OAuthServer.Scope'
            ]
        ],
        'resourceServer' => [
            'className' => 'League\OAuth2\Server\ResourceServer'
        ],
        'contain' => null,
        'header' => 'Authorization',
        'header_access_token' => 'access_token',
        'queryParam' => 'access_token',
        'tokenPrefix' => 'Bearer',
    ];

    /**
     * @param \Cake\Controller\ComponentRegistry $registry Component registry
     * @param array $config Config array
     */

    public function __construct(IdentifierInterface $identifier, array $config = [])
    {
        parent::__construct($identifier, $config);

        if ($this->getConfig('server')) {
            $this->Server = $this->getConfig('server');

            return;
        }

        $serverConfig = $this->getConfig('resourceServer');
        $serverClassName = App::className($serverConfig['className']);

        if (!$serverClassName) {
            throw new Exception('ResourceServer class was not found.');
        }

        $server = new $serverClassName(
            $this->_getStorage('session'),
            $this->_getStorage('accessToken'),
            $this->_getStorage('client'),
            $this->_getStorage('scope')
        );

        $this->Server = $server;


    }


    /**
     * Checks if the token is in the headers or a request parameter
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request The request that contains login information.
     * @return string|null
     */
    protected function getToken(ServerRequestInterface $request): ?string
    {
        $token = $this->getTokenFromHeader($request, $this->getConfig('header'));
        if ($token !== null) {
            $prefix = $this->getConfig('tokenPrefix');
            if ($prefix !== null && is_string($token) && strpos($token, $prefix." ") === 0) {
                $token =  trim($this->stripTokenPrefix($token, $prefix));
            } else {
                $token = null;
            }
        }


        if ($token === null) {
            $token = $this->getTokenFromHeader($request, $this->getConfig('header_access_token'));
        }


//        Authorization: Bearer {{access_token}}
//        access_token: {{access_token}}
//        http://cake4oauth.com/?access_token={{access_token}}

        if ($token === null) {
            $token = $this->getTokenFromQuery($request, $this->getConfig('queryParam'));
        }

        $prefix = $this->getConfig('tokenPrefix');
        if ($prefix !== null && is_string($token)) {
            return $this->stripTokenPrefix($token, $prefix);
        }

        return $token;
    }

    /**
     * Strips a prefix from a token
     *
     * @param string $token Token string
     * @param string $prefix Prefix to strip
     * @return string
     */
    protected function stripTokenPrefix(string $token, string $prefix): string
    {
        return str_ireplace($prefix . ' ', '', $token);
    }

    protected function normalizeHeaderName(string $name): string
    {
        $name = str_replace('-', '_', strtoupper($name));
        if (!in_array($name, ['CONTENT_LENGTH', 'CONTENT_TYPE'], true)) {
            $name = 'HTTP_' . $name;
        }

        return $name;
    }

    /**
     * Gets the token from the request headers
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request The request that contains login information.
     * @param string|null $headerLine Header name
     * @return string|null
     */
    protected function getTokenFromHeader(ServerRequestInterface $request, ?string $headerLine): ?string
    {
//        .htaccess
//        # Handle Authorization Header
//        RewriteCond %{HTTP:Authorization} .
//        RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]
//        RewriteCond %{HTTP:access_token} .
//        RewriteRule .* - [E=ACCESS_TOKEN:%{HTTP:access_token}]




        if (!empty($headerLine)) {
            $header = $request->getHeaderLine($headerLine);

            $headerLineName = $this->normalizeHeaderName($headerLine);
            $header1 = $request->getEnv('REDIRECT_'.$headerLineName);
            $header2 = $request->getEnv('REDIRECT_REDIRECT_'.$headerLineName);
            $header3 = $request->getEnv('REDIRECT_'.strtoupper($headerLine));

            if (!empty($header)) {
                return $header;
            } else if (!empty($header1)) {
                return $header1;
            } else if (!empty($header2)) {
                return $header2;
            } else if (!empty($header3)) {
                return $header3;
            } else if (!empty($header4)) {
                return $header4;
            }
        }

        return null;
    }

    /**
     * Gets the token from the request headers
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request The request that contains login information.
     * @param string $queryParam Request query parameter name
     * @return string|null
     */
    protected function getTokenFromQuery(ServerRequestInterface $request, string $queryParam): ?string
    {
        $queryParams = $request->getQueryParams();

        if (empty($queryParams[$queryParam])) {
            return null;
        }

        return $queryParams[$queryParam];
    }


    /**
     * Authenticate a user based on the request information.
     *
     * @param \Cake\Network\Request $request Request to get authentication information from.
     * @param \Cake\Network\Response $response A response object that can have headers added.
     * @return bool
     */

    public function authenticate(ServerRequestInterface $request): ResultInterface
    {
        $user = $this->getUser($request);

        if (empty($user)) {
            return new Result(null, Result::FAILURE_IDENTITY_NOT_FOUND, $this->_identifier->getErrors());
        }

        return new Result($user, Result::SUCCESS);
    }

    /**
     * @param \Cake\Network\Request $request Request to get authentication information from.
     * @param \Cake\Network\Response $response A response object that can have headers added.
     * @return bool|\Cake\Network\Response
     */
    public function unauthenticated(ServerRequestInterface $request, Response $response)
    {
        if ($this->_config['continue']) {
            return false;
        }
        if (isset($this->_exception)) {
            // ignoring $e->getHttpHeaders() for now
            // it only sends WWW-Authenticate header in case of InvalidClientException
            throw new \Cake\Http\Exception\HttpException($this->_exception->getMessage(), $this->_exception->httpStatusCode, $this->_exception);
        }
        $message = __d('authenticate', 'You are not authenticated.');
        throw new \Cake\Http\Exception\BadRequestException($message);
    }

    /**
     * @param \Cake\Network\Request $request Request object
     * @return array|bool|mixed
     */
    public function getUser(ServerRequestInterface $request)
    {
        try {
            $this->Server->isValidRequest(true, $this->getToken($request));
        } catch (OAuthException $e) {
            $this->_exception = $e;

            return false;
        }

        $ownerModel = $this->Server
            ->getAccessToken()
            ->getSession()
            ->getOwnerType();

        $ownerId = $this->Server
            ->getAccessToken()
            ->getSession()
            ->getOwnerId();

        $options = [];

        if ($this->_config['contain']) {
            $options['contain'] = $this->_config['contain'];
        }

        // $database       = ConnectionManager::getConfig('default')['database'];
        // $databaseOrg    = ConnectionManager::getConfig('organizations')['database'];
        // $this->table_prefix     = $database.   '.'. ConnectionManager::getConfig('default')['table_prefix'];
        // $this->table_prefixOrg  = $databaseOrg.'.'. ConnectionManager::getConfig('organizations')['table_prefix'];

        //$this->table_prefix  = ConnectionManager::getConfig('organizations')['table_prefix'];

        $con = ConnectionManager::get('default');
        $userTble = TableRegistry::getTableLocator()->get($ownerModel); //RM
        $userTble = $userTble->setConnection($con);
        
        $owner = $userTble->get($ownerId, $options)
        ->toArray();
        
        //$owner = FactoryLocator::get('Table')->get($ownerModel)
        //     ->get($ownerId, $options)
        //     ->toArray()
        // ;
        //pr($owner); die('vvvvvvvv');

        $event = new Event('OAuthServer.getUser', $request, [$ownerModel, $ownerId, $owner]);
        EventManager::instance()->dispatch($event);

        if ($event->getResult() !== null) {
            return $event->getResult();
        } else {
            return $owner;
        }
    }



    /**
     * No-op method.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request A request object.
     * @return void
     */
    public function unauthorizedChallenge(ServerRequestInterface $request): void
    {
    }
}
