<?php
declare(strict_types=1);

namespace Nexendrie\Rss\Extensions\PodloveSimpleChapters;

use Nexendrie\Rss\Extensions\PodloveSimpleChapters;
use Nexendrie\Rss\XmlConvertible;
use Nexendrie\Utils\Collection;

/**
 * @extends  Collection<Chapter>
 */
final class ChaptersCollection extends Collection implements XmlConvertible
{
    public function __construct()
    {
        parent::__construct();
        $this->class = Chapter::class;
    }

    public function appendToXml(\SimpleXMLElement $parent): void
    {
        $chaptersElement = $parent->addChild("chapters", namespace: PodloveSimpleChapters::NAMESPACE);
        array_walk($this->items, static function (Chapter $value) use ($chaptersElement): void {
            $value->appendToXml($chaptersElement);
        });
    }
}
