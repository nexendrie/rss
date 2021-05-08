<?php
declare(strict_types=1);

namespace Nexendrie\Rss;

use Nette\Http\IRequest;
use Nette\Http\IResponse;

/**
 * RSS channel response
 *
 * @author Jakub Konečný
 */
final class RssResponse implements \Nette\Application\Response {
  public string $source;

  use \Nette\SmartObject;

  public function __construct(string $source) {
    $this->source = $source;
  }

  public function send(IRequest $httpRequest, IResponse $httpResponse): void {
    $httpResponse->setContentType("application/rss+xml", "utf-8");
    echo $this->source;
  }
}
?>