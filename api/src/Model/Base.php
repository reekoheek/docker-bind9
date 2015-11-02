<?php
namespace Bapi\Model;

use Bono\App;
use Bono\Http\Context;
use ROH\Util\Collection as UtilCollection;

abstract class Base extends UtilCollection
{
    protected static $options;

    protected $state = 0;

    protected $attributes = [];

    protected static function opts($key = null)
    {
        if (is_null(static::$options)) {
            static::$options = App::getInstance()['bind9'] ?: [];
        }

        if (0 === func_num_args()) {
            return static::$options;
        } else {
            return isset(static::$options[$key]) ? static::$options[$key] : null;
        }
    }

    public function __construct(array $attributes = [], $state = 0)
    {
        $this->state = $state;
        $this->attributes = $attributes;
    }

    public function save(Context $context, $dryRun = false)
    {
        $this->validate($context);
        if (!$dryRun) {
            $this->persist();
        }
    }

    public function toArray()
    {
        unset($this->attributes['file']);
        return $this->attributes;
    }

    abstract public function validate(Context $context);
    abstract public function persist();
}

if (!function_exists('t')) {
    function t($template, array $data = [])
    {
        $templateFile = '../templates/'.$template.'.php';
        if (!is_readable($templateFile)) {
            throw new \Exception('Unreadable template '.$template);
        }

        ob_start();
        extract($data);
        include $templateFile;
        return ob_get_clean();
    }
}
