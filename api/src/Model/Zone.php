<?php
namespace Bapi\Model;

use Bono\Http\Context;

class Zone extends Base
{
    public static function find()
    {
        return array_map(function ($line) {
            preg_match('~^zone\s+"([^"]+)"~', $line, $matches);
            return [
                'domain' => $matches[1],
            ];
        }, explode("\n", file_get_contents(static::opts('indexFile'))));
    }

    public static function get($row)
    {
        $zone = new Zone($row, 1);
        return $zone;
    }

    public function validate(Context $context)
    {
        if (empty($this['domain'])) {
            $context->throwError(400);
        }

        $this['dbfile'] = '/var/lib/bind/db.'.$this['domain'];
        $this['subfile'] = '/var/lib/bind/sub.'.$this['domain'];
        $this['ip'] = empty($this['ip']) ? static::opts('parkingIp') : $this['ip'];
    }

    public function persist(Context $context)
    {
        $line = sprintf(
            'zone "%s" { type master; file "%s"; };',
            $this['domain'],
            $this['dbfile']
        );

        $lines = explode("\n", file_get_contents(static::opts('indexFile')));
        $lines[] = $line;

        file_put_contents(static::opts('indexFile'), trim(implode("\n", array_unique($lines))));

        if (!file_exists($this['dbfile'])) {
            $body = t('zone-db', [
                'entry' => $this,
                'config' => static::opts(),
            ]);
            file_put_contents($this['dbfile'], $body);
        }

        if (!file_exists($this['subfile'])) {
            $records = [
                [ 'key' => '@', 'type' => 'A', 'value' => $this['ip'], ],
                [ 'key' => '@', 'type' => 'NS', 'value' => 'ns1', ],
                [ 'key' => '@', 'type' => 'NS', 'value' => 'ns2', ],
            ];

            foreach (static::opts()['ns'] as $key => $value) {
                $records[] =[ 'key' => $key, 'type' => 'A', 'value' => $value, ];
            }

            $body = t('zone-sub', [
                'entries' => $records,
                'config' => static::opts(),
            ]);
            file_put_contents($this['subfile'], $body);
        }
    }

    public function remove(Context $context)
    {
        $this->validate($context);

        $result = [];
        $lines = explode("\n", file_get_contents(static::opts('indexFile')));
        foreach ($lines as $line) {
            if (strpos($line, 'zone "'.$this['domain'].'"') === false) {
                $result[] = $line;
            }
        }
        file_put_contents(static::opts('indexFile'), implode("\n", $result));

        if (file_exists($this['dbfile'])) {
            unlink($this['dbfile']);
        }
        if (file_exists($this['subfile'])) {
            unlink($this['subfile']);
        }
    }
}
