<?php
declare(strict_types=1);

namespace Nexendrie\Rss;

/**
 * RssPresenter
 *
 * @author Jakub Konečný
 */
final class RssPresenter extends \Nette\Application\UI\Presenter
{
    /** @inject */
    public Generator $generator;

    public function renderDefault(): never
    {
        $this->generator->dataSource = function (): Collection {
            return new Collection();
        };
        $this->sendResponse($this->generator->response(["title" => "", "link" => "", "description" => "",]));
    }
}
