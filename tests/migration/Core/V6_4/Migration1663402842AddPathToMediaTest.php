<?php declare(strict_types=1);

namespace Shopware\Tests\Migration\Core\V6_4;

use Doctrine\DBAL\Connection;
use PHPUnit\Framework\TestCase;
use Shopware\Core\Framework\Migration\IndexerQueuer;
use Shopware\Core\Framework\Test\TestCaseBase\KernelLifecycleManager;
use Shopware\Core\Migration\V6_4\Migration1663402842AddPathToMedia;
use Shopware\Tests\Migration\MigrationTestTrait;

/**
 * @internal
 *
 * @covers \Shopware\Core\Migration\V6_4\Migration1663402842AddPathToMedia
 */
class Migration1663402842AddPathToMediaTest extends TestCase
{
    use MigrationTestTrait;

    private Connection $connection;

    private IndexerQueuer $queuer;

    protected function setUp(): void
    {
        $this->connection = KernelLifecycleManager::getConnection();
        $this->queuer = new IndexerQueuer($this->connection);
        // remove the media folder indexer from the queue, if it may be already added by some other migration
        $this->queuer->finishIndexer(['media.indexer']);
    }

    public function testTablesHaveFieldPath(): void
    {
        $migration = new Migration1663402842AddPathToMedia();
        $migration->update($this->connection);

        $mediaColumns = array_column($this->connection->fetchAllAssociative('SHOW COLUMNS FROM media'), 'Field');
        static::assertContains('path', $mediaColumns);

        $mediaColumns = array_column($this->connection->fetchAllAssociative('SHOW COLUMNS FROM media_thumbnail'), 'Field');
        static::assertContains('path', $mediaColumns);

        $registeredIndexers = $this->queuer->getIndexers();
        static::assertArrayHasKey('media.indexer', $registeredIndexers);
    }
}
