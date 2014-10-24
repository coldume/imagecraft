<?php

namespace Imagecraft\Exception;

use TranslatedException\TranslatedException as BaseTranslatedException;

/**
 * @author Xianghan Wang <coldume@gmail.com>
 * @since  1.0.0
 */
class TranslatedException extends BaseTranslatedException
{
    /**
     * @param string          $id
     * @param string[]        $parameters
     * @param null|int        $number
     * @param int             $code
     * @param null|\Exception $previous
     */
    public function __construct(
        $id,
        array $parameters = [],
        $number = null,
        $code = 0,
        \Exception $previous = null
    ) {
        parent::__construct('imagecraft', $id, $parameters, $number, $code, $previous);
    }
}
