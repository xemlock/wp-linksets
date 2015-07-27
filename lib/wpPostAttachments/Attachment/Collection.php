<?php

namespace wpPostAttachments\Attachment;


class Collection implements \Countable, \ArrayAccess, \IteratorAggregate
{
    /**
     * @var \wpPostAttachments\Attachment[]
     */
    protected $_items = array();

    /**
     * @return array
     */
    public function to_array()
    {
        return array_map(
            function (\wpPostAttachments\Attachment\Attachment $attachment) {
                return $attachment->to_array();
            },
            $this->_items
        );
    }

    /**
     * @return int
     */
    public function count()
    {
        return count($this->_items);
    }

    /**
     * @param midex $offset
     * @return \wpPostAttachments\Attachment|null
     */
    public function offsetGet($offset)
    {
        return isset($this->_items[$offset]) ? $this->_items[$offset] : null;
    }

    /**
     * @param mixed $offset
     * @param \wpPostAttachments\Attachment $item
     * @throws \OutOfBoundsException
     * @throws \InvalidArgumentException
     */
    public function offsetSet($offset, $item)
    {
        if (!$item instanceof Attachment) {
            throw new \InvalidArgumentException('Item must be an instance of \wpPostAttachments\Attachment');
        }
        if ($offset === null) {
            $offset = count($this->_items);
        }
        $this->_items[$offset] = $item;
    }

    /**
     * @param mixed $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        return isset($this->_items[$offset]);
    }

    /**
     * @param mixed $offset
     */
    public function offsetUnset($offset)
    {
        unset($this->_items[$offset]);
    }

    /**
     * @return \ArrayIterator|\Traversable
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->_items);
    }
}