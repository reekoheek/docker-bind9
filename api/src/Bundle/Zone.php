<?php
namespace Bapi\Bundle;

use Bono\Http\Context;
use Bapi\Model\Zone as MZone;

class Zone extends Base
{
    public function search(Context $context)
    {
        return [];
    }

    public function create(Context $context)
    {
        $body = $context->getParsedBody();

        $zone = new MZone($body);
        $zone->save($context);

        $this->reload();

        return $zone->toArray();
    }
}