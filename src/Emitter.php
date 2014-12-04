<?php

namespace Crier;

class Emitter {
    private $listeners;
    private $definitions;

    /**
     * Our wayward constructor.
     */
    public function __construct($events = []) {
        $this->init($events);
    }

    /**
     * Initializes our object and defines default events.
     */
    protected function init($events = []) {
        $this->listeners = [];
        $this->definitions = [];

        $this->define($events);
    }

    /**
     * We override the call method to make emitting an event easier and also to
     * create the ability to create protected events.
     *
     * To define a protected event, simply define a method
     */
    public function __call($name, $arguments) {
        $this->emit($this->formatEventName($name), $arguments);
    }

    /**
     * Defines a single event or a list of events that are valid events for this
     * emitter.
     *
     * Prevents errors when an event is emitted that has no listeners.
     */
    public function define($events) {
        if(!is_array($events)) {
            $events = [$events];
        }

        array_walk($events, function($value) {
            $value = $this->formatEventName($value);
        });

        $this->definitions = array_unique(
            array_merge($this->definitions, $events)
        );
    }

    /**
     * Returns whether the event is defined by this emitter or all defined
     * events for this emitter.
     */
    public function defines($event = null) {
        if($event === null) {
            return $this->definitions;
        }

        return in_array($this->formatEventName($event), $this->definitions);
    }

    /**
     * Attach a listener to the emitter.
     */
    public function listener($event, callable $callback) {
        $event = $this->formatEventName($event);

        !in_array($event, array_keys($this->listeners)) && $this->listeners[$event] = [];

        $this->listeners[$event][] = $callback;
    }

    /**
     * If an event is emitted that is not defined and has no listeners, we throw
     * an exception just in case someone made a typo.
     */
    protected function emit($event, $parameters = false) {
        $event = $this->formatEventName($event);

        if(isset($this->listeners[$event])) {
            foreach($this->listeners[$event] as $listener) {
                if($parameters ) {
                    call_user_func_array($listener, $parameters);
                } else {
                    $listener();
                }
            }
        } elseif(!$this->defines($event)) {
            throw new \InvalidArgumentException( 'No event found matching pattern: ' . $event );
        }
    }

    /**
     * Valid event names are all lowercase characters separated by periods.
     *
     * This method filters event names to conform to this restriction.
     */
    private function formatEventName($event) {
        $replacements = [
            '/[^\w.]/' => '',
            '/([a-z])([A-Z])/' => '$1.$2'
        ];

        return strtolower(
            preg_replace(
                array_keys($replacements),
                array_values($replacements),
                $event
            )
        );
    }
}
