<?php
declare(strict_types=1);

namespace Nexendrie\Rss;

enum CloudProtocol: string
{
    case XmlPrc = "xml-rpc";
    case Soap = "soap";
    case HttpPost = "http-post";
}
