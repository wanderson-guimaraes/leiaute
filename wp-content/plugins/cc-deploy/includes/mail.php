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
if ( ! class_exists( __NAMESPACE__ . '\Mail' ) ) {
	class Mail {
		protected $to          = [];
		protected $subject     = '';
		protected $message     = '';
		protected $headers     = [];
		protected $attachments = [];

		protected function get_domain() {
			$domain = strtolower( $_SERVER['SERVER_NAME'] );
			if ( 'www.' == substr( $domain, 0, 4 ) )
				$domain = substr( $domain, 4 );
			return $domain;
		}

		public function __construct( $to, $subject, $message) {
			foreach( [ 'to', 'subject', 'message' ] as $name )
				$this->$name = $$name;

			$this->headers = [
				sprintf( "From: %s <deploy@%s>\r\n", get_bloginfo(), $this->get_domain() ),
				sprintf( 'Content-Type: text/html; charset=%s', get_bloginfo( 'charset' ) )
			];
		}

		public function __set( $name, $value ) {
			if ( property_exists( __CLASS__, $name ) )
				$this->$name = $value;
		}

		public function __get( $name ) {
			if ( property_exists( __CLASS__, $name ) )
				return $this->$name;
			return null;
		}

		public function send() {
			return wp_mail( $this->to, $this->subject, $this->message, $this->headers, $this->attachments );
		}
	}
}
