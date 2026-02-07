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
    /** @var string[] */
    private array $days;

    /**
     * @param string[] $days
     */
    public function __construct(array $days)
    {
        $this->days = array_unique($days);
    }

    public function appendToXml(\SimpleXMLElement $parent): void
    {
        $element = $parent->addChild("skipDays");
        array_walk($this->days, static function (string $value) use ($element): void {
            $element->addChild("day", $value);
        });
    }
}
