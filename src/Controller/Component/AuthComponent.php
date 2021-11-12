<?php

namespace App\Controller\Component;

use App\Traits\PolyfillSubTrait;
use App\Traits\PolyfillTrait;
use Authentication\Authenticator\SessionAuthenticator;
use Authentication\Authenticator\StatelessInterface;
use Authentication\Controller\Component\AuthenticationComponent;
use Authentication\Identifier\IdentifierInterface;
use Cake\ORM\Entity;
use OAuthServer\Auth\OAuthAuthenticator;


class AuthComponent extends AuthenticationComponent
{
    use PolyfillSubTrait;

    private static $_instance = null;

    public $controller = null;

    public function initialize(array $config = []): void
    {
        parent::initialize($config);
        self::$_instance = &$this;

    }

    public static function getInstance()
    {
        return self::$_instance;
    }


//    function user($attr=null)
//    {
//        $user = $this->getIdentity()->getOriginalData();
//        if($attr!==null){
//            return $user->{$attr}??null;
//        }
//
//        return $user;
//    }

    function user($attr=null)
    {
        $user = $this->getIdentity();

        if(empty($user)){
            return null;
        }
        $user = $user->getOriginalData();
        if($attr!==null){
            return $user->{$attr}??null;
        }

        return $user;
    }


    function setUser($user=null, $update_session=true)
    {
        if(is_array($user)){
            $user = new Entity($user);
        } else if($user === null){
            $user = $this->user();
        }

        if(empty($user)){
            return $this;
        }

        $authSessionKey = $this->getAuthSessionKey();
        if($update_session){
            $this->sessionDelete($authSessionKey);
        }

        $this->setIdentity($user);

        if($this->getAuthenticationService()->getAuthenticationProvider() instanceof StatelessInterface){
            $this->sessionDelete($authSessionKey);
        }

        return $this;
    }


    public function allow($actions=null)
    {
        if($actions!==null){
            if(!is_array($actions)){
                $actions = [$actions];
            }
            parent::addUnauthenticatedActions((array)$actions);
        }

        return $this;
    }

    public function getAuthSessionKey()
    {
        $authenticators = $this->getAuthenticationService()->authenticators();

        if($authenticators!==null){
            foreach ($authenticators as $authenticator) {
                if ($authenticator instanceof SessionAuthenticator) {
                    return $authenticator->getConfig('sessionKey');
                }
            }
        }

        return null;
    }

    public function loadWebAuthenticator()
    {
        $authenticationService = $this->getAuthenticationService();
        // Define where users should be redirected to when they are not authenticated
        $authenticationService->setConfig([
            'unauthenticatedRedirect' => '/login',
            'queryParam' => 'redirect',
        ]);


        // Load the authenticators. Session should be first.
        $this->loadSessionAuthenticator();
        $this->loadFormAuthenticator();
    }

    public function setAuthConfig($c = [])
    {
        $authenticationService = $this->getAuthenticationService();
        // Define where users should be redirected to when they are not authenticated
        $authenticationServiceConf = [
            'unauthenticatedRedirect' => $conf['unauthenticatedRedirect'] ?? $authenticationService->getConfig('unauthenticatedRedirect') ?: '/login',
            'queryParam' => $conf['queryParam'] ?? $authenticationService->getConfig('queryParam') ?? 'redirect',
        ];


        var_dump($authenticationServiceConf); die;

        $authenticationService->setConfig($authenticationServiceConf);


    }

    public function loadOAuth2Authenticator()
    {
        $authenticationService = $this->getAuthenticationService();
        // Define where users should be redirected to when they are not authenticated
        $authenticationService->setConfig([
            'unauthenticatedRedirect' => '/login',
            'queryParam' => 'redirect',
        ]);
        $authenticationService->loadAuthenticator(OAuthAuthenticator::class);
    }

    public function loadSessionAuthenticator()
    {
        $authenticationService = $this->getAuthenticationService();
        $authenticationService->loadAuthenticator('Authentication.Session');
    }


    public function loadFormAuthenticator()
    {
        $authenticationService = $this->getAuthenticationService();
        $fields = $this->getFormAuthenticatorFields();
        $authenticationService->loadAuthenticator('Authentication.Form', [
            'fields' => $fields,
            'loginUrl' => '/login'
        ]);

        // Load identifiers
        $authenticationService->loadIdentifier('Authentication.Password', compact('fields'));
    }


    public function getFormAuthenticatorFields($fields=[])
    {
        return [
            IdentifierInterface::CREDENTIAL_USERNAME => $fields[IdentifierInterface::CREDENTIAL_USERNAME]??'email',
            IdentifierInterface::CREDENTIAL_PASSWORD => $fields[IdentifierInterface::CREDENTIAL_PASSWORD]??'password',
        ];
    }
}