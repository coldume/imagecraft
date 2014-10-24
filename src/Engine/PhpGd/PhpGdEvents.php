<?php

namespace Imagecraft\Engine\PhpGd;

/**
 * Contains all events thrown in the PhpGd engine.
 *
 * @author Xianghan Wang <coldume@gmail.com>
 * @since  1.0.0
 */
final class PhpGdEvents
{
    const PRE_IMAGE    = 'php_gd.pre_image';
    const IMAGE        = 'php_gd.image';
    const FINISH_IMAGE = 'php_gd.finish_image';
}
