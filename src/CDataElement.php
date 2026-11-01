<?php
declare(strict_types=1);

namespace Nexendrie\Rss;

use DOMDocument;

/**
 * CDataElement
 *
 * @author Jakub Konečný
 */
final class CDataElement implements XmlConvertible
{
    public function __construct(public string $name, public string $value, public ?string $namespaceUri = null)
    {
    }

    public function appendToXml(\SimpleXMLElement $parent): void
    {
        if ($this->value === "") {
            return;
        }
        $element = $parent->addChild($this->name, "", $this->namespaceUri);
        $dom = dom_import_simplexml($element);
        /** @var DOMDocument $domDoc */
        $domDoc = $dom->ownerDocument;
        $cdata = $domDoc->createCDATASection($this->value);
        $dom->appendChild($cdata);
    }
}
