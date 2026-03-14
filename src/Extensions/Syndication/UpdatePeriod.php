<?php
declare(strict_types=1);

namespace Nexendrie\Rss\Extensions\Syndication;

enum UpdatePeriod: string
{
    case Hourly = "hourly";
    case Daily = "daily";
    case Weakly = "weakly";
    case Monthly = "monthly";
    case Yearly = "yearly";
}
