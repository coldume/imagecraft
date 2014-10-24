<?php

namespace Imagecraft;

/**
 * @author Xianghan Wang <coldume@gmail.com>
 * @since  1.0.0
 */
abstract class AbstractContext
{
    /**
     * @return bool
     * @api
     */
    abstract public function isEngineSupported();

    /**
     * @return string
     * @api
     */
    abstract public function getSupportedImageFormatsToString();

    /**
     * @return string
     * @api
     */
    abstract public function getSupportedFontFormatsToString();

    /**
     * @param  int $modifier
     * @return int
     */
    public function getMemoryLimit($modifier = 0)
    {
        $str = trim(ini_get('memory_limit'));
        if(!preg_match('/(?i)\\A(?<limit>\d+).*(?<unit>g|m|k).*\\Z/', $str, $matches)) {
            return 1024 * 1024 * 1024;
        };
        $limit = $matches['limit'];
        $unit  = strtolower($matches['unit']);
        switch($unit) {
            case 'g':
                $limit *= 1024;
            case 'm':
                $limit *= 1024;
            case 'k':
                $limit *= 1024;
        }

        $modifier *= 1024 * 1024;
        if (0 < $modifier) {
            $limit = ($limit < $modifier) ? $limit : $modifier;
        } else {
            $sum = $limit + $modifier;
            $limit = (0 >= $sum) ? $limit : $sum;
        }

        return $limit;
    }

    /**
     * @return bool
     */
    public function isFileinfoExtensionEnabled()
    {
        return extension_loaded('fileinfo');
    }
}
