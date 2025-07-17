<?php
declare(strict_types=1);

namespace Nexendrie\Rss;

/**
 * XmlConvertible
 *
 * @author Jakub Konečný
 */
interface XmlConvertible {
  public function appendToXml(\SimpleXMLElement &$parent): void;
}
?>