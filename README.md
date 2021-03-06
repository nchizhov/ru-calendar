# Inok - Russian Productivity Calendar

[![License](https://poser.pugx.org/inok/ru-calendar/license)](https://packagist.org/packages/inok/ru-calendar)
[![License](https://poser.pugx.org/inok/ru-calendar/v/stable)](https://packagist.org/packages/inok/ru-calendar)
[![License](https://poser.pugx.org/inok/ru-calendar/d/monthly)](https://packagist.org/packages/inok/ru-calendar)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/nchizhov/ru-calendar/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/nchizhov/ru-calendar/?branch=master)

This package offers a russian productivity calendar. Source calendar takes from www.consultant.ru.

## Installation

You can install this package by using [Composer](http://getcomposer.org), running this command:

```sh
composer require inok/ru-calendar
```
Link to Packagist: https://packagist.org/packages/inok/ru-calendar

##Usage

```PHP
$calendar = new Inok\ruCalendar\Calendar($year, $cacheDays);
$calendarInfo = $calendar->getCalendar();
```
where:
- **$year** - Requested year of calendar (_default_: current year, _min year_: 2013)
- **$cacheDays** - Cache days for downloaded html-calendar (_default_: 7)

**$calendarInfo** returns array of months:
- **month** - Name of month in russian language
- **preholidays** - Array of preholiday days
- **holidays** - Array of holiday days
- **weekends** - Array of weekend days
- **noworkdays** - Array of no work days

## License

This package is released under the __MIT license__.

Copyright (c) 2021 Chizhov Nikolay
