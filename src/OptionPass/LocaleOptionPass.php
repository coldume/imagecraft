<?php

namespace Imagecraft\OptionPass;

/**
 * @author Xianghan Wang <coldume@gmail.com>
 * @since  1.0.0
 */
class LocaleOptionPass implements OptionPassInterface
{
    /**
     * @inheritDoc
     */
    public function process(array $options)
    {
        if (
            !isset($options['locale']) ||
            !file_exists(__DIR__.'/../Resources/translations/imagecraft.'.$options['locale'].'.xlf')
        ) {
            $options['locale'] = 'en';
        }

        return $options;
    }
}
