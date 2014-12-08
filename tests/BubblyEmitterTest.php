<?php

namespace Crier\Tests;

use Crier\BubblyEmitter;

class BubblyEmitterTest extends \PHPUnit_Framework_TestCase
{
    public function testThrowExceptionWhenMissingRootDefine() {
        $this->setExpectedException('InvalidArgumentException');

        $crier = new BubblyEmitter();
        $crier->fooBar();
    }

    public function testWillItBubble() {
        $three = 4;

        $crier = new BubblyEmitter('foo');
        $crier->listener(
            'foo',
            function () use (&$three) {
                $three = 3;
            }
        );

        $crier->fooBar();

        $this->assertEquals(3, $three);
    }
}
