<?php

namespace Hochstrasser\WirecardBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class WirecardBundle extends Bundle
{
    function getContainerExtension()
    {
        return new DependencyInjection\WirecardExtension;
    }
}
