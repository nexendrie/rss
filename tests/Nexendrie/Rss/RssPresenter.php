<?php
declare(strict_types = 1);

namespace Nexendrie\Rss;

/**
 * RssPresenter
 *
 * @author Jakub Konečný
 */
final class RssPresenter extends \Nette\Application\UI\Presenter {
  /** @inject */
  public Generator $generator;

  public function renderDefault(): void {
    $this->generator->dataSource = function() {
      return new Collection();
    };
    $this->sendResponse($this->generator->response(["title" => "", "link" => "", "description" => "",]));
  }
}
?>