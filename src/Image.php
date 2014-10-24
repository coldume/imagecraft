<?php

namespace Imagecraft;

/**
 * @author Xianghan Wang <coldume@gmail.com>
 * @since  1.0.0
 */
class Image
{
    /**
     * @var string
     */
    protected $contents;

    /**
     * @var int
     */
    protected $width;

    /**
     * @var int
     */
    protected $height;

    /**
     * @var string
     */
    protected $mime;

    /**
     * @var string
     */
    protected $extension;

    /**
     * @var string|null
     */
    protected $message;

    /**
     * @var string|null
     */
    protected $verboseMessage;

    /**
     * @var mixed[]
     */
    protected $extras = [];

    /**
     * @return bool
     * @api
     */
    public function isValid()
    {
        return !isset($this->message);
    }

    /**
     * @param string $message
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }

    /**
     * @return string
     * @api
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param string $verboseMessage
     */
    public function setVerboseMessage($verboseMessage)
    {
        $this->verboseMessage = $verboseMessage;
    }

    /**
     * @return string
     * @api
     */
    public function getVerboseMessage()
    {
        return $this->verboseMessage;
    }

    /**
     * @param string $contents
     */
    public function setContents($contents)
    {
        $this->contents = $contents;
    }

    /**
     * @return string
     * @api
     */
    public function getContents()
    {
        return $this->contents;
    }

    /**
     * @param int $width
     */
    public function setWidth($width)
    {
        $this->width = $width;
    }

    /**
     * @return int
     * @api
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * @param int $height
     */
    public function setHeight($height)
    {
        $this->height = $height;
    }

    /**
     * @return int
     * @api
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * @param string $mime
     */
    public function setMime($mime)
    {
        $this->mime = $mime;
    }

    /**
     * @return string
     * @api
     */
    public function getMime()
    {
        return $this->mime;
    }

    /**
     * @param string $extension
     */
    public function setExtension($extension)
    {
        $this->extension = $extension;
    }

    /**
     * @return string
     * @api
     */
    public function getExtension()
    {
        return $this->extension;
    }

    /**
     * @param mixed $extras
     */
    public function addExtras(array $extras)
    {
        $this->extras = $extras + $this->extras;
    }

    /**
     * @return string
     * @api
     */
    public function getExtras()
    {
        return $this->extras;
    }
}
