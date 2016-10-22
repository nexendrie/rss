<?php
namespace Nexendrie\Rss;

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
   * @param \Nette\Http\IRequest $httpRequest
   * @param \Nette\Http\IResponse $httpResponse
   * @return void
   */
  function send(\Nette\Http\IRequest $httpRequest, \Nette\Http\IResponse $httpResponse) {
    $httpResponse->setContentType("application/xhtml+xml");
    echo $this->source->asXML();
  }
}
?>