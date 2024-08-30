<?php
declare(strict_types=1);

namespace Nexendrie\Rss;

use Tester\Assert;
use Nette\Application\Application;
use Nette\Application\Request;
use Nette\Application\Response;

require __DIR__ . "/../../bootstrap.php";

/**
 * @author Jakub Konečný
 * @testCase
 * @skip
 */
final class RssResponseTest extends \Tester\TestCase {
  use \Testbench\TCompiledContainer;
  use \Testbench\TPresenter;

  private Generator $generator;

  public function setUp(): void {
    /** @var Generator $generator */
    $generator = $this->getService(Generator::class);
    $this->generator = $generator;
    $this->generator->dataSource = function() {
      return new Collection();
    };
  }

  public function testSend(): void {
    $this->checkRss("Rss:default");
  }
}

$test = new RssResponseTest();
$test->run();
?>