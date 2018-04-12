<?php

namespace Carlsson;

use Exception;

class Maybe {

    private $tag;
    private $value;

    private function __construct(string $tag, $value = null)
    {
        $this->tag = $tag;
        $this->value = $value;
    }
    
    public static function fromNullable($value)
    {
        if (is_null($value)) {
            return static::Nothing();
        }
        return static::Just($value);
    }

    public static function Just($value)
    {
        return new static("Just", $value);
    }

    public static function Nothing()
    {
        return new static("Nothing");
    }

    public function cata($ifJust, $ifNothing)
    {
        if ($this->tag === "Just") {
            return call_user_func($ifJust, $this->value);
        }
        return call_user_func($ifNothing);
    }

    public function getOrElse($default)
    {
        return $this->cata(
            function($x) { return $x; }, 
            function() use ($default) { return $default; }
        );
    }

    public function map($f)
    {
        return $this->cata(
            function($x) use ($f) { return static::Just(call_user_func($f, $x)); },
            function() { return $this; }
        );
    }

    public function chain($f)
    {
        $ifJust = function($x) use ($f) {
            $next = call_user_func($f, $x);

            if ($next instanceof self === false) {
                throw new Exception("The callable passed to Maybe::chain must return another Maybe");
            }

            return $next;
        };
        return $this->cata($ifJust, function() { return $this; });
    }

    public function apply(Maybe $x)
    {
        $ifJust = function($f) use ($x) {
            return $x->map($f);
        };
        return $this->cata($ifJust, function() { return $this; });
    }

    public function orElse($f)
    {
        $ifNothing = function() use ($f) {
            $next = call_user_func($f);

            if ($next instanceof self === false) {
                throw new Exception("The callable passed to Maybe::orElse must return another Maybe");
            }

            return $next;
        };
        return $this->cata(function() { return $this; }, $ifNothing);
    }
}
