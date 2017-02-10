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
  
  /**
   * @param \SimpleXMLElement $source
   */
  function __construct(\SimpleXMLElement $source) {
    $this->source = $source;
  }
  
  /**
   * @return \SimpleXMLElement
   */
  function getSource() {
    return $this->source;
  }
  
  /**
   * Sends response to output
   *
   * @param IRequest $httpRequest
   * @param IResponse $httpResponse
   * @return void
   */
  function send(IRequest $httpRequest, IResponse $httpResponse) {
    $httpResponse->setContentType("application/xhtml+xml");
    echo $this->source->asXML();
  }
}
?>