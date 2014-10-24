<?php

namespace Imagecraft\Engine\PhpGd\Extension\Gif;

/**
 * Builds GIF using data extracted from GifExtractor.
 *
 * @author Xianghan Wang <coldume@gmail.com>
 * @since  1.0.0
 */
class GifBuilder
{
    /**
     * @var int
     */
    protected $imageWidth;

    /**
     * @var int
     */
    protected $imageHeight;

    /**
     * @var bool
     */
    protected $interlaceFlag;

    /**
     * @var null|int
     */
    protected $transparentColorIndex;

    /**
     * @var string
     */
    protected $colorTable;

    /**
     * @var string
     */
    protected $imageData;

    /**
     * @param  int $width
     * @return $this
     */
    public function imageWidth($width)
    {
        $this->imageWidth = $width;

        return $this;
    }

    /**
     * @param  int $height
     * @return $this
     */
    public function imageHeight($height)
    {
        $this->imageHeight = $height;

        return $this;
    }

    /**
     * @param  bool $flag
     * @return $this
     */
    public function interlaceFlag($flag)
    {
        $this->interlaceFlag = $flag;

        return $this;
    }

    /**
     * @param  int $index
     * @return $this
     */
    public function transparentColorIndex($index)
    {
        $this->transparentColorIndex = $index;

        return $this;
    }

    /**
     * @param  string $contents
     * @return $this
     */
    public function colorTable($contents)
    {
        $this->colorTable = $contents;

        return $this;
    }

    /**
     * @param  string $contents
     * @return $this
     */
    public function imageData($contents)
    {
        $this->imageData = $contents;

        return $this;
    }

    /**
     * @return string
     */
    public function getContents()
    {
        // Header
        $contents = 'GIF89a';

        // Logical screen descriptor
        $contents .= pack('v*', $this->imageWidth, $this->imageHeight);
        $contents .= "\x00\x00\x00";

        // Graphic control extension
        if (isset($this->transparentColorIndex)) {
            $contents .= "\x21\xF9\x04\x01\x00\x00";
            $contents .= pack('C', $this->transparentColorIndex);
            $contents .= "\x00";
        }

        // Image descriptor
        $contents .= "\x2C\x00\x00\x00\x00";
        $contents .= pack('v*', $this->imageWidth, $this->imageHeight);
        $interlace = $this->interlaceFlag ? 0b01000000 : 0;
        $colorTableSize = log(strlen($this->colorTable) / 3, 2) - 1;
        $unpack = 0b10000000 | $interlace | $colorTableSize;
        $pack = pack('C', $unpack);
        $contents .= $pack;

        // Local color table
        $contents .= $this->colorTable;

        // Image Data
        $contents .= $this->imageData;

        // Terminator
        $contents .= "\x3B";

        $this->imageWidth = null;
        $this->imageHeight = null;
        $this->interlaceFlag = null;
        $this->transparentColorIndex = null;
        $this->colorTable= null;
        $this->imageData= null;

        return $contents;
    }
}
