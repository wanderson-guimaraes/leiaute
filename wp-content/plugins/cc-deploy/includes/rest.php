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

use Clearcode\Deploy;
use Clearcode\Deploy\Settings;
use Clearcode\Framework\v1\Filterer;
use WP_REST_Request;
use WP_Error;

if ( ! defined( 'ABSPATH' ) ) exit;
if ( ! class_exists( __NAMESPACE__ . '\Rest' ) ) {
	class Rest extends Filterer {
	    const URL = 'clearcode/deploy/v1';

		public function action_rest_api_init() {
            $settings = Settings::instance();

            if ( $settings->status and $settings->route )
                register_rest_route( self::URL, '/' . $settings->route, [
                    'methods'  => 'POST',
                    'callback' => [ $this, 'deploy' ]
                ] );
        }

        public function deploy( WP_REST_Request $request ) {
            $settings = Settings::instance();

            if ( ! $this->validate_token( $request ) )
                return new WP_Error( 'validate_token',
                    Deploy::__('Token is not valid.' ), [ 'status' => 400 ] );

            if ( ! $this->validate_branch( $request ) )
                return new WP_Error( 'validate_branch',
                    Deploy::__('Branch is not valid.' ), [ 'status' => 400 ]  );

            if ( ! is_executable( $settings->git ) )
                return new WP_Error( 'is_executable', sprintf(
                    Deploy::__('Wrong git path: %s. This is not the file or is not executable.' ),
                    Deploy::get_template( 'code', [ 'content' => $settings->git ] ) ) );

            if ( ! file_exists($settings->dir . '.git' ) )
                return new WP_Error( 'file_exists', sprintf(
                    Deploy::__('Wrong repository path: %s. There is no %s directory.' ),
                    Deploy::get_template( 'code', [ 'content' => $settings->dir ] ),
                    Deploy::get_template( 'code', [ 'content' => '.git' ] ) ) );

            if ( ! chdir( $settings->dir ) )
                return new WP_Error( 'chdir', sprintf(
                    Deploy::__( 'Faild to change directory to: %s.' ),
                    Deploy::get_template( 'code', [ 'content' => $settings->dir ] ) ) );

            $log = shell_exec( $settings->git . ' pull 2>&1' );
            DB::insert( $log );

            $subject = '[' . Deploy::__( 'Deploy' ) . '] ' . get_site_url();
            $mail = new Mail( $settings->emails, $subject, Deploy::get_template( 'pre', [ 'content' => $log ] ) );
            $mail->send();

            return $log;
        }

        /*
         * GitHub
         * https://developer.github.com/webhooks/securing/
         */
        protected function validate_token( WP_REST_Request $request ) {
            $settings = Settings::instance();
            if ( ! $settings->host  ) return false;
            if ( ! $settings->token ) return false;

            switch ( $settings->host ) {
                case 'github' :
                    if ( ! $header = $request->get_header( 'x_hub_signature' ) ) return false;
                    if ( ! $body   = $request->get_body() ) return false;

                    list( $algo, $token ) = explode( '=', $header, 2 ) + ['',''];
                    return $token === hash_hmac( $algo, $body, $settings->token );
//                case 'gitlab' :
//                    if ( ! $token = $request->get_header( 'HTTP_X_GITLAB_TOKEN' ) ) return false;
//                    return $token === sha1( $settings->token );
                case 'bitbucket' :
                case 'stash' :
                    if ( ! $token = $request->get_param( 'token' ) ) return false;
                    return $token === $settings->token;
            }
            return false;
        }

        /*
         * GitHub
         * https://developer.github.com/webhooks/
         * BitBucket
         * https://confluence.atlassian.com/bitbucket/event-payloads-740262817.html#EventPayloads-Push
         */
        protected function validate_branch( WP_REST_Request $request ) {
            $settings = Settings::instance();
            if ( ! $settings->host   ) return false;
            if ( ! $settings->branch ) return false;

            $json = json_decode( $request->get_body() );
            if ( ! is_object( $json ) ) return false;

            switch ( $settings->host ) {
                case 'github' :
                //case 'gitlab' :
                    return ( isset( $json->ref ) and $json->ref === $settings->branch );
                case 'bitbucket' :
                    if ( isset( $json->push ) and
                        isset( $json->push->changes ) and
                        is_array( $json->push->changes ) )
                        foreach ( $json->push->changes as $changes )
                            if ( isset( $changes->new ) and
                                isset( $changes->new->type ) and
                                $changes->new->type === 'branch' and
                                isset( $changes->new->name ) and
                                $changes->new->name === $settings->branch ) return true;
                case 'stash' :
                    if ( isset( $json->refChanges ) and
                        is_array( $json->refChanges ) )
                        foreach ( $json->refChanges as $refChanges )
                            if ( isset( $refChanges->refId ) and $refChanges->refId === $settings->branch ) return true;
            }
            return false;
        }
	}
}
