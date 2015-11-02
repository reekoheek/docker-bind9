<?php
namespace Bapi\Model;

use Bono\Http\Context;

class Zone extends Base
{
    public function validate(Context $context)
    {
        if (empty($this['domain'])) {
            $context->throwError(400);
        }

        $this['file'] = '/var/lib/bind/db.'.$this['domain'];
        $this['ip'] = empty($this['ip']) ? static::opts('parkingIp') : $this['ip'];

        $this['records'] = isset($this['records']) ? $this['records'] : [];
    }

    public function persist()
    {
        $line = sprintf(
            'zone "%s" { type master; file "%s"; };',
            $this['domain'],
            $this['file']
        );

        $lines = explode("\n", file_get_contents(static::opts('indexFile')));
        $lines[] = $line;

        file_put_contents(static::opts('indexFile'), trim(implode("\n", array_unique($lines))));

        if (!file_exists($this['file'])) {
            $body = t('zone-db', [
                'model' => $this,
                'config' => static::opts(),
            ]);
            file_put_contents($this['file'], $body);
        }
    }
}
