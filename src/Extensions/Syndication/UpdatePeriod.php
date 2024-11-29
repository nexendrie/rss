<?php
declare(strict_types=1);

namespace Nexendrie\Rss\Extensions\Syndication;

enum UpdatePeriod: string {
  case HOURLY = "hourly";
  case DAILY = "daily";
  case WEAKLY = "weakly";
  case MONTHLY = "monthly";
  case YEARLY = "yearly";
}
?>