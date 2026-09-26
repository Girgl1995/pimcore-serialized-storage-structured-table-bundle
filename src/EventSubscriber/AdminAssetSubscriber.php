<?php

declare(strict_types=1);

namespace Factotum\SerializedStorageStructuredTableBundle\EventSubscriber;

use Pimcore\Event\BundleManager\PathsEvent;
use Pimcore\Event\BundleManagerEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class AdminAssetSubscriber implements EventSubscriberInterface
{
    private const JS_PATHS_EVENT = 'onJsPaths';

    /**
     * @return string[]
     */
    public static function getSubscribedEvents(): array
    {
        return [
            BundleManagerEvents::JS_PATHS => self::JS_PATHS_EVENT,
        ];
    }

    /**
     * @param PathsEvent $event
     * @return void
     */
    public function onJsPaths(PathsEvent $event): void
    {
        $event->addPaths([
            '/bundles/pimcoreserializedstoragestructuredtable/js/pimcore/classes/tags/serializedStorageStructuredTable.js',
            '/bundles/pimcoreserializedstoragestructuredtable/js/pimcore/classes/data/serializedStorageStructuredTable.js',
        ]);
    }
}
