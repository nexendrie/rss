<?php
declare(strict_types=1);

namespace Nexendrie\Rss;

use Nette\Http\IRequest,
    Nette\Http\IResponse;

/**
 * RSS channel response
 *
 * @author Jakub Konečný
 *
 * @property-read \SimpleXMLElement $source
 */
class RssResponse implements \Nette\Application\IResponse {
  /** @var \SimpleXMLElement */
  private $source;
  
  use \Nette\SmartObject;
  
  public function __construct(\SimpleXMLElement $source) {
    $this->source = $source;
  }
  
  public function getSource(): \SimpleXMLElement {
    return $this->source;
  }
  
  public function send(IRequest $httpRequest, IResponse $httpResponse): void {
    $httpResponse->setContentType("application/xhtml+xml");
    echo $this->source->asXML();
  }
}
?>