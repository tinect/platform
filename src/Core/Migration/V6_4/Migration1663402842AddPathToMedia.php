<?php declare(strict_types=1);

namespace Shopware\Core\Migration\V6_4;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

/**
 * @package core
 *
 * @internal
 */
class Migration1663402842AddPathToMedia extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1663402842;
    }

    public function update(Connection $connection): void
    {
        $this->updateMedia($connection);
        $this->updateThumbnail($connection);

        $this->registerIndexer($connection, 'media.indexer');
    }

    public function updateMedia(Connection $connection): void
    {
        $columns = array_column($connection->fetchAllAssociative('SHOW COLUMNS FROM media'), 'Field');

        if (\in_array('path', $columns, true)) {
            return;
        }

        $connection->executeStatement('ALTER TABLE `media` ADD COLUMN `path` VARCHAR(2048) NULL');
    }

    public function updateThumbnail(Connection $connection): void
    {
        $columns = array_column($connection->fetchAllAssociative('SHOW COLUMNS FROM media_thumbnail'), 'Field');

        if (\in_array('path', $columns, true)) {
            return;
        }

        $connection->executeStatement('ALTER TABLE `media_thumbnail` ADD COLUMN `path` VARCHAR(2048) NULL');
    }

    public function updateDestructive(Connection $connection): void
    {
        // implement update destructive
    }
}