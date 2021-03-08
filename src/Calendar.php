<?php

namespace Inok\ruCalendar;

use AppZz\Http\CurlClient;
use AppZz\Http\CurlClient\Response;

class Calendar
{
  private $calendarYear;
  private $calendarCacheDays;

  /** @var domCalendar */
  private $calendar = null;

  private $minYear = 2013;
  const CALENDAR_URL = 'http://www.consultant.ru/law/ref/calendar/proizvodstvennye/%04d/';

  public function __construct(int $year = null, int $calendarCacheDays = 7) {
    $this->calendarYear = $year;
    $this->calendarCacheDays = $calendarCacheDays;
  }

  public function getCalendar(): array {
    if (is_null($this->calendar)) {
      $this->getCalendarContent();
    }
    return (is_null($this->calendar)) ? [] : $this->calendar->getHolidays();
  }

  private function getCalendarContent() {
    if (is_null($this->calendarYear)) {
      $this->calendarYear = (int) date("Y");
    }
    if ($this->calendarYear < $this->minYear) {
      return;
    }
    $content = $this->getCalendarDomContent();
    if ($content) {
      $this->calendar = new domCalendar($content);
    }
  }

  private function getCacheDir(): string {
    $cacheDir = sys_get_temp_dir()."/iCalendar";
    if (!file_exists($cacheDir)) {
      mkdir($cacheDir);
    }
    return $cacheDir;
  }

  private function getCalendarDomContent() {
    $calendarCache = sprintf($this->getCacheDir()."/%04d", $this->calendarYear);
    if (file_exists($calendarCache) && ((time() - filemtime($calendarCache)) / 60 / 60 / 24) <= $this->calendarCacheDays) {
      return file_get_contents($calendarCache);
    }
    $calendarUrl = sprintf(Calendar::CALENDAR_URL, $this->calendarYear);
    $request = CurlClient::get($calendarUrl);
    $request->browser('chrome', 'mac');
    $request->accept('html', 'gzip');
    /** @var Response $response */
    $response = $request->send();
    if ($response->get_status() === 200) {
      file_put_contents($calendarCache, $response->get_body());
      return $response->get_body();
    }
    return false;
  }
}
