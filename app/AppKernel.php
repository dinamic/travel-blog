<?php

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            // enable symfony-standard bundles
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            new Symfony\Bundle\AsseticBundle\AsseticBundle(),
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),

            new Doctrine\Bundle\PHPCRBundle\DoctrinePHPCRBundle(),
            new Knp\Bundle\MenuBundle\KnpMenuBundle(),

            // enable cmf bundles
            new Symfony\Cmf\Bundle\RoutingExtraBundle\SymfonyCmfRoutingExtraBundle(),
            new Symfony\Cmf\Bundle\CoreBundle\SymfonyCmfCoreBundle(),
            new Symfony\Cmf\Bundle\MenuBundle\SymfonyCmfMenuBundle(),
            new Symfony\Cmf\Bundle\ContentBundle\SymfonyCmfContentBundle(),
            new Symfony\Cmf\Bundle\BlogBundle\SymfonyCmfBlogBundle(),
            new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle(),
            new Symfony\Cmf\Bundle\RoutingAutoBundle\SymfonyCmfRoutingAutoBundle(),

            new DTL\TravelBundle\DTLTravelBundle(),
            new DTL\Bundle\TimeDistanceBundle\DTLTimeDistanceBundle(),
            new Sonata\jQueryBundle\SonatajQueryBundle(),
            new Sonata\BlockBundle\SonataBlockBundle(),
            new Sonata\AdminBundle\SonataAdminBundle(),
            new Sonata\DoctrinePHPCRAdminBundle\SonataDoctrinePHPCRAdminBundle(),
            new Sonata\MediaBundle\SonataMediaBundle(),
            new Sonata\EasyExtendsBundle\SonataEasyExtendsBundle(),
            new Sandbox\MediaBundle\SandboxMediaBundle(),
        );

        if (in_array($this->getEnvironment(), array('dev', 'test'))) {
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
        }

        return $bundles;
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__.'/config/config_'.$this->getEnvironment().'.yml');
    }
}
