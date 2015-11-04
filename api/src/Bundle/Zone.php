<?php
namespace Bapi\Bundle;

use Bono\App;
use Bono\Http\Context;
use Bono\Bundle;
use Bapi\Model\Zone as MZone;
use Bapi\Model\Record as MRecord;

class Zone extends Bundle
{
    public function __construct(array $options = [])
    {
        $options = App::getInstance()['bind9'];

        parent::__construct($options);

        $this->routeMap(['GET'], '/', [$this, 'search']);
        $this->routeMap(['POST'], '/', [$this, 'create']);
        $this->routeMap(['DELETE'], '/', [$this, 'delete']);

        $this->routeMap(['GET'], '/reload', [$this, 'reload']);
        $this->routeMap(['POST'], '/{domain}/record', [$this, 'recordCreate']);
        $this->routeMap(['DELETE'], '/{domain}/record', [$this, 'recordDelete']);
    }

    public function reload()
    {
        exec('/usr/sbin/rndc reload', $result, $statusCode);
        if ($statusCode > 0) {
            throw new \Exception('RNDC reload failed');
        }
    }

    public function search(Context $context)
    {
        return MZone::find();
    }

    public function create(Context $context)
    {
        $body = $context->getParsedBody();

        $zone = new MZone($body);
        $zone->save($context);

        $this->reload();

        return $zone->toArray();
    }

    public function delete(Context $context)
    {
        $body = $context->getParsedBody();
        $zone = MZone::get($body);
        $zone->remove($context);

        $this->reload();

        return [];
    }

    public function recordCreate(Context $context)
    {
        $body = $context->getParsedBody();


        $record = new MRecord($body);
        $record->save($context);

        $this->reload();

        return $record->toArray();
    }

    public function recordDelete(Context $context)
    {
        $body = $context->getParsedBody();

        $record = MRecord::get($body);
        $record->remove($context);

        $this->reload();

        return [];
    }
}
