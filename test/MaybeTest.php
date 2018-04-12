<?php

use PHPUnit\Framework\TestCase;
use Carlsson\Maybe;


class MaybeTest extends TestCase {

    /**
     * @test
     */
    public function create()
    {
        $just = Maybe::Just(1);
        $nothing = Maybe::Nothing();
        $maybe = Maybe::fromNullable(2);
        $maybe2 = Maybe::fromNullable(null);
        
        $this->assertEquals(1, $just->getOrElse(0));
        $this->assertEquals(0, $nothing->getOrElse(0));
        $this->assertEquals(2, $maybe->getOrElse(0));
        $this->assertEquals(0, $maybe2->getOrElse(0));
    }
    
    /**
     * @test
     */
    public function immutable()
    {
        $maybe = Maybe::fromNullable(1);
        $mapped = $maybe->map(function($num) {
            return $num + $num;
        });
        $this->assertEquals(1, $maybe->getOrElse(0));
    }

    /**
     * @test
     */
    public function map()
    {
        $maybe = Maybe::fromNullable(1);
        $nothing = Maybe::Nothing();
        $double = function($num) {
            return $num + $num;
        };
        $mapped = $maybe->map($double);
        $mappedNothing = $nothing->map($double);

        $this->assertEquals(2, $mapped->getOrElse(0));
        $this->assertEquals(1, $maybe->getOrElse(0));
        $this->assertEquals(0, $mappedNothing->getOrElse(0));
    }
    
    /**
     * @test
     */
    public function chain()
    {
        $maybe = Maybe::fromNullable(1);
        $nothing = Maybe::Nothing();
        $double = function($x) { return Maybe::Just($x + $x); };
        $mapped = $maybe->chain($double);
        $mappedNothing = $nothing->chain($double);

        $this->assertEquals(2, $mapped->getOrElse(0));
        $this->assertEquals(1, $maybe->getOrElse(0));
        $this->assertEquals(0, $mappedNothing->getOrElse(0));
    }

    /**
     * @test
     */
    public function apply()
    {
        $double = function($num) {
            return $num + $num;
        };
        $just = Maybe::Just($double);
        $nothing = Maybe::Nothing();
        $appliedJust = $just->apply(Maybe::Just(2));
        $appliedNothing = $nothing->apply(Maybe::Just(2));
        
        $this->assertEquals(4, $appliedJust->getOrElse(0));
        $this->assertEquals(0, $appliedNothing->getOrElse(0));
    }
    
    /**
     * @test
     */
    public function orElse()
    {
        $just = Maybe::Just(1);
        $nothing = Maybe::Nothing();
        $alt = function() { return Maybe::Just(2); };

        $this->assertEquals(1, $just->orElse($alt)->getOrElse(0));
        $this->assertEquals(2, $nothing->orElse($alt)->getOrElse(0));
    }
}
