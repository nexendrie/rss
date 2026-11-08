<?php
declare(strict_types=1);

namespace Nexendrie\Rss\Extensions\PodloveSimpleChapters;

use Nexendrie\Rss\Extensions\ElementTypes\Url;
use Nexendrie\Rss\Extensions\PodloveSimpleChapters;
use Nexendrie\Rss\XmlConvertible;
use Nexendrie\Rss\XmlElement;
use ValueError;

/**
 * @property string|float|int $start
 * @property string $href
 * @property string $image
 */
final class Chapter implements XmlConvertible
{
    use \Nette\SmartObject;

    private string|float|int $start;

    private string $href = "";

    private string $image = "";

    public function __construct(string|float|int $start, public string $title, string $href = "", string $image = "")
    {
        $this->setStart($start);
        $this->setHref($href);
        $this->setImage($image);
    }

    protected function getStart(): string|float|int
    {
        return $this->start;
    }

    protected function setStart(string|float|int $start): void
    {
        if (
            ((is_float($start) || is_int($start)) && $start < 0) ||
            (is_string($start) && preg_match("/^(\d+(\.\d+)?|(((?:\d+:)?([0-5]?\d):)?([0-5]?\d)(?:\.\d+)?))$/", $start) !== 1)
        ) {
            throw new ValueError("Start time has to be normal play time.");
        }
        $this->start = $start;
    }

    protected function getHref(): string
    {
        return $this->href;
    }

    protected function setHref(string $href): void
    {
        if ($href !== "" && !(new Url())->getValidator()($href)) {
            throw new ValueError("\"$href\" is not a valid URL.");
        }
        $this->href = $href;
    }

    protected function getImage(): string
    {
        return $this->image;
    }

    protected function setImage(string $image): void
    {
        if ($image !== "" && !(new Url())->getValidator()($image)) {
            throw new ValueError("\"$image\" is not a valid URL.");
        }
        $this->image = $image;
    }

    public function appendToXml(\SimpleXMLElement $parent): void
    {
        $element = new XmlElement(
            "chapter",
            null,
            PodloveSimpleChapters::NAMESPACE,
            ["start" => $this->start, "title" => $this->title, "href" => $this->href, "image" => $this->image,]
        );
        $element->appendToXml($parent);
    }
}
