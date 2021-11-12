<?php
namespace App\Traits;


use App\Controller\Component\AuthComponent;
use Cake\I18n\FrozenDate;
use Psr\Log\LogLevel;

trait PolyfillTrait {

    private $isController = null;
    private function isController()
    {
        if($this->isController===null){
            $this->isController = false;
            if(method_exists($this, 'getController')){
                $this->isController = true;
            }

        }
        return $this->isController;
    }

    public function authUser(?string $attr = null, $default = null)
    {
        if(!AuthComponent::getInstance()){
            return $default;
        }

        return AuthComponent::getInstance()->user($attr, $default);
    }


    public function authUserCheck(?string $attr = null)
    {
        if(!AuthComponent::getInstance()){
            return false;
        }
        return AuthComponent::getInstance()->check($attr);
    }


    public function authAllow($attr = null)
    {
        if(AuthComponent::getInstance()){
            AuthComponent::getInstance()->allow($attr);
        }

        return $this;
    }

    public function sessionEngine($class = null, array $options = [])
    {
        return $this->getSession()->engine($class, $options);
    }

    public function sessionOptions(array $options): void
    {
        $this->getSession()->options($options);
    }

    public function sessionStart(): bool
    {
        return $this->getSession()->start();
    }

    public function sessionClose(): bool
    {
        return $this->getSession()->close();
    }

    public function sessionStarted(): bool
    {
        return $this->getSession()->started();
    }

    public function sessionCheck(?string $name = null): bool
    {
        return $this->getSession()->check($name);
    }

    public function sessionRead(?string $name = null, $default = null)
    {
        return $this->getSession()->read($name, $default);
    }

    public function sessionReadOrFail(string $name)
    {
        return $this->getSession()->readOrFail($name);
    }

    public function sessionConsume(string $name)
    {
        return $this->getSession()->consume($name);
    }

    public function sessionWrite($name, $value = null): void
    {
        $this->getSession()->write($name, $value);
    }

    public function sessionId(?string $id = null): string
    {
        return $this->getSession()->id($id);
    }

    public function sessionDelete(string $name): void
    {
        $this->getSession()->delete($name);
    }

    public function sessionDestroy(): void
    {

       $this->getSession()->destroy();
    }

    public function sessionClear(bool $renew = false): void
    {
        $this->getSession()->clear($renew);
    }

    public function sessionRenew(): void
    {
        $this->getSession()->renew();
    }

    public function getSession()
    {
        return $this->getRequest()->getSession();
    }

    public function requestIs($type, ...$args): bool
    {
        return $this->getRequest()->is($type, ...$args);
    }

    public function allowRequestMethod($methods): bool
    {
        return $this->getRequest()->allowMethod($methods);
    }

    public function getRequestParam($name=null, $default = null)
    {
        if($name === null){
            return $this->getRequest()->getAttribute('params');
        }

        $param_val = $this->getRequest()->getParam($name, $default);
        if($name === 'prefix' && !empty($param_val) && is_string($param_val)){
            $param_val = strtolower($param_val);
        }

        return $param_val;
    }

    public function getRequestQuery(string $name=null, $default = null)
    {
        return $this->getRequest()->getQuery($name, $default);
    }

    public function getRequestData($name = null, $default = null)
    {
        if($name !== null){
            $name = (string) $name;
        }
        return $this->getRequest()->getData($name, $default);
    }

    public function setRequestData($data)
    {
        return $this->setRequest($this->getRequest()->withParsedBody($data));
    }

