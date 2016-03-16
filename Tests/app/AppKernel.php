<?php

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Routing\RouteCollectionBuilder;

class AppKernel extends Kernel
{
    use MicroKernelTrait;

    public function registerBundles()
    {
        return array(
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Hochstrasser\WirecardBundle\HochstrasserWirecardBundle(),
        );
    }

    protected function configureRoutes(RouteCollectionBuilder $routes)
    {
        $routes->add('/wirecard/confirm', 'hochstrasser_wirecard.wirecard_controller:confirmAction', 'wirecard_confirm');
    }

    protected function configureContainer(ContainerBuilder $c, LoaderInterface $loader)
    {
        $c->loadFromExtension('framework', ['secret' => '12345']);
        $c->loadFromExtension('hochstrasser_wirecard', [
            'customer_id' => getenv('CUSTOMER_ID'),
            'secret' => getenv('CUSTOMER_SECRET'),
            'language' => 'en',
            'shop_id' => getenv('SHOP_ID'),
        ]);
    }
}
