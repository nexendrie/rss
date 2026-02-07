<?php
declare(strict_types=1);

namespace Nexendrie\Rss;

/**
 * GenericElement
 *
 * @author Jakub Konečný
 */
final class GenericElement implements XmlConvertible
{
    public function __construct(public string $name, public mixed $value)
    {
    }

    public function appendToXml(\SimpleXMLElement $parent): void
    {
        if (is_array($this->value)) {
            foreach ($this->value as $item) {
                $item = (string) $item;
                if ($item === "") {
                    continue;
                }
                $parent->{$this->name}[] = $item;
            }
            return;
        }
        $value = (string) $this->value;
        if ($value === "") {
            return;
        }
        $parent->{$this->name} = $value;
    }
}
