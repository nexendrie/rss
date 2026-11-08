<?php
declare(strict_types=1);

namespace Nexendrie\Rss;

use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Rss Channel Item
 *
 * @author Jakub Konečný
 */
final readonly class RssChannelItem
{
    /**
     * @param array<string, mixed> $data
     */
    public function __construct(private array $data)
    {
    }

    private function configureOptions(OptionsResolver $resolver, Generator $generator): void
    {
        foreach ($generator->extensions as $extension) {
            $extension->configureItemOptions($resolver, $generator);
        }
    }

    /**
     * @param array<string, string> $namespaces
     */
    public function toXml(\SimpleXMLElement $element, Generator $generator, array $namespaces = []): void
    {
        $resolver = new OptionsResolver();
        $this->configureOptions($resolver, $generator);
        $data = $resolver->resolve($this->data);
        foreach ($data as $key => $value) {
            if ($value === "") {
                continue;
            }
            if (!$value instanceof XmlConvertible) {
                $namespaceUri = "";
                if (str_contains($key, ":")) {
                    $namespaceUri = $namespaces[explode(":", $key, 2)[0]];
                }
                $value = new XmlElement($key, $value, $namespaceUri);
            }
            $value->appendToXml($element);
        }
    }
}
