<?php
declare(strict_types=1);

namespace Nexendrie\Rss;

/**
 * SkipHoursCollection
 *
 * @author Jakub Konečný
 * @internal
 */
final class SkipHoursCollection implements XmlConvertible
{
    /**
     * @var list<int>
     */
    private array $hours;

    /**
     * @param list<int> $hours
     */
    public function __construct(array $hours)
    {
        array_walk($hours, static function (int &$value): void {
            $value = (string) $value;
        });
        $hours = array_unique($hours);
        $this->hours = $hours; // @phpstan-ignore assign.propertyType
    }

    public function appendToXml(\SimpleXMLElement $parent): void
    {
        $element = $parent->addChild("skipHours");
        // @phpstan-ignore argument.type
        array_walk($this->hours, static function (string $value) use ($element): void {
            $element->addChild("hour", $value);
        });
    }
}
