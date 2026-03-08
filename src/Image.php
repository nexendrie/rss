<?php
declare(strict_types=1);

namespace Nexendrie\Rss;

use Nexendrie\Rss\Extensions\ElementTypes\Url;
use Nexendrie\Utils\Numbers;
use ValueError;

/**
 * Image
 *
 * @author Jakub Konečný
 * @property string $url
 * @property string $link
 * @property int $width
 * @property int $height
 */
final class Image implements XmlConvertible
{
    use \Nette\SmartObject;

    private string $url;
    private string $link;
    private int $width;
    private int $height;

    public function __construct(
        string $url,
        public string $title,
        string $link,
        public string $description = ""
    ) {
        $this->setUrl($url);
        $this->setLink($link);
    }

    protected function getUrl(): string
    {
        return $this->url;
    }

    protected function setUrl(string $url): void
    {
        if (!(new Url())->getValidator()($url)) {
            throw new ValueError("\"$url\" is not a valid URL.");
        }
        $this->url = $url;
    }

    protected function getLink(): string
    {
        return $this->link;
    }

    protected function setLink(string $link): void
    {
        if (!(new Url())->getValidator()($link)) {
            throw new ValueError("\"$link\" is not a valid URL.");
        }
        $this->link = $link;
    }

    protected function getWidth(): int
    {
        return $this->width;
    }

    protected function setWidth(int $width): void
    {
        $this->width = Numbers::clamp($width, 0, 144);
    }

    protected function getHeight(): int
    {
        return $this->height;
    }

    protected function setHeight(int $height): void
    {
        $this->height = Numbers::clamp($height, 0, 400);
    }

    public function appendToXml(\SimpleXMLElement $parent): void
    {
        $element = $parent->addChild("image");
        $element->addChild("url", $this->url);
        $element->addChild("title", $this->title);
        $element->addChild("link", $this->link);
        if ($this->description !== "") {
            $element->addChild("description", $this->description);
        }
        if (isset($this->width)) {
            $element->addChild("width", (string) $this->width);
        }
        if (isset($this->height)) {
            $element->addChild("height", (string) $this->height);
        }
    }
}
