<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Cache\Adapter;

use Symfony\Contracts\Cache\TagAwareCacheInterface;

/**
 * Chains several tagaware adapters together.
 *
 * Cached items are fetched from the first adapter having them in its data store.
 * They are saved and deleted in all adapters at once.
 *
 * @author Mark van Dam <mark.vandam@nepgroup.com>
 */
class ChainTagAwareAdapter extends ChainAdapter implements TagAwareCacheInterface, TagAwareAdapterInterface
{
    /**
     * @inheritDoc
     */
    public function __construct(array $adapters, int $defaultLifetime = 0)
    {
        foreach ($adapters as $adapter) {
            if (!$adapter instanceof TagAwareAdapterInterface) {
                throw new \InvalidArgumentException(
                    sprintf(
                        'The class "%s" does not implement the "%s" interface.',
                        \get_class($adapter),
                        TagAwareAdapterInterface::class
                    )
                );
            }
        }

        parent::__construct($adapters, $defaultLifetime);
    }

    /**
     * @inheritDoc
     */
    public function invalidateTags(array $tags)
    {
        $invalidated = true;
        $i = $this->adapterCount;

        while ($i--) {
            $invalidated = $this->adapters[$i]->invalidateTags($tags) && $invalidated;
        }

        return $invalidated;
    }
}
