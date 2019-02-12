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

  public function getUrl(): string {
    return $this->url;
  }

  public function setUrl(string $url): void {
    $this->url = $url;
  }

  public function getTitle(): string {
    return $this->title;
  }

  public function setTitle(string $title): void {
    $this->title = $title;
  }

  public function getLink(): string {
    return $this->link;
  }

  public function setLink(string $link): void {
    $this->link = $link;
  }

  public function getWidth(): int {
    return $this->width;
  }

  public function setWidth(int $width): void {
    $this->width = Numbers::range($width, 0, 144);
  }

  public function getHeight(): int {
    return $this->height;
  }

  public function setHeight(int $height): void {
    $this->height = Numbers::range($height, 0, 400);
  }

  public function getDescription(): string {
    return $this->description;
  }

  public function setDescription(string $description): void {
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