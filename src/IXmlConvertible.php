<?php
declare(strict_types=1);

namespace Nexendrie\Rss;

/**
 * IXmlConvertible
 *
 * @author Jakub Konečný
 */
interface IXmlConvertible {
  public function appendToXml(\SimpleXMLElement &$parent): void;
}
?>