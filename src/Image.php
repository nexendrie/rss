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
final class Image implements IXmlConvertible {
  use \Nette\SmartObject;

  public string $url;
  public string $title;
  public string $link;
  protected int $width;
  protected int $height;
  public string $description;

  public function __construct(string $url, string $title, string $link, string $description = "") {
    $this->url = $url;
    $this->title = $title;
    $this->link = $link;
    $this->description = $description;
  }

  protected function getWidth(): int {
    return $this->width;
  }

  protected function setWidth(int $width): void {
    $this->width = Numbers::range($width, 0, 144);
  }

  protected function getHeight(): int {
    return $this->height;
  }

  protected function setHeight(int $height): void {
    $this->height = Numbers::range($height, 0, 400);
  }

  public function appendToXml(\SimpleXMLElement &$parent): void {
    $element = $parent->addChild("image");
    $element->addChild("url", $this->url);
    $element->addChild("title", $this->title);
    $element->addChild("link", $this->link);
    if($this->description !== "") {
      $element->addChild("description", $this->description);
    }
    if(isset($this->width)) {
      $element->addChild("width", (string) $this->width);
    }
    if(isset($this->height)) {
      $element->addChild("height", (string) $this->height);
    }
  }
}
?>