<?php
declare(strict_types=1);

namespace Nexendrie\Rss\Bridges\NetteApplication;

use MyTester\Attributes\BeforeTest;
use MyTester\Attributes\Skip;
use MyTester\Attributes\TestSuite;
use Nexendrie\Rss\Collection;
use Nexendrie\Rss\Generator;

#[TestSuite("RssResponse")]
final class RssResponseTest extends \MyTester\TestCase
{
    use \MyTester\Bridges\NetteDI\TCompiledContainer;

    private Generator $generator;

    #[BeforeTest]
    public function setUp(): void
    {
        /** @var Generator $generator */
        $generator = $this->getService(Generator::class);
        $this->generator = $generator;
        $this->generator->dataSource = static fn() => new Collection();
    }

    #[Skip]
    public function testSend(): void
    {
        //$this->checkRss("Rss:default");
    }
}
