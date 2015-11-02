<?php
namespace Bapi\Bundle;

use ArrayAccess;
use Bono\App;
use Bono\Bundle;
use Bono\Http\Context;

abstract class Base extends Bundle
{
    public function __construct(array $options = [])
    {
        $options = App::getInstance()['bind9'];

        parent::__construct($options);

        $this->routeMap(['GET'], '/', [$this, 'search']);
        $this->routeMap(['POST'], '/', [$this, 'create']);
        // $app['bind9']['indexFile']

    }

    public function reload()
    {
        exec('/usr/sbin/rndc reload', $result, $statusCode);
        if ($statusCode > 0) {
            throw new \Exception('RNDC reload failed');
        }
    }

    abstract public function search(Context $context);
    abstract public function create(Context $context);
}
