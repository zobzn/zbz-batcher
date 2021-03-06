<?php

namespace Zobzn;

use SplQueue;

class Batcher
{
    protected $size;
    protected $queue;
    protected $callback;

    /**
     * Batcher constructor.
     *
     * @param int      $size
     * @param callable $callback
     */
    public function __construct($size, $callback)
    {
        if (!is_callable($callback)) {
            throw new \InvalidArgumentException('$callback must be of callable type');
        }

        $this->size     = $size;
        $this->queue    = new SplQueue();
        $this->callback = $callback;
    }

    public function add($data)
    {
        $this->queue->enqueue($data);

        if ($this->queue->count() >= $this->size) {
            $this->process();
        }
    }

    public function finish()
    {
        if ($this->queue->count()) {
            $this->process();
        }
    }

    protected function process()
    {
        $items = array();

        while (!$this->queue->isEmpty()) {
            $items[] = $this->queue->dequeue();
        }

        if ($items) {
            $callback = $this->callback;
            $callback($items);
        }
    }
}