    public function patchRequestData($data=[], $value=null)
    {
        $rdata = $this->getRequestData();
        if(!is_array($data ) && !is_object($data)){
            $keys = explode(".", $data);
            $_rdata = &$rdata;
            $i = 0;
            while(!empty($keys) && $i<100){
                $key = trim(array_shift($keys));
                if($key==""){
                    throw new \InvalidArgumentException("Invalid argument '".$data."' supplied to patchRequestData()");
                }

                if(empty($keys)){
                    if(is_object($_rdata)) {
                        $_rdata->{$key} = $value;
                    } else {
                        $_rdata[$key] = $value;
                    }
                } else {
                    if(is_object($_rdata)) {
                        $_rdata->{$key} = $_rdata->{$key}?? [];
                        if(!is_array($_rdata->{$key}) && !is_object($_rdata->{$key})){
                            $_rdata->{$key} = [];
                        }
                        $_rdata = &$_rdata->{$key};
                    } else {
                        $_rdata[$key] = $_rdata[$key]?? [];
                        if(!is_array($_rdata[$key]) && !is_object($_rdata[$key])){
                            $_rdata[$key] = [];
                        }
                        $_rdata = &$_rdata[$key];
                    }
                }

                ++$i;
            }

        } else {
            $rdata = $data + $rdata;
        }

        return $this->setRequestData($rdata);
    }

    public function unsetRequestData($data=null)
    {
        return $this->setRequest($this->getRequest()->withoutData($data));
    }

    public function clearRequestData($data=null)
    {
        $rdata = $this->getRequestData();
        if(!is_array($data ) && !is_object($data)){
            $keys = explode(".", $data);
            $_rdata = &$rdata;
            $i = 0;
            while(!empty($keys) && $i<100){
                $key = trim(array_shift($keys));
                if($key==""){
//                    throw new \InvalidArgumentException("Invalid argument '".$data."' supplied to patchRequestData()");
                    return;
                }

                if(empty($keys)){
                    if(is_object($_rdata)) {
                        unset($_rdata->{$key});
                    } else {
                        unset($_rdata[$key]);
                    }
                } else {
                    if(is_object($_rdata) && isset($_rdata->{$key})) {
                        $_rdata = &$_rdata->{$key};
                    } else if(is_array($_rdata) && isset($_rdata[$key])) {
                        $_rdata = &$_rdata[$key];
                    } else {
                        return;
                    }
                }

                ++$i;
            }
        }

        return $this->setRequestData($rdata);
    }

    public function getEnv(string $key, ?string $default = null)
    {
        return $this->getRequest()->getEnv($key,  $default);
    }



    public function loadAllComponents($components=[])
    {
        $components = array_unique($components);

        foreach ($components as $c){
            $this->loadComponent($c);

//            try{
//
//            } catch (\Exception $e){
//
//                var_dump($c, $e->getMessage());
//            }
        }
    }


    public function getComponent(string $name, $config = [])
    {
        [, $prop] = pluginSplit($name);
        if(empty($this->{$prop})){
            $this->loadComponent($name, $config);
        }

        return $this->{$prop};
    }


    public function unloadComponent($name)
    {
        [, $prop] = pluginSplit($name);

        try{
            $this->components()->unload($name);
        } catch (\Exception $e){

        }

        unset($this->{$prop});
        return $this;
    }

    public function getDbTable($alias, $options = [])
    {
        return $this->getTableLocator()->get($alias, $options);
    }


    public function dateFormat($date, $format=null, $default=false)
    {
        if(!empty($date) && is_string($date)){
            try{
                $date = new FrozenDate($date);
            } catch (\Exception $e){

            }
        }
        if($date instanceof \DateTimeInterface)
        {
            if(empty($format)){
                $format = 'Y-m-d';
            }
            $d = date_format($date, $format);

            if($d[0] != '-'){
                return $d;
            }
        }

        return $default;
    }


    public function log($message, $level = LogLevel::INFO, $context = []): bool
    {
        if(is_array($message) || is_object($message)){
            $message = print_r($message, true);
        } else if(is_bool($message)){
            $message = $message? 'true':'false';
        } else {
            $message = (string) $message;
        }
        return parent::log($message, $level, $context);
    }

    public function json_encode ($value, $options = 0, $depth = 512)
    {
        if($options===0){
            $options = $options | JSON_INVALID_UTF8_IGNORE;

        }
        return json_encode($value, $options, $depth);
    }

    public function json_decode ($json, $assoc = false, $depth = 512, $options = 0)
    {
        if($options===0){
            $options = $options | JSON_INVALID_UTF8_IGNORE;
        }
        return json_decode($json, $assoc, $depth, $options);
    }

}