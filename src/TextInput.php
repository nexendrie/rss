<?php
declare(strict_types=1);

namespace Nexendrie\Rss;

/**
 * TextInput
 *
 * @author Jakub Konečný
 */
final class TextInput implements XmlConvertible {
  public function __construct(public string $title, public string $description, public string $name, public string $link) {
  }

  public function appendToXml(\SimpleXMLElement &$parent): void {
    $element = $parent->addChild("textInput");
    $element->addChild("title", $this->title);
    $element->addChild("description", $this->description);
    $element->addChild("name", $this->name);
    $element->addChild("link", $this->link);
  }
}
?>