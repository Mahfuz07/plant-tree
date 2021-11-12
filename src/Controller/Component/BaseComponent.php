<?php

namespace App\Controller\Component;

use App\Traits\PolyfillSubTrait;
use Cake\Controller\Component;

class BaseComponent extends Component
{

    use PolyfillSubTrait;

    public $controller = null;

    public function initialize(array $config = []): void
    {
        parent::initialize($config);
        $this->controller = $this->getController();
    }

}