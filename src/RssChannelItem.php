<?php
declare(strict_types=1);

namespace Nexendrie\Rss;

use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Rss Channel Item
 *
 * @author Jakub Konečný
 */
final class RssChannelItem
{
    public function __construct(private readonly array $data)
    {
    }

    private function configureOptions(OptionsResolver $resolver, Generator $generator): void
    {
        foreach ($generator->extensions as $extension) {
            $extension->configureItemOptions($resolver, $generator);
        }
    }

    public function toXml(\SimpleXMLElement &$element, Generator $generator): void
    {
        $resolver = new OptionsResolver();
        $this->configureOptions($resolver, $generator);
        $data = $resolver->resolve($this->data);
        foreach ($data as $key => $value) {
            if ($value === "") {
                continue;
            }
            if (!$value instanceof XmlConvertible) {
                $value = new GenericElement($key, $value);
            }
            $value->appendToXml($element);
        }
    }
}
