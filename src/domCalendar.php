<?php

namespace Inok\ruCalendar;

use DOMDocument;
use DOMNode;
use DOMXPath;

class domCalendar
{
  private $content;
  /** @var DOMXPath */
  private $xPath;

  private $holidays = null;

  /***** DOM XPath Queries *****/
  private $xpathMonths = '//table[@class="cal"]';
  private $xpathMonthInfo = './thead/tr/th[@class="month"]';
  private $xpathPreholidays = './tbody/tr/td[@class="preholiday"]';
  private $xpathNoworkdays = './tbody/tr/td[@class="nowork"]';
  private $xpathWeekends = './tbody/tr/td[contains(@class, "weekend")]';

  public function __construct(string $content) {
    $this->content = $content;
  }

  public function getHolidays(): array {
    if (is_null($this->holidays)) {
      $this->buildCalendarFromDom();
    }
    return $this->holidays;
  }

  private function buildCalendarFromDom() {
    $this->holidays = [];
    libxml_use_internal_errors(true);
    $domDocument = new DOMDocument();
    $domDocument->loadHTML($this->content);
    $this->xPath = new DOMXPath($domDocument);
    $this->buildCalendar();
  }

  private function buildCalendar() {
    $months = $this->xPath->query($this->xpathMonths);
    if (!count($months)) {
      return;
    }
    foreach ($months as $month) {
      $this->buildMonth($month);
    }
  }

  private function buildMonth(DOMNode $month) {
    $monthInfo = $this->xPath->query($this->xpathMonthInfo, $month);
    if (!count($monthInfo)) {
      return;
    }
    $weekendDays = $this->getMonthWeekendDays($month);
    $this->holidays[] = ["month" => $monthInfo[0]->nodeValue,
                         "preholidays" => $this->getMonthPreNoworkholidays($month, $this->xpathPreholidays),
                         "noworkdays" => $this->getMonthPreNoworkholidays($month, $this->xpathNoworkdays),
                         "holidays" => $weekendDays["holidays"],
                         "weekends" => $weekendDays["weekends"]];
  }

  private function getMonthPreNoworkholidays(DOMNode $month, string $query): array {
    $days = $this->xPath->query($query, $month);
    if (!count($days)) {
      return [];
    }
    $daysList = [];
    foreach ($days as $day) {
      $this->removeTags($day);
      $dayInfo = $day->nodeValue;
      if (!$this->isDay($dayInfo)) {
        continue;
      }
      $daysList[] = (int) $dayInfo;
    }
    return $daysList;
  }

  private function getMonthWeekendDays(DOMNode $month): array {
    $daysList = ["weekends" => [],
                 "holidays" => []];
    $days = $this->xPath->query($this->xpathWeekends, $month);
    if (!count($days)) {
      return $daysList;
    }
    foreach ($days as $day) {
      $this->removeTags($day);
      $dayInfo = $day->nodeValue;
      if (!$this->isDay($dayInfo)) {
        continue;
      }
      $wClasses = explode(' ', $day->getAttribute('class'));
      $daysList[(in_array('holiday', $wClasses)) ? "holidays" : "weekends"][] = (int) $dayInfo;
    }
    return $daysList;
  }

  private function removeTags(DOMNode &$day) {
    $tags = $this->xPath->query('*', $day);
    if (!count($tags)) {
      return;
    }
    foreach ($tags as $tag) {
      $day->removeChild($tag);
    }
  }

  private function isDay($day): bool {
    return (is_numeric($day) && $day > 0 && $day < 32);
  }
}
