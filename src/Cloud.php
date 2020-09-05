<?php
declare(strict_types=1);

namespace Nexendrie\Rss;

use Nexendrie\Utils\Numbers;

/**
 * Cloud
 *
 * @author Jakub Konečný
 * @property int $port
 * @property string $path
 * @property string $protocol
 */
final class Cloud implements IXmlConvertible {
  use \Nette\SmartObject;

  public string $domain;
  protected int $port;
  protected string $path;
  public string $registerProcedure;
  protected string $protocol;

  public function __construct(string $domain, int $port, string $path, string $registerProcedure, string $protocol) {
    $this->domain = $domain;
    $this->setPort($port);
    $this->setPath($path);
    $this->registerProcedure = $registerProcedure;
    $this->setProtocol($protocol);
  }

  protected function getPort(): int {
    return $this->port;
  }

  protected function setPort(int $port): void {
    $this->port = Numbers::range($port, 0, 65535);
  }

  protected function getPath(): string {
    return $this->path;
  }

  /**
   * @throws \InvalidArgumentException
   */
  protected function setPath(string $path): void {
    if(!str_starts_with($path, "/")) {
      throw new \InvalidArgumentException("Path has to start with /.");
    }
    $this->path = $path;
  }

  protected function getProtocol(): string {
    return $this->protocol;
  }

  /**
   * @throws \InvalidArgumentException
   */
  protected function setProtocol(string $protocol): void {
    if(!in_array($protocol, ["xml-rpc", "soap", "http-post", ], true)) {
      throw new \InvalidArgumentException("Invalid value for protocol. Expected xml-rpc, soap or http-post, $protocol given.");
    }
    $this->protocol = $protocol;
  }

  public function appendToXml(\SimpleXMLElement &$parent): void {
    $element = $parent->addChild("cloud");
    $element->addAttribute("domain", $this->domain);
    $element->addAttribute("port", (string) $this->port);
    $element->addAttribute("path", $this->path);
    $element->addAttribute("registerProcedure", $this->registerProcedure);
    $element->addAttribute("protocol", $this->protocol);
  }
}
?>