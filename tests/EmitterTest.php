<?php

namespace Crier\Tests;

use Crier\Emitter;

class EmitterTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructorAcceptEventDefinitions() {
        $crier = new Emitter(['foo.bar']);
        $crier->fooBar();
    }

    public function testCanListenForEvent() {
        $three = 4;

        $crier = new Emitter();
        $crier->listener(
            'fooBar',
            function () use (&$three) {
                $three = 3;
            }
        );

        $crier->fooBar();

        $this->assertEquals(3, $three);
    }

    public function testCanTriggerEventWithParameters() {
        $param = null;

        $crier = new Emitter();
        $crier->listener(
            'fooBar',
            function ($passed) use (&$param) {
                $param = $passed;
            }
        );

        $crier->fooBar('potato');

        $this->assertEquals('potato', $param);
    }

    public function testCanTriggerEventWithMultipleParameters() {
        $param = 0;

        $crier = new Emitter();
        $crier->listener(
            'fooBar',
            function ($three, $seven) use (&$param) {
                $param = $three + $seven;
            }
        );

        $crier->fooBar(3, 7);

        $this->assertEquals(10, $param);
    }


    public function testCanTriggerMultipleListeners() {
        $param = 0;

        $crier = new Emitter();
        $crier->listener(
            'fooBar',
            function () use (&$param) {
                $param++;
            }
        );
        $crier->listener(
            'fooBar',
            function () use (&$param) {
                $param++;
            }
        );

        $crier->fooBar();

        $this->assertEquals(2, $param);
    }

    public function testMissingEventCausesException() {
        $this->setExpectedException( '\InvalidArgumentException' );

        $crier = new Emitter();
        $crier->fooBar();
    }
}
