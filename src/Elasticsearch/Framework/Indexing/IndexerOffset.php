<?php declare(strict_types=1);

namespace Shopware\Elasticsearch\Framework\Indexing;

use Shopware\Core\Framework\DataAbstractionLayer\Dbal\Common\IterableQuery;
use Shopware\Elasticsearch\Framework\AbstractElasticsearchDefinition;

/**
 * @package core
 *
 * @phpstan-import-type Offset from IterableQuery
 */
class IndexerOffset
{
    /**
     * @var array<string>
     */
    protected array $languages;

    /**
     * @var array<string>
     */
    protected array $definitions;

    /**
     * @var array<string>
     */
    protected array $allDefinitions;

    protected ?int $timestamp;

    /**
     * @var Offset|null
     */
    protected ?array $lastId;

    protected ?string $languageId;

    protected ?string $definition;

    /**
     * @param list<string> $languages
     * @param iterable<AbstractElasticsearchDefinition> $definitions
     * @param Offset|null $lastId
     */
    public function __construct(
        array $languages,
        iterable $definitions,
        ?int $timestamp,
        ?array $lastId = null
    ) {
        $this->languages = $languages;

        $mapping = [];
        /** @var AbstractElasticsearchDefinition $definition */
        foreach ($definitions as $definition) {
            $mapping[] = $definition->getEntityDefinition()->getEntityName();
        }

        $this->allDefinitions = $mapping;
        $this->definitions = $mapping;

        $this->timestamp = $timestamp;
        $this->lastId = $lastId;

        $this->setNextLanguage();
        $this->setNextDefinition();
    }

    public function setNextDefinition(): ?string
    {
        return $this->definition = array_shift($this->definitions);
    }

    public function resetDefinitions(): void
    {
        $this->definitions = $this->allDefinitions;
        $this->definition = array_shift($this->definitions);
    }

    public function hasNextDefinition(): bool
    {
        return !empty($this->definitions);
    }

    public function setNextLanguage(): ?string
    {
        return $this->languageId = array_shift($this->languages);
    }

    public function hasNextLanguage(): bool
    {
        return !empty($this->languages);
    }

    public function getLanguageId(): ?string
    {
        return $this->languageId;
    }

    /**
     * @return list<string>
     */
    public function getLanguages(): array
    {
        return $this->languages;
    }

    /**
     * @return list<string>
     */
    public function getDefinitions(): array
    {
        return $this->definitions;
    }

    public function getTimestamp(): ?int
    {
        return $this->timestamp;
    }

    /**
     * @return Offset|null
     */
    public function getLastId(): ?array
    {
        return $this->lastId;
    }

    public function getDefinition(): ?string
    {
        return $this->definition;
    }

    /**
     * @param Offset|null $lastId
     */
    public function setLastId(?array $lastId): void
    {
        $this->lastId = $lastId;
    }
}
