<?php
declare(strict_types=1);

namespace Nexendrie\Rss;

use Nette\Http\IRequest;
use Nette\Http\IResponse;

/**
 * RSS channel response
 *
 * @author Jakub Konečný
 *
 * @property-read string $source
 */
final class RssResponse implements \Nette\Application\IResponse {
  private string $source;
  
  use \Nette\SmartObject;
  
  public function __construct(string $source) {
    $this->source = $source;
  }

  protected function getSource(): string {
    return $this->source;
  }
  
  public function send(IRequest $httpRequest, IResponse $httpResponse): void {
    $httpResponse->setContentType("application/rss+xml", "utf-8");
    echo $this->source;
  }
}
?>