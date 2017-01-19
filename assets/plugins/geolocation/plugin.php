<?php

	if ( !defined( 'MODX_BASE_PATH' ) ) {
		die( 'What are you doing? Get out of here!' );
	}

	require_once 'geolocation.php';

	$e = &$modx->Event;

	switch( $e->name ) {
		case 'OnWebPageInit': { 
			$modx->geo = new Geolocation( $modx, $params );

			foreach ( $modx->geo->location as $field => $value ) {
				$modx->setPlaceholder( 'geo_' . $field, $value );
			}

			break;
		}
	}

