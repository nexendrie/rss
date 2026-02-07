<?php
declare(strict_types=1);

namespace Nexendrie\Rss;

use Nexendrie\Utils\Collection as BaseCollection;

/**
 * CategoriesCollection
 *
 * @author Jakub Konečný
 */
final class EnclosuresCollection extends BaseCollection implements XmlConvertible
{
    protected string $class = Enclosure::class;

    public function appendToXml(\SimpleXMLElement $parent): void
    {
        // @phpstan-ignore argument.type
        array_walk($this->items, static function (Enclosure $value) use ($parent): void {
            $value->appendToXml($parent);
        });
    }
}
