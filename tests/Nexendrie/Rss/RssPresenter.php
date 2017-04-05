<?php
declare(strict_types = 1);

namespace Nexendrie\Rss;

/**
 * RssPresenter
 *
 * @author Jakub Konečný
 */
class RssPresenter extends \Nette\Application\UI\Presenter {
  /** @var \Nexendrie\Rss\Generator @inject */
  public $generator;
  
  function renderDefault() {
    $this->generator->dataSource = function() {
      return new Collection;
    };
    $this->sendResponse($this->generator->response());
  }
}
?>