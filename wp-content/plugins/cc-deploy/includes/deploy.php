<?php

/*
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

namespace Clearcode;

use Clearcode\Deploy\Settings;
use Clearcode\Deploy\Rest;
use Clearcode\Deploy\DB;
use Clearcode\Framework\v1\Plugin;

if ( ! defined( 'ABSPATH' ) ) exit;
if ( ! class_exists( __NAMESPACE__ . '\Deploy' ) ) {
	class Deploy extends Plugin {
		public function __construct() {
            Rest::instance();

			parent::__construct();
		}

		public function action_plugins_loaded() {
            Settings::instance();
        }

		public function activation() {
		    DB::install();
        }

		public function deactivation() {
            //Settings::instance()->delete();
            DB::uninstall();
        }

        public function filter_plugin_action_links( $actions, $plugin_file, $plugin_data, $context ) {
            if ( empty( static::$name        ) ) return $actions;
            if ( empty( $plugin_data['Name'] ) ) return $actions;
            if ( static::$name == $plugin_data['Name'] )
                array_unshift( $actions, self::get_template( 'link', [
                    'url'  => get_admin_url( null, sprintf( Settings::URL, self::$slug . '-settings' ) ),
                    'link' => self::__( 'Settings' )
                ] ) );

            return $actions;
        }
	}
}
