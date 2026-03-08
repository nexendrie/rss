<?php
declare(strict_types=1);

namespace Nexendrie\Rss;

use Nexendrie\Rss\Extensions\ElementTypes\Url;
use ValueError;

/**
 * TextInput
 *
 * @author Jakub Konečný
 * @property string $link
 */
final class TextInput implements XmlConvertible
{
    use \Nette\SmartObject;

    private string $link;

    public function __construct(
        public string $title,
        public string $description,
        public string $name,
        string $link
    ) {
        $this->setLink($link);
    }

    public function getLink(): string
    {
        return $this->link;
    }

    public function setLink(string $link): void
    {
        if (!(new Url())->getValidator()($link)) {
            throw new ValueError("\"$link\" is not a valid URL.");
        }
        $this->link = $link;
    }

    public function appendToXml(\SimpleXMLElement $parent): void
    {
        $element = $parent->addChild("textInput");
        $element->addChild("title", $this->title);
        $element->addChild("description", $this->description);
        $element->addChild("name", $this->name);
        $element->addChild("link", $this->link);
    }
}
