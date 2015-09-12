<?php

namespace wpLinksets;

class LinkFactory
{
    /**
     * Link type handlers
     * @var array
     */
    protected $_type_classes = array(
        'link'    => '\\wpLinksets\\Link\\Link',
        'post'    => '\\wpLinksets\\Link\\Post',
        'file'    => '\\wpLinksets\\Link\\File',
        'audio'   => '\\wpLinksets\\Link\\Audio',
        'youtube' => '\\wpLinksets\\Link\\Youtube',
    );

    /**
     * @param string|array $type
     * @return Link\BaseLink|null
     * @throws \InvalidArgumentException
     */
    public function create_link($type)
    {
        if (is_array($type)) {
            $data = $type;
            $type = (string) $data['type'];
        } else {
            $data = null;
            $type = (string) $type;
        }

        if (isset($this->_type_classes[$type])) {
            $class = $this->_type_classes[$type];
            /** @var Link\BaseLink $link */
            $link = call_user_func(array($class, 'from_array'), (array) $data);
            return $link;
        }

        throw new \InvalidArgumentException(sprintf('Invalid link type specified: %s', $type));
    }
}