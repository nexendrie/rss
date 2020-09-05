<?php
declare(strict_types=1);

namespace Nexendrie\Rss;

/**
 * TextInput
 *
 * @author Jakub Konečný
 */
final class TextInput implements IXmlConvertible {
  use \Nette\SmartObject;

  public string $title;
  public string $description;
  public string $name;
  public string $link;

  public function __construct(string $title, string $description, string $name, string $link) {
    $this->title = $title;
    $this->description = $description;
    $this->name = $name;
    $this->link = $link;
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