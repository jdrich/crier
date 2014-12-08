<?php

namespace Crier;

class BubblyEmitter extends Emitter {
    protected function emit($event, $parameters = false) {
        $events = $this->stratify($event);

        $root_event = $events[count($events) - 1];

        if(!$this->defines($root_event)) {
            throw new \InvalidArgumentException( 'No event type found matching base event: ' . $root_event );
        }

        foreach($events as $event) {
            $this->notify($event, $parameters);
        }
    }

    /**
     * Generates an array of parent events.
     */
    private function stratify($event) {
        $event_chunks = explode('.', $this->formatEventName($event));
        $stratified_events = [];
        $event = '';

        foreach($event_chunks as $chunk) {
            $event .= $chunk;

            $stratified_events[] = $event;

            $event .= '.';
        }

        return array_reverse($stratified_events);
    }
}
