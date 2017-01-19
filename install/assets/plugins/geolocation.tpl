//<?php
/**
 * geolocation
 *
 * geo location module for sypexgeo
 *
 * @category    plugin
 * @version     0.1
 * @license     http://www.gnu.org/copyleft/gpl.html GNU Public License (GPL)
 * @internal    @properties {"cookie":[{"label":"Имя cookie","type":"string","value":"geodata_DjPhrbg8cQixqUGNpLju","default":"geodata_DjPhrbg8cQixqUGNpLju","desc":""}],"datafile":[{"label":"Путь к локальной базе","type":"string","value":"assets/plugins/geolocation/SxGeoCity.dat","default":"assets/plugins/geolocation/SxGeoCity.dat","desc":"Прямая ссылка на скачивание: <a href=\"https://sypexgeo.net/files/SxGeoCity_utf8.zip\">https://sypexgeo.net/files/SxGeoCity_utf8.zip</a>"}],"url":[{"label":"Адрес сервера SxGeo","type":"string","value":"http://ru3.sxgeo.city","default":"http://ru3.sxgeo.city","desc":"Список доступных серверов можно узнать здесь: <a href=\"https://sxgeo.city\">https://sxgeo.city</a>"}],"default":[{"label":"Идентификатор записи по умолчанию","type":"string","value":"1","default":"1","desc":""}]}
 * @internal    @events OnWebPageInit
 * @internal    @installset base
 * @reportissues https://github.com/sunhaim/modxevo-sypexgeo
 * @author      sunhaim
 * @lastupdate  2017-01-19
 */

require MODX_BASE_PATH.'assets/plugins/geolocation/plugin.php';