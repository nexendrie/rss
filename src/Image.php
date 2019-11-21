<?php
declare(strict_types=1);

namespace Nexendrie\Rss;

use Nexendrie\Utils\Numbers;

/**
 * Image
 *
 * @author Jakub Konečný
 * @property string $url
 * @property string $title
 * @property string $link
 * @property int $width
 * @property int $height
 * @property string $description
 */
final class Image implements IXmlConvertible {
  use \Nette\SmartObject;

  /** @var string */
  protected $url;
  /** @var string */
  protected $title;
  /** @var string */
  protected $link;
  /** @var int */
  protected $width;
  /** @var int */
  protected $height;
  /** @var string */
  protected $description;

  public function __construct(string $url, string $title, string $link, string $description = "") {
    $this->url = $url;
    $this->title = $title;
    $this->link = $link;
    $this->description = $description;
  }

  protected function getUrl(): string {
    return $this->url;
  }

  protected function setUrl(string $url): void {
    $this->url = $url;
  }

  protected function getTitle(): string {
    return $this->title;
  }

  protected function setTitle(string $title): void {
    $this->title = $title;
  }

  protected function getLink(): string {
    return $this->link;
  }

  protected function setLink(string $link): void {
    $this->link = $link;
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

  protected function getDescription(): string {
    return $this->description;
  }

  protected function setDescription(string $description): void {
    $this->description = $description;
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