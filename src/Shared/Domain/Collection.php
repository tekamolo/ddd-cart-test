<?php

namespace Shopping\Shared\Domain;

use Shopping\Infrastructure\ItemNotInMemoryException;

class Collection extends \ArrayIterator
{
    private $item = array();

    public function add($id,$item){
        $this->offsetSet($id,$item);
    }

    public function remove($id){
        $this->offsetUnset($id);
    }

    public function get($id){
        if(!$this->offsetExists($id))
            throw new ItemNotInCollection();
        return $this->offsetGet($id);
    }
}