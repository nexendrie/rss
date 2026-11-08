<?php
declare(strict_types=1);

namespace Nexendrie\Rss;

use BackedEnum;
use DOMDocument;
use SimpleXMLElement;
use UnitEnum;

/**
 * XmlElement
 *
 * @author Jakub Konečný
 */
final class XmlElement implements XmlConvertible
{
    /**
     * @param string|list<scalar|null>|null $value
     * @param array<string, scalar|UnitEnum|null> $attributes
     */
    public function __construct(
        public string $name,
        public string|int|float|array|null $value = null,
        public ?string $namespaceUri = null,
        public array $attributes = [],
        public bool $escapeAsCdata = false
    ) {
    }

    public function appendToXml(SimpleXMLElement $parent): void
    {
        if (is_array($this->value)) {
            foreach ($this->value as $item) {
                if ($item === null || $item === "") {
                    continue;
                }
                $value = $this->getValue($item);
                $element = $parent->addChild($this->name, $this->escapeAsCdata ? "" : $value, $this->namespaceUri);
                if ($this->escapeAsCdata) {
                    $this->escapeValueAsCdata($element, (string) $value);
                }
                $this->addAttributes($element);
            }
            return;
        }

        if (($this->value === null || $this->value === "") && count($this->attributes) === 0) {
            return;
        }

        $value = $this->getValue($this->value);
        $element = $parent->addChild($this->name, $this->escapeAsCdata ? "" : $value, $this->namespaceUri);
        if ($this->escapeAsCdata) {
            $this->escapeValueAsCdata($element, (string) $value);
        }
        $this->addAttributes($element);
    }

    private function addAttributes(SimpleXMLElement $element): void
    {
        foreach ($this->attributes as $key => $value) {
            if ($value === null || $value === "") {
                continue;
            }
            $element->addAttribute($key, $this->getValue($value)); // @phpstan-ignore argument.type
        }
    }

    private function getValue(string|int|float|bool|UnitEnum|null $value): ?string
    {
        if ($value === null || $value === "") {
            return null;
        }
        if ($value instanceof UnitEnum) {
            $returnValue = $value instanceof BackedEnum ? (string) $value->value : $value->name;
        } else {
            $returnValue = (string) $value;
        }
        if (is_bool($value)) {
            $returnValue = $value ? "true" : "false";
        }
        return $returnValue;
    }

    private function escapeValueAsCdata(SimpleXMLElement $element, string $value): void
    {
        $dom = dom_import_simplexml($element);
        /** @var DOMDocument $domDoc */
        $domDoc = $dom->ownerDocument;
        $cdata = $domDoc->createCDATASection($value);
        $dom->appendChild($cdata);
    }
}
