<?php

abstract class wpPostAttachments_Attachment implements wpPostAttachmentInterface
{
    /**
     * @var string
     */
    protected $_title;

    /**
     * @var string
     */
    protected $_description;

    /**
     * @var DateTime
     */
    protected $_date;

    /**
     * @var wpPostAttachments_Image
     */
    protected $_image;
}
