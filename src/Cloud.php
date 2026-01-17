<?php
declare(strict_types=1);

namespace Nexendrie\Rss;

use Nexendrie\Utils\Numbers;

/**
 * Cloud
 *
 * @author Jakub Konečný
 * @property int $port
 * @property string $path
 */
final class Cloud implements XmlConvertible
{
    use \Nette\SmartObject;

    private int $port;
    private string $path;

    public function __construct(
        public string $domain,
        int $port,
        string $path,
        public string $registerProcedure,
        public CloudProtocol $protocol
    ) {
        $this->setPort($port);
        $this->setPath($path);
    }

    protected function getPort(): int
    {
        return $this->port;
    }

    protected function setPort(int $port): void
    {
        $this->port = Numbers::range($port, 0, 65535);
    }

    protected function getPath(): string
    {
        return $this->path;
    }

    /**
     * @throws \InvalidArgumentException
     */
    protected function setPath(string $path): void
    {
        if (!str_starts_with($path, "/")) {
            throw new \InvalidArgumentException("Path has to start with /.");
        }
        $this->path = $path;
    }

    public function appendToXml(\SimpleXMLElement &$parent): void
    {
        $element = $parent->addChild("cloud");
        $element->addAttribute("domain", $this->domain);
        $element->addAttribute("port", (string) $this->port);
        $element->addAttribute("path", $this->path);
        $element->addAttribute("registerProcedure", $this->registerProcedure);
        $element->addAttribute("protocol", $this->protocol->value);
    }
}
