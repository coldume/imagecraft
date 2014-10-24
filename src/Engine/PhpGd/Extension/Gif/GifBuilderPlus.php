<?php

namespace Imagecraft\Engine\PhpGd\Extension\Gif;

/**
 * Builds Animated GIF using data extracted from GifExtractor.
 *
 * @author Xianghan Wang <coldume@gmail.com>
 * @since  1.0.0
 */
class GifBuilderPlus
{
    /**
     * @var int
     */
    protected $canvasWidth;

    /**
     * @var int
     */
    protected $canvasHeight;

    /**
     * @var int
     */
    protected $loop;

    /**
     * @var int[]
     */
    protected $disposes = [];

    /**
     * @var (null|int)[]
     */
    protected $transparentColorIndexes = [];

    /**
     * @var int[]
     */
    protected $delayTimes = [];

    /**
     * @var int[]
     */
    protected $imageLefts = [];

    /**
     * @var int[]
     */
    protected $imageTops = [];

    /**
     * @var int[]
     */
    protected $imageWidths = [];

    /**
     * @var int[]
     */
    protected $imageHeights = [];

    /**
     * @var bool[]
     */
    protected $interlaceFlags = [];

    /**
     * @var string[]
     */
    protected $colorTables = [];

    /**
     * @var string[]
     */
    protected $imageDatas = [];

    /**
     * @var int
     */
    protected $fp;

    /**
     * @param  int $width
     * @return $this
     */
    public function canvasWidth($width)
    {
        $this->canvasWidth = $width;

        return $this;
    }

    /**
     * @param  int $height
     * @return $this
     */
    public function canvasHeight($height)
    {
        $this->canvasHeight = $height;

        return $this;
    }

    /**
     * @param  int $loop
     * @return $this
     */
    public function loop($loop)
    {
        $this->loop = $loop;

        return $this;
    }

    /**
     * @param  int $dispose
     * @return $this
     */
    public function dispose($dispose)
    {
        $this->disposes[$this->fp] = $dispose;

        return $this;
    }

    /**
     * @param  int $index
     * @return $this
     */
    public function transparentColorIndex($index)
    {
        $this->transparentColorIndexes[$this->fp] = $index;

        return $this;
    }

    /**
     * @param  int $time
     * @return $this
     */
    public function delayTime($time)
    {
        $this->delayTimes[$this->fp] = $time;

        return $this;
    }

    /**
     * @param  int $left
     * @return $this
     */
    public function imageLeft($left)
    {
        $this->imageLefts[$this->fp] = $left;

        return $this;
    }

    /**
     * @param  int $top
     * @return $this
     */
    public function imageTop($top)
    {
        $this->imageTops[$this->fp] = $top;

        return $this;
    }

    /**
     * @param  int $width
     * @return $this
     */
    public function imageWidth($width)
    {
        $this->imageWidths[$this->fp] = $width;

        return $this;
    }

    /**
     * @param  int $height
     * @return $this
     */
    public function imageHeight($height)
    {
        $this->imageHeights[$this->fp] = $height;

        return $this;
    }

    /**
     * @param  bool $flag
     * @return $this
     */
    public function interlaceFlag($flag)
    {
        $this->interlaceFlags[$this->fp] = $flag;

        return $this;
    }

    /**
     * @param  int $contents
     * @return $this
     */
    public function colorTable($contents)
    {
        $this->colorTables[$this->fp] = $contents;

        return $this;
    }

    /**
     * @param  int $contents
     * @return $this
     */
    public function imageData($contents)
    {
        $this->imageDatas[$this->fp] = $contents;

        return $this;
    }

    /**
     * @return $this
     */
    public function addFrame()
    {
        if (!isset($this->fp)) {
            $this->fp = 0;
        } else {
            $this->fp++;
        }

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
        $contents .= pack('v*', $this->canvasWidth, $this->canvasHeight) . "\x00\x00\x00";

        // Netscape Extenstion
        $contents .= "\x21\xFF\x0B";
        $contents .= 'NETSCAPE2.0';
        $contents .= "\x03\x01";
        $contents .= pack('v', $this->loop);
        $contents .= "\x00";

        for ($i = 0; $i < count($this->imageDatas); $i++) {

            // Graphic control extension
            $contents .= "\x21\xF9\x04";
            $unpack = $this->disposes[$i] << 2;
            if (isset($this->transparentColorIndexes[$i])) {
                $unpack = $unpack | 0b00000001;
            }
            $contents .= pack('C', $unpack);
            $contents .= pack('v', $this->delayTimes[$i]);
            $contents .= pack('C', $this->transparentColorIndexes[$i]);
            $contents .= "\x00";

            // Image descriptor
            $contents .= "\x2C";
            $contents .= pack('v*', $this->imageLefts[$i], $this->imageTops[$i]);
            $contents .= pack('v*', $this->imageWidths[$i], $this->imageHeights[$i]);
            $interlace = $this->interlaceFlags[$i] ? 0b01000000 : 0;
            $colorTableSize = log(strlen($this->colorTables[$i]) / 3, 2) - 1;
            $unpack = 0b10000000 | $interlace | $colorTableSize;
            $pack = pack('C', $unpack);
            $contents .= $pack;

            // Local color table
            $contents .= $this->colorTables[$i];

            // Image Data
            $contents .= $this->imageDatas[$i];
        }

        // Terminator
        $contents .= "\x3B";

        return $contents;
    }
}
