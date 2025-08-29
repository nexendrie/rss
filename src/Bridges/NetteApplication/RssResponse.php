<?php
declare(strict_types=1);

namespace Nexendrie\Rss\Bridges\NetteApplication;

use Nette\Http\IRequest;
use Nette\Http\IResponse;

/**
 * RSS channel response
 *
 * @author Jakub Konečný
 */
final class RssResponse implements \Nette\Application\Response
{
    public function __construct(public string $source)
    {
    }

    public function send(IRequest $httpRequest, IResponse $httpResponse): void
    {
        $httpResponse->setContentType("application/rss+xml", "utf-8");
        echo $this->source;
    }
}
