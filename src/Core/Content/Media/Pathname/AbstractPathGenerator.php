<?php declare(strict_types=1);

namespace Shopware\Core\Content\Media\Pathname;

use Shopware\Core\Content\Media\Aggregate\MediaThumbnail\MediaThumbnailEntity;
use Shopware\Core\Content\Media\MediaEntity;

abstract class AbstractPathGenerator
{
    abstract public function getDecorated(): AbstractPathGenerator;

    abstract public function generatePath(MediaEntity $media, ?MediaThumbnailEntity $thumbnail = null): string;
}
