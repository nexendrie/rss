Version 0.12.0-dev
- BC break: renamed IXmlConvertible to XmlConvertible and IRssExtension to RssExtension
- BC break: replaced Nette events with PSR-14 event dispatcher

Version 0.11.0
- made Generator::$dataSource readable
- made RssResponse::$source writable
- BC break: moved RssResponse to namespace Nexendrie\Rss\Bridges\NetteApplication
- raised minimal version of PHP to 8.1
- BC break: callbacks for lastBuildDate and pubDate now have to return DateTime
- BC break: removed Syndication::UPDATE_PERIOD_* constants, updatePeriod in Syndication now accepts enum Nexendrie\Rss\Extensions\Syndication\UpdatePeriod
- BC break: skipDays now has to be an array enum Nexendrie\Rss\Extensions\RssCore\SkipDay

Version 0.10.0
- raised minimal version of PHP to 7.4
- used typed properties (possible BC break)

Version 0.9.0
- BC break: RssChannelItem's constructor now accepts an array with all data
- BC break: RssChannelItem cannot be changed once constructed now
- added support for RSS extensions
- marked RssChannelItem as final
- raised minimal version of PHP to 7.3

Version 0.8.0
- dropped support for Nette 2.4

Version 0.7.1
- RssResponse now sets encoding
- fixed values for <skipHours><hour>

Version 0.7.0
- fixed content type in RssResponse
- added support for skipDays, skipHours, image, cloud and textInput in channel
- raised minimal required version of nexendrie/utils to 1.4
- RSSChannelItem now renders itself
- added support for enclosures and source in items

Version 0.6.0
- BC break: info about channel is now passed via parameter to Generator::generate/response()
- added support for rating in channel
- added support for categories in channel and items
- raised minimal required version of nexendrie/utils to 1.3
- events onBeforeGenerate and onAfterGenerate of Generator now get channel info as second parameter
- prettified output of Generator::generate()

Version 0.5.1
- changed default time format to r
- changed default value for docs

Version 0.5.0
- marked some classes as final
- added events onBeforeGenerate, onAddItem and onAfterGenerate to Generator
- allowed customization of RSS channel's template
- BC break: Generator::generate() now returns string, RssResponse::__construct() now takes string as parameter
- raised minimal version of PHP to 7.2
- BC break: RssChannelItem::$pubDate has to be integer (timestamp) now
- added support for language, copyright, managingEditor, webMaster, pubDate and ttl in channel
- Generator now adds generator and docs to channel by default
- added support for author, comments and guid in items
- changed default time format to D, d M Y H:i:s

Version 0.4.0
- raised minimal version of PHP to 7.1
- allowed setting lastBuildDate for channel
- title, link and description for Generator and all properties of RssChannelItem now has to be strings
- callback for Generator::$dataSource now has to return Nexendrie\Rss\Collection
- added dependency on nexendrie/utils

Version 0.3.0
- allowed setting dateTimeFormat via Nette DI extension
- changed thrown exceptions in Generator

Version 0.2.0
- added method Generator::response()
- added extension for Nette DI container

Version 0.1.0
- initial version
