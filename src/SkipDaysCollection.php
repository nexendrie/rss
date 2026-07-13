<?php
declare(strict_types=1);

namespace Nexendrie\Rss;

/**
 * SkipDaysCollection
 *
 * @author Jakub Konečný
 * @internal
 */
final class SkipDaysCollection implements XmlConvertible
{
    /** @var list<string> */
    private array $days;

    /**
     * @param list<string> $days
     */
    public function __construct(array $days)
    {
        $this->days = array_unique($days); // @phpstan-ignore assign.propertyType
    }

    public function appendToXml(\SimpleXMLElement $parent): void
    {
        $element = $parent->addChild("skipDays");
        array_walk($this->days, static function (string $value) use ($element): void {
            $element->addChild("day", $value);
        });
    }
}
