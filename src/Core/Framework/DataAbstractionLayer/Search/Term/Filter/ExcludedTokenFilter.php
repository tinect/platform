<?php declare(strict_types=1);

namespace Shopware\Core\Framework\DataAbstractionLayer\Search\Term\Filter;

use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\Search\SearchConfigLoader;
use Shopware\Core\Framework\Log\Package;

#[Package('framework')]
class ExcludedTokenFilter extends AbstractTokenFilter
{
    /**
     * @internal
     */
    public function __construct(
        private readonly AbstractTokenFilter $tokenFilter,
        private readonly SearchConfigLoader $configLoader,
    ) {
    }

    public function getDecorated(): AbstractTokenFilter
    {
        return $this->tokenFilter;
    }

    /**
     * {@inheritdoc}
     */
    public function filter(array $tokens, Context $context): array
    {
        $tokens = $this->tokenFilter->filter($tokens, $context);

        if (empty($tokens)) {
            return $tokens;
        }

        $config = $this->configLoader->load($context);

        return $this->excludedTermsFilter(
            $tokens,
            array_flip($config[0]['excluded_terms'] ?? [])
        );
    }

    /**
     * @param list<string> $tokens
     * @param array<string> $excludedTerms
     *
     * @return list<string>
     */
    private function excludedTermsFilter(array $tokens, array $excludedTerms): array
    {
        if (empty($excludedTerms) || empty($tokens)) {
            return $tokens;
        }

        $filtered = [];
        foreach ($tokens as $token) {
            if (!isset($excludedTerms[$token])) {
                $filtered[] = $token;
            }
        }

        return $filtered;
    }
}
