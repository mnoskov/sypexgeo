<?php

	require_once 'sxgeo.php';

	class Geolocation {

		public $location;

		private $modx;
		private $geo      = null;
		private $ip       = null;
		private $fallback = null;
		private $cookie   = 'geodata_DjPhrbg8cQixqUGNpLju';
		private $datafile = 'assets/plugins/geolocation/SxGeoCity.dat';
		private $url      = 'http://ru3.sxgeo.city';
		private $default  = 1;

		public function __construct( $modx, $params = [] ) {
			$this->modx = $modx;

			foreach ( [ 'cookie', 'datafile', 'url', 'default' ] as $field ) {
				if ( !empty( $params[$field] ) ) {
					$this->{$field} = $params[$field];
				}
			}

			$this->location = $this->get();
		}

		private function setcookie( $data ) {
			setcookie( $this->cookie, base64_encode( json_encode( $data ) ), time() + 2592000 );
		}

		private function fallback() {
			if ( $this->fallback !== null ) {
				return $this->fallback;
			}

			$query = $this->modx->db->query( "SELECT * FROM " . $this->modx->getFullTableName( 'locations' ) . " WHERE id = " . $this->default );
			$row = $this->modx->db->getRow( $query );

			$this->fallback = [ 
				'city'   => $row['city_id'], 
				'region' => $row['region_id'], 
				'manual' => true,
			];

			return $this->fallback;
		}

		private function ip() {
			if ( !$this->ip ) {
				foreach ( [ 'HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR', 'SERVER_ADDR' ] as $field ) {
					if ( !empty( $_SERVER[$field] ) && strpos( $_SERVER[$field], ',' ) === FALSE ) {
						$this->ip = $_SERVER[$field];
						break;
					}
				}
			}

			return $this->ip;
		}

		private function locate() {
			if ( isset( $_GET['region'] ) || isset( $_GET['city'] ) ) { 
				$result = [ 
					'city'   => isset( $_GET['city'] ) && is_numeric( $_GET['city'] ) ? intval( $_GET['city'] ) : 0, 
					'region' => isset( $_GET['region'] ) && is_numeric( $_GET['region'] ) ? intval( $_GET['region'] ) : 0, 
					'manual' => true,
				];
				$this->setcookie( $result );
				return $result;
			}

			if ( isset( $_COOKIE[$this->cookie] ) ) {
				$cookie = json_decode( base64_decode( $_COOKIE[$this->cookie] ), true );

				if ( $cookie !== null ) {
					$result = [ 
						'city'   => isset( $cookie['city'] ) && is_numeric( $cookie['city'] ) ? intval( $cookie['city'] ) : 0, 
						'region' => isset( $cookie['region'] ) && is_numeric( $cookie['region'] ) ? intval( $cookie['region'] ) : 0, 
						'manual' => true,
					];

					return $result;
				}
			}

			if ( file_exists( MODX_BASE_PATH . $this->datafile ) ) {
				$api = new SxGeo( MODX_BASE_PATH . $this->datafile );
				$geo = $api->getCityFull( $this->ip() );
			}

			if ( empty( $geo ) ) {
				$isBot = isset( $_SERVER['HTTP_USER_AGENT'] ) && preg_match( '/(Google|Yahoo|Rambler|Bot|Yandex|Spider|Snoopy|Crawler|Finder|Mail|curl)/i', $_SERVER['HTTP_USER_AGENT'] );

				if ( !$isBot ) {
					$geo = @file_get_contents( $this->url . '/json/' . $this->ip() );
					$geo = json_decode( $geo, true );
				}
			}

			if ( !empty( $geo ) ) {
				$result = [
					'manual' => false,
				];

				if ( !empty( $geo['region']['id'] ) ) {
					$result['region'] = $geo['region']['id'];
				}

				if ( !empty( $geo['city']['id'] ) ) {
					$result['city'] = $geo['city']['id'];
				}
			}

			if ( empty( $result ) ) {
				$result = $this->fallback();
			}

			$this->setcookie( $result );
			return $result;
		}

		public function get() {
			$location = $this->locate();
			$fallback = $this->fallback();

			$query = $this->modx->db->query( "SELECT r.name_ru AS region_name, r.timezone, c.name_ru AS city_name, c.lat, c.lon, '$location[manual]' AS manual, l.* FROM " . $this->modx->getFullTableName( 'locations' ) . " l LEFT JOIN `sxgeo_cities` c ON c.id = l.city_id LEFT JOIN `sxgeo_regions` r ON r.id = l.region_id WHERE l.city_id = '$location[city]' OR l.region_id = '$location[region]' OR l.city_id = '$fallback[city]' OR l.region_id = '$fallback[region]' ORDER BY ( l.city_id != '$location[city]' AND l.region_id != '$location[region]' )" );

			return $this->modx->db->getRow( $query );
		}

		public function getRecords() {
			$query = $this->modx->db->query( "SELECT r.name_ru AS region_name, r.timezone, c.name_ru AS city_name, c.lat, c.lon, '$location[manual]' AS manual, l.* FROM " . $this->modx->getFullTableName( 'locations' ) . " l JOIN `sxgeo_cities` c ON c.id = l.city_id JOIN `sxgeo_regions` r ON r.id = l.city_id ORDER BY r.name_ru, c.name_ru" );

			return $this->modx->db->makeArray( $query );
		}

	}


