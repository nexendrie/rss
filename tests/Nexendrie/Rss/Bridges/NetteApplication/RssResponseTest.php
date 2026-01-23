<?php
declare(strict_types=1);

namespace Nexendrie\Rss\Bridges\NetteApplication;

use Nexendrie\Rss\Collection;
use Nexendrie\Rss\Generator;

require __DIR__ . "/../../../../bootstrap.php";

/**
 * @author Jakub Konečný
 * @testCase
 * @skip
 */
final class RssResponseTest extends \Tester\TestCase
{
    use \Testbench\TCompiledContainer;
    use \Testbench\TPresenter;

    private Generator $generator;

    public function setUp(): void
    {
        /** @var Generator $generator */
        $generator = $this->getService(Generator::class);
        $this->generator = $generator;
        $this->generator->dataSource = static fn() => new Collection();
    }

    public function testSend(): void
    {
        $this->checkRss("Rss:default");
    }
}

$test = new RssResponseTest();
$test->run();
