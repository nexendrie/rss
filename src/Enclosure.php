<?php
declare(strict_types=1);

namespace Nexendrie\Rss;

use Nexendrie\Rss\Extensions\ElementTypes\Url;
use ValueError;

/**
 * Enclosure
 *
 * @author Jakub Konečný
 * @property string $url
 */
final class Enclosure implements XmlConvertible
{
    use \Nette\SmartObject;

    private string $url;

    public function __construct(string $url, public int $length, public string $type)
    {
        $this->setUrl($url);
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function setUrl(string $url): void
    {
        if (!(new Url())->getValidator()($url)) {
            throw new ValueError("\"$url\" is not a valid URL.");
        }
        $this->url = $url;
    }

    public function appendToXml(\SimpleXMLElement $parent): void
    {
        $enclosureElement = $parent->addChild("enclosure");
        $enclosureElement->addAttribute("url", $this->url);
        $enclosureElement->addAttribute("length", (string) $this->length);
        $enclosureElement->addAttribute("type", $this->type);
    }
}
