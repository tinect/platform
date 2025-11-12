<?php declare(strict_types=1);

namespace Shopware\Tests\Integration\Elasticsearch\Product;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Term\Filter\AbstractTokenFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Term\Filter\TokenFilter;
use Shopware\Core\Framework\Test\TestCaseBase\IntegrationTestBehaviour;
use Shopware\Elasticsearch\Product\StopwordTokenFilter;

/**
 * @internal
 *
 * @deprecated tag:v6.8.0 - Will be removed without replacement
 */
class StopwordTokenFilterTest extends TestCase
{
    use IntegrationTestBehaviour;

    private Context $context;

    private AbstractTokenFilter $tokenFilter;

    protected function setUp(): void
    {
        $this->context = Context::createDefaultContext();
        $this->tokenFilter = static::getContainer()->get(TokenFilter::class);
    }

    /**
     * @param list<string> $tokens
     * @param list<string> $expected
     */
    #[DataProvider('cases')]
    public function testExcludedFilterFilter(array $tokens, array $expected): void
    {
        $service = new StopwordTokenFilter($this->tokenFilter);
        $keywords = $service->filter($tokens, $this->context);

        sort($expected);
        sort($keywords);
        static::assertSame($expected, $keywords);
    }

    /**
     * @return array<array{list<string>, list<string>}>
     */
    public static function cases(): array
    {
        return [
            [
                [],
                [],
            ],
            [
                ['between', 'against', 'surprise', 'on', 'in', 'at'],
                ['between', 'against', 'surprise', 'on', 'in', 'at'],
            ],
            [
                ['i', '', 'ky', 'u', 'ag', 'vn'],
                ['ky', 'ag', 'vn'],
            ],
        ];
    }
}
