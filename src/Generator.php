<?php
declare(strict_types=1);

namespace Nexendrie\Rss;

use Closure;
use Nexendrie\Rss\Bridges\NetteApplication\RssResponse;
use Nexendrie\Rss\Events\ChannelAfterGenerate;
use Nexendrie\Rss\Events\ChannelBeforeGenerate;
use Nexendrie\Rss\Events\ItemAdded;
use Nexendrie\Rss\Extensions\RssCore;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * RSS Channel Generator
 *
 * @author Jakub Konečný
 * @property string $template
 */
final class Generator
{
    use \Nette\SmartObject;

    private const NAMESPACE_ATTRIBUTE_HACK = "__extension_namespace__";

    public string $dateTimeFormat = "r";
    public ?Closure $dataSource = null;
    public int $shortenDescription = 150;
    public string $generator = "Nexendrie RSS";
    public string $docs = "http://www.rssboard.org/rss-specification";
    private string $template = __DIR__ . "/template.xml";
    /** @var RssExtensionsCollection|RssExtension[] */
    public RssExtensionsCollection $extensions;

    public function __construct(private readonly ?EventDispatcherInterface $eventDispatcher = null)
    {
        $this->extensions = RssExtensionsCollection::fromArray([new RssCore()]);
    }

    protected function getTemplate(): string
    {
        return $this->template;
    }

    /**
     * @throws \RuntimeException
     */
    protected function setTemplate(string $template): void
    {
        if (!is_file($template) || !is_readable($template)) {
            throw new \RuntimeException("File $template does not exist or is not readable.");
        }
        $this->template = $template;
    }

    /**
     * @throws InvalidStateException
     * @throws \InvalidArgumentException
     */
    private function getData(): Collection
    {
        if ($this->dataSource === null) {
            throw new InvalidStateException("Data source for RSS generator is not set.");
        }
        $items = call_user_func($this->dataSource);
        if (!$items instanceof Collection) {
            throw new \InvalidArgumentException(
                "Callback for data source for RSS generator has to return an instance of  " . Collection::class . "."
            );
        }
        return $items;
    }

    private function writeProperty(\SimpleXMLElement &$channel, array $info, string $property): void
    {
        $value = $info[$property] ?? "";
        if (!$value instanceof XmlConvertible) {
            $value = new GenericElement($property, $value);
        }
        $value->appendToXml($channel->channel);
    }

    /**
     * @throws InvalidStateException
     * @throws \InvalidArgumentException
     */
    public function generate(array $info): string
    {
        $this->eventDispatcher?->dispatch(new ChannelBeforeGenerate($this, $info));
        $items = $this->getData();
        $resolver = new OptionsResolver();
        foreach ($this->extensions as $extension) {
            $extension->configureChannelOptions($resolver, $this);
        }
        $info = $resolver->resolve($info);
        /** @var \SimpleXMLElement $channel */
        $channel = simplexml_load_file($this->template);
        foreach ($this->extensions as $extension) {
            if ($extension->getName() !== "" && $extension->getNamespace() !== "") {
                $channel->addAttribute(
                    self::NAMESPACE_ATTRIBUTE_HACK . $extension->getName(),
                    $extension->getNamespace()
                );
            }
        }
        $properties = $resolver->getDefinedOptions();
        foreach ($properties as $property) {
            $this->writeProperty($channel, $info, $property);
        }
        if ($this->generator !== "") {
            $channel->channel->generator = $this->generator;
        }
        if ($this->docs !== "") {
            $channel->channel->docs = $this->docs;
        }
        /** @var RssChannelItem $item */
        foreach ($items as $item) {
            /** @var \SimpleXMLElement $i */
            $i = $channel->channel->addChild("item");
            $item->toXml($i, $this);
            $this->eventDispatcher?->dispatch(new ItemAdded($this, $channel, $item, $i));
        }
        $this->eventDispatcher?->dispatch(new ChannelAfterGenerate($this, $info));
        $dom = new \DOMDocument();
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;
        $xml = (string) $channel->asXML();
        $xml = str_replace(self::NAMESPACE_ATTRIBUTE_HACK, "xmlns:", $xml);
        $dom->loadXML($xml);
        return (string) $dom->saveXML();
    }

    /**
     * @throws InvalidStateException
     * @throws \InvalidArgumentException
     */
    public function response(array $info): RssResponse
    {
        return new RssResponse($this->generate($info));
    }
}
