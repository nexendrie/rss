<?php
declare(strict_types = 1);

namespace Nexendrie\Rss;

/**
 * RssPresenter
 *
 * @author Jakub Konečný
 */
final class RssPresenter extends \Nette\Application\UI\Presenter {
  /** @var \Nexendrie\Rss\Generator @inject */
  public $generator;
  
  public function renderDefault() {
    $this->generator->dataSource = function() {
      return new Collection();
    };
    $this->sendResponse($this->generator->response());
  }
}
?>