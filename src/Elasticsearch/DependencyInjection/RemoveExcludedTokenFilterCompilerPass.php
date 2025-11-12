<?php declare(strict_types=1);

namespace Shopware\Elasticsearch\DependencyInjection;

use Shopware\Core\Framework\DataAbstractionLayer\Search\Term\Filter\ExcludedTokenFilter;
use Shopware\Core\Framework\Log\Package;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

#[Package('framework')]
class RemoveExcludedTokenFilterCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $container->removeDefinition(ExcludedTokenFilter::class);
    }
}
