<?php

namespace App\DependencyInjection\Compiler;

use App\Services\Payment\PayInContext;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class PaymentPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->has(PayInContext::class)) {
            return;
        }

        $definition = $container->findDefinition(PayInContext::class);

        $taggedServices = $container->findTaggedServiceIds('jtwc.payment');

        foreach ($taggedServices as $id => $tags) {
            $definition->addMethodCall('addPaymentProvider', [
                new Reference($id)
            ]);
        }
    }
}
