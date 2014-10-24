<?php

namespace Imagecraft\OptionPass;

/**
 * @author Xianghan Wang <coldume@gmail.com>
 * @since  1.0.0
 */
class DelegatingOptionPass implements OptionPassInterface
{
    /**
     * @inheritDoc
     */
    public function process(array $options)
    {
        $passes = $this->getRegisteredOptionPasses();
        foreach ($passes as $pass) {
            $options = $pass->process($options);
        }

        return $options;
    }

    /**
     * @return OptionPassInterface[]
     */
    protected function getRegisteredOptionPasses()
    {
        return [
            new CacheDirOptionPass(),
            new DebugOptionPass(),
            new EngineOptionPass(),
            new GifAnimationOptionPass(),
            new JpegQualityOptionPass(),
            new LocaleOptionPass(),
            new MemoryLimitOptionPass(),
            new OutputFormatOptionPass(),
            new PngCompressionOptionPass(),
        ];
    }
}
