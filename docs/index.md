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

Create new instance of \Nexendrie\Rss\Generator and add data source for channel's items. It has to return \Nexendrie\Rss\Collection which is a collection of \Nexendrie\Rss\RssChannelItem. Then you can call method generate with array that contains info about the channel. The basic minimum is title, description and link.

Example:

```php
<?php
declare(strict_types=1);

use Nexendrie\Rss\Generator,
    Nexendrie\Rss\RssChannelItem,
    Nexendrie\Rss\Collection;

$generator = new Generator();
$generator->dataSource = function() use($generator) {
  $items = new Collection();
  $items[] = new RssChannelItem("Item 1", "Item 1 description", "https://nexendrie.cz/item1", time());
  return $items;
};
$info = [
  "title" => "Nexendrie RSS", "link" => "https://nexendrie.cz/rss", "description" => "News for package nexendrie/rss",
];
$result = $generator->generate($info);
?>
```

Method generate returns plain text that can shown with echo.

Advanced usage
--------------

By default, the generator shows all times in the channel in the recommended format but it is possible to change it. Just use:

```php
<?php
declare(strict_types=1);

$generator = new Nexendrie\Rss\Generator();
$generator->dateTimeFormat = "your preferred format";
?>
```

By default, only first 150 characters of items' description are printed (remember, it is supposed to be synopsis). You can change the length limit like this:

```php
<?php
declare(strict_types=1);

$generator = new Nexendrie\Rss\Generator();
$generator->shortenDescription = 500;
?>
```

or completely disable it by setting the property to 0.

You can also change lastBuildDate for channel by setting property lastBuildDate of the Generator. It accepts callback that returns integer which is interpreted as timestamp. Default value is current time. The same applies to pubDate but that has no default value.

It is also possible to use custom template for RSS Channel. Just use:

```php
<?php
declare(strict_types=1);

$generator = new Nexendrie\Rss\Generator();
$generator->template = "/path/to/your/template.xml";
?>
```

You can also set language, copyright, managingEditor, webMaster, ttl, rating, skipDays and skipHours for channel by adding those keys to the info parameter. skipHours has to be an array of integers between 0 and 23, skipDays has to be an array of strings (names of days in English with first capital letter).

We add generator and docs to channel but you can change their values by setting property of the same name Generator. If you do not want to have them in your channel at all, set their values to an empty string.

The item contains (besides those passed to constructor) properties author, comments and guid which when set will be added to the generated xml.

Both channel and individual items can have any number of categories. Category is represented by class Nexendrie\Rss\Category and has an identifier and domain (the latter is optional). You can add categories to channel via key categories in info parameter in form of array. Item class has property categories which behaves like an array (you can add new elements and remove/modify them).

It is possible to add multiple enclosures into an item, just add an instance of Nexendrie\Rss\Enclosure as new element of array to property enclosures on the item.

Nette applications
------------------

The package contains extension for Nette DI container which adds the generator. It allows you to change some parameters of generator. Example (with default values):

```yaml
extensions:
    rss: Nexendrie\Rss\Bridges\NetteDI\RssExtension
rss:
    shortenDescription: 150
    dateTimeFormat: "r"
    template: "/path/to/default/template.xml"
```

If do not need to do anything with the result after generating, you can you method **response** instead of **generate** to get a response to send from your presenter:

```php
$response = $this->generator->response([...]);
$this->sendResponse($reponse);
```

.
