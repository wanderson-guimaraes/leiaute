<?php

/**
 * Plugin Name
 *
 * @package     CC-Deploy
 * @author      PiotrPress
 * @copyright   2017 Clearcode
 * @license     GPL-3.0+
 *
 * @wordpress-plugin
 * Plugin Name: CC-Deploy
 * Plugin URI:  https://wordpress.org/plugins/cc-deploy
 * Description: This plugin allows you to deploy your WordPress site source code from git repository using webhooks.
 * Version:     1.0.0
 * Author:      Clearcode
 * Author URI:  https://clearcode.cc
 * Text Domain: cc-deploy
 * Domain Path: /languages/
 * License:     GPL-3.0+
 * License URI: http://www.gnu.org/licenses/gpl-3.0.txt

   Copyright (C) 2017 by Clearcode <https://clearcode.cc>
   and associates (see AUTHORS.txt file).

   This file is part of CC-Deploy.

   CC-Deploy is free software; you can redistribute it and/or modify
   it under the terms of the GNU General Public License as published by
   the Free Software Foundation; either version 2 of the License, or
   (at your option) any later version.

   CC-Deploy is distributed in the hope that it will be useful,
   but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   GNU General Public License for more details.

   You should have received a copy of the GNU General Public License
   along with CC-Deploy; if not, write to the Free Software
   Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

namespace Clearcode\Deploy;

use Clearcode\Deploy;
use Exception;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'get_plugin_data' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
}

require __DIR__ . '/vendor/autoload.php';

foreach ( [ 'deploy' ] as $file ) {
	require_once( __DIR__ . "/includes/$file.php" );
}

try {
    Deploy::set_plugin_data( __FILE__ );

	spl_autoload_register( __NAMESPACE__ . '::autoload' );

    do_action( __NAMESPACE__, Deploy::instance() );
} catch ( Exception $exception ) {
	if ( WP_DEBUG && WP_DEBUG_DISPLAY ) {
		echo $exception->getMessage();
	}
}
