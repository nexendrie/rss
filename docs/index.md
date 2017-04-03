Rss
===

Generate Rss channels.

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

Create new instance of \Nexendrie\Rss\Generator, set its properties title and description. Then add data source for channel's items. It should return array of \Nexendrie\Rss\RssChannelItem.

Example:

```php
use Nexendrie\Rss\Generator,
    Nexendrie\Rss\RssChannelItem;

$generator = new Generator;
$generator->title = "Nexendrie RSS";
$generator->link = "https://nexendrie.cz/rss";
$generator->description = "News for package nexendrie/rss";
$generator->dataSource = function() {
  return [
    new RssChannelItem("Item 1", "Item 1 description", "https://nexendrie.cz/item1", date($generator->dateTimeFormat))
  ];
};
$result = $generator->generate();
```

Method generate returns plain text that can showed with echo.

Advanced usage
--------------

It is possible to change time format for the channel. Just use:

```php
$generator->dateTimeFormat = "your preferred format";
```

By default, only first 150 characters of items' description is printed. You can change the length limit like this:

```php
$generator->shortenDescription = 150;
```

or completely disable it by setting the property to 0.

You can also change lastBuildDate for channel by setting property lastBuildDate of the Generator. It accepts callback that returns integer which is interpreted as timestamp. Default value is current time.

Nette applications
------------------

The package contains extension for Nette DI container which adds the generator. It allows you to set maximal length of items' description. Example (with default values):

```
extensions:
    rss: Nexendrie\Rss\Bridges\NetteDI\RssExtension
rss:
    shortenDescription: 150
    dateTimeFormat: "Y-m-d H:i:s"
```

If do not need to do anything with the result after generating, you can you method **response** instead of **generate** to get a response to send from your presenter:

```php
$response = $this->generator->response();
$this->sendResponse($reponse);
```

.
