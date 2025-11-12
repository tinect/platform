<?php declare(strict_types=1);

namespace Shopware\Elasticsearch\Product;

use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Term\Filter\AbstractTokenFilter;
use Shopware\Core\Framework\Feature;
use Shopware\Core\Framework\Log\Package;

/**
 * @deprecated tag:v6.8.0 - Will be removed without replacement, use TokenFilter and decoration instead
 */
#[Package('framework')]
class StopwordTokenFilter extends AbstractTokenFilter
{
    /**
     * @internal
     */
    public function __construct(
        private readonly AbstractTokenFilter $tokenFilter,
    ) {
    }

    public function getDecorated(): AbstractTokenFilter
    {
        Feature::triggerDeprecationOrThrow('v6.8.0.0', Feature::deprecatedMethodMessage(self::class, 'getDecorated', 'v6.8.0.0'));

        return $this->tokenFilter;
    }

    /**
     * {@inheritdoc}
     */
    public function filter(array $tokens, Context $context): array
    {
        Feature::triggerDeprecationOrThrow('v6.8.0.0', Feature::deprecatedMethodMessage(self::class, 'getDecorated', 'v6.8.0.0'));

        return $this->getDecorated()->filter($tokens, $context);
    }
}
