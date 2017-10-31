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

namespace Clearcode\Deploy;

if ( ! defined( 'ABSPATH' ) ) exit;
if ( ! class_exists( __NAMESPACE__ . '\DB' ) ) {
	class DB {
	    const TABLE = 'cc_deploy_log';

        static public function install() {
			global $wpdb;

			$charset_collate = $wpdb->get_charset_collate();

			$table = $wpdb->prefix . self::TABLE;

			$sql[] = "CREATE TABLE $table (
				`id` bigint(20) NOT NULL AUTO_INCREMENT,
				`date` datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
				`data` longtext DEFAULT NULL,
				UNIQUE KEY id (id),
				PRIMARY KEY  (id)
			) $charset_collate;";

			if ( ! function_exists( 'dbDelta' ) )
				require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

			return dbDelta( $sql );
		}

        static public function uninstall() {
			global $wpdb;

            $table = $wpdb->prefix . self::TABLE;

			$sql   = "DROP TABLE IF EXISTS $table;";

			return $wpdb->query( $sql );
		}

        static public function insert( $data ) {
			global $wpdb;

            $table = $wpdb->prefix . self::TABLE;

			$insert = $wpdb->insert(
                $table,
				[ 'data' => $data ],
				[ '%s' ]
			);

			if ( $insert ) return $wpdb->insert_id;
			return false;
		}

        static public function select( $per_page = 20, $page_number = 1, $orderby = 'id', $order = 'DESC' ) {
            global $wpdb;

            $table = $wpdb->prefix . self::TABLE;

            $sql   = "SELECT `id`, `date`, `data` FROM $table";

            if ( ! empty( $orderby ) ) {
                $sql .= ' ORDER BY ' . esc_sql( $orderby );
                $sql .= ! empty( $order ) ? ' ' . esc_sql( $order ) : ' ASC';
            } else {
                $sql .= ' ORDER BY id DESC';
            }

            $sql .= " LIMIT $per_page";
            $sql .= ' OFFSET ' . ( $page_number - 1 ) * $per_page;

            return $wpdb->get_results( $sql, 'ARRAY_A' );
        }

        static public function count() {
			global $wpdb;

            $table = $wpdb->prefix . self::TABLE;

			$sql   = "SELECT COUNT(*) FROM $table";

			return $wpdb->get_var( $sql );
		}
	}
}
