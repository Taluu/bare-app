<?php
namespace Bare;

use Symfony\Component\HttpKernel\Bundle\Bundle;

use Bare\DependencyInjection\BareExtension;

class BareApp extends Bundle
{
    /** {@inheritDoc} */
    public function getContainerExtension()
    {
        if (null === $this->extension) {
            $this->extension = new BareExtension;
        }

        return $this->extension;
    }
}

