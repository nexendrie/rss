<?php
declare(strict_types=1);

namespace Nexendrie\Rss;

use Nexendrie\Rss\Extensions\ElementTypes\Url;
use ValueError;

/**
 * Source
 *
 * @author Jakub Konečný
 * @property string $url
 */
final class Source implements XmlConvertible
{
    use \Nette\SmartObject;

    private string $url;

    public function __construct(string $url = "", public string $title = "")
    {
        $this->setUrl($url);
    }

    protected function getUrl(): string
    {
        return $this->url;
    }

    protected function setUrl(string $url): void
    {
        if ($url !== "" && !(new Url())->getValidator()($url)) {
            throw new ValueError("\"$url\" is not a valid URL.");
        }
        $this->url = $url;
    }

    public function appendToXml(\SimpleXMLElement $parent): void
    {
        if ($this->url !== "") {
            $element = $parent->addChild("source", $this->title);
            $element->addAttribute("url", $this->url);
        }
    }
}
