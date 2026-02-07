<?php
declare(strict_types=1);

namespace Nexendrie\Rss;

final class Guid implements XmlConvertible
{
    public function __construct(public string $value, public bool|null $permalink = null)
    {
    }

    public function appendToXml(\SimpleXMLElement $parent): void
    {
        $element = $parent->addChild("guid", $this->value);
        if ($this->permalink !== null) {
            $element->addAttribute("isPermalink", $this->permalink ? "true" : "false");
        }
    }
}
