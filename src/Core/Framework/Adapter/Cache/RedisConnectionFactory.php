<?php declare(strict_types=1);

namespace Shopware\Core\Framework\Adapter\Cache;

use Shopware\Core\Framework\Log\Package;

/**
 * @deprecated tag:v6.7.0 - Will be removed, use \Shopware\Core\Framework\Adapter\Redis\RedisConnectionFactory instead
 */
#[Package('core')]
class RedisConnectionFactory extends \Shopware\Core\Framework\Adapter\Redis\RedisConnectionFactory
{
}
