<?php

namespace wpLinksets;


class Linkset implements \Countable, \ArrayAccess, \IteratorAggregate
{
    /**
     * @var Link\BaseLink[]
     */
    protected $_items = array();

    /**
     * @param Link\BaseLink $attachment
     */
    public function add(Link\BaseLink $attachment)
    {
        $this->_items[] = $attachment;
    }

    /**
     * @return array
     */
    public function to_array()
    {
        return array_map(
            function (Link\BaseLink $attachment) {
                return $attachment->to_array();
            },
            $this->_items
        );
    }

    /**
     * Get primitive representation for use in JavaScript
     *
     * @return array
     */
    public function get_js_data()
    {
        $data = array();
        foreach ($this->_items as $item) {
            $data[] = $item->get_js_data();
        }
        return $data;
    }

    /**
     * @return int
     */
    #[\ReturnTypeWillChange]
    public function count()
    {
        return count($this->_items);
    }

    /**
     * @param midex $offset
     * @return Link\BaseLink|null
     */
    #[\ReturnTypeWillChange]
    public function offsetGet($offset)
    {
        return isset($this->_items[$offset]) ? $this->_items[$offset] : null;
    }

    /**
     * @param mixed $offset
     * @param Link\BaseLink $item
     * @throws \OutOfBoundsException
     * @throws \InvalidArgumentException
     */
    #[\ReturnTypeWillChange]
    public function offsetSet($offset, $item)
    {
        if (!$item instanceof Link\BaseLink) {
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
    #[\ReturnTypeWillChange]
    public function offsetExists($offset)
    {
        return isset($this->_items[$offset]);
    }

    /**
     * @param mixed $offset
     */
    #[\ReturnTypeWillChange]
    public function offsetUnset($offset)
    {
        unset($this->_items[$offset]);
    }

    /**
     * @return \ArrayIterator|\Traversable
     */
    #[\ReturnTypeWillChange]
    public function getIterator()
    {
        return new \ArrayIterator($this->_items);
    }
}
