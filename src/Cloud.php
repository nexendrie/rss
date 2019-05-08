<?php
declare(strict_types=1);

namespace Nexendrie\Rss;

use Nexendrie\Utils\Numbers;
use Nette\Utils\Strings;

/**
 * Cloud
 *
 * @author Jakub Konečný
 * @property string $domain
 * @property int $port
 * @property string $path
 * @property string $registerProcedure
 * @property string $protocol
 */
final class Cloud implements IXmlConvertible {
  use \Nette\SmartObject;

  /** @var string */
  protected $domain;
  /** @var int */
  protected $port;
  /** @var string */
  protected $path;
  /** @var string */
  protected $registerProcedure;
  /** @var string */
  protected $protocol;

  public function __construct(string $domain, int $port, string $path, string $registerProcedure, string $protocol) {
    $this->setDomain($domain);
    $this->setPort($port);
    $this->setPath($path);
    $this->setRegisterProcedure($registerProcedure);
    $this->setProtocol($protocol);
  }

  public function getDomain(): string {
    return $this->domain;
  }

  public function setDomain(string $domain): void {
    $this->domain = $domain;
  }

  public function getPort(): int {
    return $this->port;
  }

  public function setPort(int $port): void {
    $this->port = Numbers::range($port, 0, 65535);
  }

  public function getPath(): string {
    return $this->path;
  }

  /**
   * @throws \InvalidArgumentException
   */
  public function setPath(string $path): void {
    if(!Strings::startsWith($path, "/")) {
      throw new \InvalidArgumentException("Path has to start with /.");
    }
    $this->path = $path;
  }

  public function getRegisterProcedure(): string {
    return $this->registerProcedure;
  }

  public function setRegisterProcedure(string $registerProcedure): void {
    $this->registerProcedure = $registerProcedure;
  }

  public function getProtocol(): string {
    return $this->protocol;
  }

  /**
   * @throws \InvalidArgumentException
   */
  public function setProtocol(string $protocol): void {
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