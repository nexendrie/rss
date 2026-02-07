<?php
declare(strict_types=1);

namespace Nexendrie\Rss;

use Nexendrie\Utils\Numbers;

/**
 * Image
 *
 * @author Jakub Konečný
 * @property int $width
 * @property int $height
 */
final class Image implements XmlConvertible
{
    use \Nette\SmartObject;

    private int $width;
    private int $height;

    public function __construct(
        public string $url,
        public string $title,
        public string $link,
        public string $description = ""
    ) {
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
