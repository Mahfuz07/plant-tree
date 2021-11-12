<?php
namespace App\Traits;


use Cake\Controller\Component;
use Cake\Http\Response;
use Cake\Http\ServerRequest;

trait PolyfillSubTrait {

    use PolyfillTrait;

    public function getRequest(): ServerRequest
    {
        return $this->getController()->getRequest();
    }


    public function setRequest($request)
    {
        return $this->getController()->setRequest($request);
    }


    public function getTableLocator()
    {
        return $this->getController()->getTableLocator();
    }


    public function getSession()
    {
        return $this->getController()->getSession();
    }



    public function getDbTable(string $alias, $options = [])
    {
        return $this->getController()->getDbTable($alias, $options);
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

    public function getComponent(string $name, $config = []): Component
    {
        return $this->getController()->getComponent($name, $config);
    }

    public function unloadComponent($name)
    {
        [, $prop] = pluginSplit($name);
        $this->getController()->unloadComponent($name);
        unset($this->{$prop});
        return $this;
    }

    public function redirect($url, $status = 302, $forceExit = true): ?Response
    {
        return $this->getController()->redirect($url, $status, $forceExit);
    }

}