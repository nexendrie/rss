<?php
declare(strict_types=1);

namespace Nexendrie\Rss;

/**
 * Source
 *
 * @author Jakub Konečný
 * @property string $url
 * @property string $title
 */
final class Source extends \stdClass implements IXmlConvertible {
  use \Nette\SmartObject;

  /** @var string */
  protected $url;
  /** @var string */
  protected $title;

  public function __construct(string $url = "", string $title = "") {
    $this->url = $url;
    $this->title = $title;
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

  public function appendToXml(\SimpleXMLElement &$parent): void {
    if($this->url !== "") {
      $element = $parent->addChild("source", $this->title);
      $element->addAttribute("url", $this->url);
    }
  }
}
?>