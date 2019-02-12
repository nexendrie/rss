<?php
declare(strict_types=1);

namespace Nexendrie\Rss;

/**
 * TextInput
 *
 * @author Jakub Konečný
 * @property string $title
 * @property string $description
 * @property string $name
 * @property string $link
 */
final class TextInput implements IXmlConvertible {
  use \Nette\SmartObject;

  /** @var string */
  protected $title;
  /** @var string */
  protected $description;
  /** @var string */
  protected $name;
  /** @var string */
  protected $link;

  public function __construct(string $title, string $description, string $name, string $link) {
    $this->title = $title;
    $this->description = $description;
    $this->name = $name;
    $this->link = $link;
  }

  public function getTitle(): string {
    return $this->title;
  }

  public function setTitle(string $title): void {
    $this->title = $title;
  }

  public function getDescription(): string {
    return $this->description;
  }

  public function setDescription(string $description): void {
    $this->description = $description;
  }

  public function getName(): string {
    return $this->name;
  }

  public function setName(string $name): void {
    $this->name = $name;
  }

  public function getLink(): string {
    return $this->link;
  }

  public function setLink(string $link): void {
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