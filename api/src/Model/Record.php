<?php
namespace Bapi\Model;

use Bono\Http\Context;

class Record extends Base
{
    public static $ALLOWED_TYPES = [
        'A' => true,
        'NS' => true,
        'MX' => true,
    ];

    public static function get($row)
    {
        $record = new Record($row, 1);
        return $record;
    }

    public function validate(Context $context)
    {
        if (!isset($this['key'])) {
            $context->throwError(400, 'Key is required');
        }

        if (!isset($this['type'])) {
            $context->throwError(400, 'Type is required');
        }

        if (!isset($this['value'])) {
            $context->throwError(400, 'Value is required');
        }

        if (!isset(static::$ALLOWED_TYPES[$this['type']])) {
            $context->throwError(400, 'Type '.$this['type'].' not allowed');
        }
        // $this['id'] = $this['key'].'/'.$this['type'].'/'.$this['value'];
        $this['file'] = '/var/lib/bind/sub.'.$context['domain'];
    }

    public function persist(Context $context)
    {
        $this->remove($context);

        $f = fopen($this['file'], 'a');
        fputs($f, "\n".t('record-line', ['entry' => $this])."\n");
        fclose($f);
    }

    public function remove(Context $context)
    {
        $this->validate($context);

        $result = [];
        $rows = explode("\n", file_get_contents($this['file']));
        foreach ($rows as $line) {
            if ($line === '' || $line === t('record-line', ['entry' => $this])) {
                continue;
            }
            $result[] = $line;
        }

        file_put_contents($this['file'], implode("\n", $result));
    }
}
