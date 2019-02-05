Rss
===

This package helps you generate Rss channels.

Links
-----

Primary repository: https://gitlab.com/nexendrie/rss
Github repository: https://github.com/nexendrie/rss
Packagist: https://packagist.org/packages/nexendrie/rss

Installation
------------
The best way to install it is via Composer. Just add **nexendrie/rss** to your dependencies.

Usage
-----

Create new instance of \Nexendrie\Rss\Generator, set its properties title and description. Then add data source for channel's items. It has to return \Nexendrie\Rss\Collection which is a collection of \Nexendrie\Rss\RssChannelItem.

Example:

```php
<?php
declare(strict_types=1);

use Nexendrie\Rss\Generator,
    Nexendrie\Rss\RssChannelItem,
    Nexendrie\Rss\Collection;

$generator = new Generator();
$generator->title = "Nexendrie RSS";
$generator->link = "https://nexendrie.cz/rss";
$generator->description = "News for package nexendrie/rss";
$generator->dataSource = function() use($generator) {
  $items = new Collection;
  $items[] = new RssChannelItem("Item 1", "Item 1 description", "https://nexendrie.cz/item1", time());
  return $items;
};
$result = $generator->generate();
?>
```

Method generate returns plain text that can showed with echo.

Advanced usage
--------------

It is possible to change time format for the channel. Just use:

```php
<?php
declare(strict_types=1);

$generator = new Nexendrie\Rss\Generator();
$generator->dateTimeFormat = "your preferred format";
?>
```

By default, only first 150 characters of items' description is printed. You can change the length limit like this:

```php
<?php
declare(strict_types=1);

$generator = new Nexendrie\Rss\Generator();
$generator->shortenDescription = 150;
?>
```

or completely disable it by setting the property to 0.

You can also change lastBuildDate for channel by setting property lastBuildDate of the Generator. It accepts callback that returns integer which is interpreted as timestamp. Default value is current time.

It is also possible to use custom template for RSS Channel. Just use:

```php
<?php
declare(strict_types=1);

$generator = new Nexendrie\Rss\Generator();
$generator->template = "/path/to/your/template.xml";
?>
```

You can also set language, copyright, managingEditor and webMaster for channel by setting property of the same name on Generator.

We add generator and docs to channel but you can change their values by setting property of the same name Generator. If you do not want to have them in your channel at all, set their values to an empty string.

Nette applications
------------------

The package contains extension for Nette DI container which adds the generator. It allows you to set maximal length of items' description. Example (with default values):

```yaml
extensions:
    rss: Nexendrie\Rss\Bridges\NetteDI\RssExtension
rss:
    shortenDescription: 150
    dateTimeFormat: "Y-m-d H:i:s"
    template: "/path/to/default/template.xml"
    generator: "Nexendrie RSS"
    docs: "http://blogs.law.harvard.edu/tech/rss"
```

If do not need to do anything with the result after generating, you can you method **response** instead of **generate** to get a response to send from your presenter:

```php
$response = $this->generator->response();
$this->sendResponse($reponse);
```

.
