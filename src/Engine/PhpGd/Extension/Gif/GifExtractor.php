<?php

namespace Imagecraft\Engine\PhpGd\Extension\Gif;

use Imagecraft\Exception\FileParseException;

/**
 * @author Xianghan Wang <coldume@gmail.com>
 * @since  1.0.0
 */
class GifExtractor
{
    /**
     * @var int
     */
    protected $kp;

    /**
     * @var int
     */
    protected $dp;

    /**
     * @param  string $uri
     * @return GifExtracted
     */
    public function extractFromStream($uri)
    {
        $fp = fopen($uri, 'r');
        $this->i = $uri;
        $extracted = $this->extractFromFilePointer($fp);
        fclose($fp);

        return $extracted;
    }

    /**
     * @param  string $contents
     * @return GifExtracted
     */
    public function extractFromContents($contents)
    {
        $fp = fopen('php://memory', 'r+');
        fwrite($fp, $contents);
        rewind($fp);
        $extracted = $this->extractFromFilePointer($fp);
        fclose($fp);

        return $extracted;
    }

    /**
     * @param  resource $fp
     * @return GifExtracted
     * @throws FileParseException
     */
    public function extractFromFilePointer($fp)
    {
        $extracted = new GifExtracted();
        $this->readMetadata($fp, $extracted);
        try {
            while (!feof($fp)) {
                switch ($this->read($fp, 1)) {
                    case "\x21":
                        $this->readExtension($fp, $extracted);
                        break;
                    case "\x2C":
                        $this->readFrame($fp, $extracted);
                        break;
                    case "\x3B":
                        $this->readEnd($extracted);
                        break 2;
                    default:
                        throw new FileParseException('gif.parse.error');
                }
            }
        } catch (\Exception $e) {
            $extracted->setValid(false);
        }
        $this->kp = null;
        $this->dp = null;

        return $extracted;
    }

    /**
     * @param resource     $fp
     * @param GifExtracted $extracted
     */
    protected function readMetadata($fp, GifExtracted $extracted)
    {
        $extracted->setHeader($v = $this->read($fp, 6));
        $extracted->setLogicalScreenDescriptor($v = $this->read($fp, 7));
        if ($v = $extracted->getGlobalColorTableFlag()) {
            $quantity = $extracted->getTotalGlobalColors();
            $extracted->setGlobalColorTable($this->read($fp, $quantity * 3));
        }
    }

    /**
     * @param resource     $fp
     * @param GifExtracted $extracted
     */
    protected function readExtension($fp, GifExtracted $extracted)
    {
        switch ($this->read($fp, 1)) {
            case "\xFF":
                if ("\x0bNETSCAPE2.0" !== $this->read($fp, 12)) {
                    $this->readDataBlock($fp);
                    break;
                }
                $extracted->setNetscapeExtension("\x0bNETSCAPE2.0" . $this->read($fp, 5));
                break;
            case "\xF9":
                $contents = $this->read($fp, 6);
                if ($extracted->hasGraphicControlExtension()) {
                    break;
                }
                $extracted->setGraphicControlExtension($contents);
                break;
            default:
                $this->readDataBlock($fp);
        }
    }

    /**
     * @param resource     $fp
     * @param GifExtracted $extracted
     */
    protected function readFrame($fp, GifExtracted $extracted)
    {
        if (!$extracted->hasGraphicControlExtension()) {
            $contents = "\x04\x00".pack('v', 10)."\x00\x00";
            $extracted->setGraphicControlExtension($contents);
        }

        $extracted->setLinkedKey($this->kp);
        $extracted->setLinkedDisposalMethod($this->dp);
        if (GifExtracted::DISPOSAL_METHOD_PREVIOUS !== $extracted->getDisposalMethod()) {
            $this->kp = $extracted->key();
            $this->dp = $extracted->getDisposalMethod();
        }

        $extracted->setImageDescriptor($this->read($fp, 9));

        if ($extracted->getLocalColorTableFlag()) {
            $quantity = $extracted->getTotalLocalColors();
            $extracted->setLocalColorTable($this->read($fp, $quantity * 3));
        }

        $extracted->setImageData($this->read($fp, 1) . $this->readDataBlock($fp));
        $extracted->next();
    }

    /**
     * @param GifExtracted $extracted
     */
    protected function readEnd(GifExtracted $extracted)
    {
        if (!$extracted->hasNetscapeExtension() && 1 < count($extracted)) {
            $extracted->setNetscapeExtension("\x0bNETSCAPE2.0\x03\x01\x00\x00\x00");
        }
        $extracted->rewind();
    }

    /**
     * @param  resource $fp
     * @return string
     * @throws FileParseException
     */
    protected function readDataBlock($fp)
    {
        $str = '';
        while (true) {
            $packed = $this->read($fp, 1);
            if ("\x00" === $packed) {
                $str .= "\x00";
                break;
            }
            $str .= $packed;
            $blockSize = unpack('C', $packed)[1];
            if (0 === $blockSize) {
                throw new FileParseException('gif.parse.error');
            }
            $str .= $this->read($fp, $blockSize);
        }

        return $str;
    }

    /**
     * @param  resource $fp
     * @param  int      $length
     * @return string
     * @throws FileParseException
     */
    protected function read($fp, $length)
    {
        $bytes = @fread($fp, $length);
        if (false === $bytes || $length > strlen($bytes)) {
            throw new FileParseException('gif.parse.error');
        }

        return $bytes;
    }
}
