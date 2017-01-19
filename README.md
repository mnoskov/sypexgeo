# Плагин SypexGeo для MODX Evolution
Плагин определяет город пользователя по ip-адресу с помощью api SypexGeo, используя локальную базу и удаленный сервер.

### Установка и настройка
Для работы плагину нужны файл с базой адресов ([скачать с оф.сайта](https://sypexgeo.net/files/SxGeoCity_utf8.zip) и положить в папку /assets/plugins/geolocation/) и три таблицы в базе данных:

```sql
CREATE TABLE IF NOT EXISTS `modx_locations` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `city_id` int(10) UNSIGNED NOT NULL,
  `region_id` int(10) UNSIGNED NOT NULL,
  `phone` varchar(64) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `city_id` (`city_id`,`region_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `sxgeo_cities`;
CREATE TABLE IF NOT EXISTS `sxgeo_cities` (
  `id` mediumint(8) unsigned NOT NULL,
  `region_id` mediumint(8) unsigned NOT NULL,
  `name_ru` varchar(128) NOT NULL,
  `name_en` varchar(128) NOT NULL,
  `lat` decimal(10,5) NOT NULL,
  `lon` decimal(10,5) NOT NULL,
  `okato` varchar(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

DROP TABLE IF EXISTS `sxgeo_regions`;
CREATE TABLE IF NOT EXISTS `sxgeo_regions` (
  `id` mediumint(8) unsigned NOT NULL,
  `iso` varchar(7) NOT NULL,
  `country` char(2) NOT NULL,
  `name_ru` varchar(128) NOT NULL,
  `name_en` varchar(128) NOT NULL,
  `timezone` varchar(30) NOT NULL,
  `okato` char(4) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
```

В таблице `modx_locations` первые три поля являются обязательными, а остальные могут содержать всю информацию, которой в вашем случае должны отличаться друг от друга разные города (В примере указан только телефон). 

Таблицы `sxgeo_cities` и `sxgeo_regions` содержат данные о городах и регионах ([скачать с оф.сайта](https://sypexgeo.net/files/SxGeo_Info.zip)). Загружать все необязательно, только те, которые вам нужны.

Плагин обрабатывает событие `OnWebPageInit`.

В конфигурации плагина укажите идентификатор записи из таблицы `modx_locations`, который будет использоваться в случае невозможности определения города, либо если города нет в списке. Остальные настройки подходят по умолчанию.

### Использование

В шаблонах становятся доступны плейсхолдеры с приставкой `geo_`:
* `[+geo_city_name+]` - Название города
* `[+geo_region_name+]` - Название региона
* `[+geo_timezone+]` - Временная зона ([возможные варианты](http://php.net/manual/en/timezones.php))
* `[+geo_lat+]`, `[+geo_lon+]` - Координаты города
* `[+geo_manual+]` - **1** если город указан в запросе, и **пусто**, если определен автоматически
* Пользовательские данные (в примере это будет `[+geo_phone+]`)

Для использования в сниппетах текущий город и его данные можно получить с помощью `$modx->geo->location`, все города - с помощью метода `$modx->geo->getRecords()`.

### Примеры использования

Пример сниппета для вывода всех возможных городов:
```php
<?php
  $locations = $modx->geo->getRecords();
  $out = '';

  foreach ( $locations as $location ) {
  	$out .= $modx->parseChunk( 'locations-row', $location, '[+', '+]' );
  }

  return $modx->parseChunk( 'locations-wrap', [ 'wrap' => $out ], '[+', '+]' );
?>
  
// чанк locations-row:
<li><a href="?city=[+city_id+]">[+city+]</a></li>
  
// чанк locations-wrap:
<ul>[+wrap+]</ul>
```

Пример вывода текущего города с телефоном:
```html
<h4>[+geo_city_name+]</h4>
<p>Тел.: [+geo_phone+]</p>
```
