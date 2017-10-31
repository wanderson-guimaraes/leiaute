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
use Clearcode\Deploy\Rest;
use Clearcode\Deploy\Table;
use Clearcode\Framework\v1\Filterer;

if ( ! defined( 'ABSPATH' ) ) exit;
if ( ! class_exists( __NAMESPACE__ . '\Settings' ) ) {
	class Settings extends Filterer {
		const URL = 'admin.php?page=%s';

		protected $host   = 'github';
		protected $git    = '/usr/bin/git';
        protected $dir    = ABSPATH;
		protected $branch = 'refs/heads/master';
		protected $token  = '';
        protected $route  = '';
        protected $emails = [];
        protected $status = false;

        protected function __construct() {
            parent::__construct();

            foreach ( [ 'token', 'route' ] as $field )
                $this->$field = wp_generate_password( 32, false, false );

            $this->emails = [ get_option( 'admin_email' ) ];

            if ( $settings = $this->get() )
                foreach( $this->get_class_vars() as $key => $value )
                    if ( ! empty( $settings[$key] ) ) $this->$key = $settings[$key];

        }

        public function __get( $name ) {
            if ( isset( $this->$name ) ) return $this->$name;
            return false;
        }

		public function action_admin_init() {
			register_setting(     Deploy::$slug, Deploy::$slug, [ $this, 'sanitize' ] );
			add_settings_section( Deploy::$slug, Deploy::__( 'Deploy' ), [ $this, 'section'  ], Deploy::$slug );

            add_settings_field( 'host', Deploy::__( 'Hosting' ), [ __CLASS__, 'select' ], Deploy::$slug, Deploy::$slug, [
                'name'     => Deploy::$slug . '[host]',
                'options'  => [
                    'github'    => Deploy::__( 'GitHub' ),
                    //'gitlab'    => Deploy::__( 'GitLab' ),
                    'bitbucket' => Deploy::__( 'BitBucket' ),
                    'stash'     => Deploy::__( 'Stash' )
                ],
                'selected' => $this->host,
                'desc'     => Deploy::__( 'Git repository hosting service.' ) . '<br />' .
                              Deploy::__( 'Default' ) . ': ' .
                              Deploy::get_template( 'code', [ 'content' => 'GitHub' ] )
            ] );

            add_settings_field( 'git', Deploy::__( 'Git' ), [ __CLASS__, 'input' ], Deploy::$slug, Deploy::$slug, [
                'name'  => Deploy::$slug . '[git]',
                'value' => $this->git,
                'desc'  => Deploy::__( 'The path to the git executable.' ) . '<br />' .
                           Deploy::__( 'Default' ) . ': ' .
                           Deploy::get_template( 'code', [ 'content' => '/usr/bin/git' ] )
            ] );

            add_settings_field( 'dir', Deploy::__( 'Dir' ), [ __CLASS__, 'input' ], Deploy::$slug, Deploy::$slug, [
                'name'  => Deploy::$slug . '[dir]',
                'value' => $this->dir,
                'desc'  => Deploy::__( 'The path to your repository.' ) . '<br />' .
                           Deploy::__( 'Default' ) . ': ' .
                           Deploy::get_template( 'code', [ 'content' => 'ABSPATH' ] )
            ] );

            add_settings_field( 'branch', Deploy::__( 'Branch' ), [ __CLASS__, 'input' ], Deploy::$slug, Deploy::$slug, [
                'name'  => Deploy::$slug . '[branch]',
                'value' => $this->branch,
                'desc'  => Deploy::__( 'The branch route.' ) . '<br />' .
                           Deploy::__( 'Default' ) . ': ' .
                           Deploy::get_template( 'code', [ 'content' => 'refs/heads/master' ] )
            ] );

            add_settings_field( 'token', Deploy::__( 'Token' ), [ __CLASS__, 'input' ], Deploy::$slug, Deploy::$slug, [
                'name'  => Deploy::$slug . '[token]',
                'value' => $this->token,
                'desc'  => Deploy::__( 'The secret token to add as a GitHub secret or otherwise as query string parameter.' ) . '<br />' .
                           Deploy::__( 'Default' ) . ': ' .
                           Deploy::get_template( 'code', [ 'content' => 'random' ] )
            ] );

            add_settings_field( 'route', Deploy::__( 'URL' ), [ __CLASS__, 'input' ], Deploy::$slug, Deploy::$slug, [
                'name'  => Deploy::$slug . '[route]',
                'value' => $this->route,
                'desc'  => Deploy::__( 'Payload URL for webhook.' ) . '<br />' .
                    Deploy::__( 'Default' ) . ': ' .
                    Deploy::get_template( 'code', [ 'content' => 'random' ] ),
                'before' => trailingslashit( get_site_url( null, 'wp-json/' . Rest::URL ) )
            ] );

            add_settings_field( 'emails', Deploy::__( 'Emails' ), [ $this, 'emails' ], Deploy::$slug, Deploy::$slug );
            add_settings_field( 'test',   Deploy::__( 'Test'   ), [ $this, 'test'   ], Deploy::$slug, Deploy::$slug );
            add_settings_field( 'status', Deploy::__( 'Status' ), [ $this, 'status' ], Deploy::$slug, Deploy::$slug );
		}

		public function action_admin_menu_999() {
            add_menu_page(
                Deploy::__( 'Deploy' ),
                Deploy::__( 'Deploy' ),
                'manage_options',
                Deploy::$slug . '-logs',
                [ $this, 'logs' ],
                'dashicons-update',
                2
            );

            add_submenu_page(
                Deploy::$slug . '-logs',
                Deploy::__( 'Logs' ),
                Deploy::__( 'Logs' ),
                'manage_options',
                Deploy::$slug . '-logs',
                [ $this, 'logs' ]
            );

            add_submenu_page(
                Deploy::$slug . '-logs',
                Deploy::__( 'Settings' ),
                Deploy::__( 'Settings' ),
                'manage_options',
                Deploy::$slug . '-settings',
                [ $this, 'settings' ]
            );
		}

        public function action_admin_bar_menu_999( $wp_admin_bar ) {
            $wp_admin_bar->add_node( [
                'id'    => Deploy::$slug,
                'title' => Deploy::get_template( 'admin-bar', [ 'content' => Deploy::__( 'Deploy' ) ] ),
                'href'  => get_admin_url( null, sprintf( self::URL, Deploy::$slug . '-logs' ) )
            ] );
        }

        protected function enqueue_style( $style ) {
            wp_register_style( Deploy::$slug . '-' . $style, Deploy::$url . 'assets/css/'  . $style . '.css', [], Deploy::$version );
            wp_enqueue_style(  Deploy::$slug . '-' . $style );
        }

        public function action_admin_enqueue_scripts( $page ) {
            if ( false !== strpos( $page, Deploy::$slug . '-logs' ) ) $this->enqueue_style( 'logs' );
            if ( is_admin_bar_showing() ) $this->enqueue_style( 'admin-bar' );
        }

        public function action_wp_enqueue_scripts() {
            if ( ! is_admin_bar_showing() ) return;
            $this->enqueue_style( 'admin-bar' );
        }

        public function logs() {
            $table = new Table();
            $table->prepare_items();

            echo Deploy::get_template( 'logs', array(
                'header' => Deploy::__( 'Deploy Logs' ),
                'table'  => $table,
            ) );
        }

		public function settings() {
			echo Deploy::get_template( 'page', [
			    'settings'     => Deploy::$slug,
				'option_group' => Deploy::$slug,
				'page'         => Deploy::$slug
			] );
		}

		public function section() {
			echo Deploy::get_template( 'section', [
				'content' => Deploy::__( 'Settings' )
			] );
		}

        public function emails() {
            $emails   = $this->emails;
            $emails[] = '';
            foreach( $emails as $id => $email )
                self::input( [
                    'type'  => 'email',
                    'name'  => Deploy::$slug . "[emails][$id]",
                    'value' => $email,
                    'after' => '<br />'
                ] );
        }

        public function test() {
            self::input( [
                'type'  => 'checkbox',
                'name'  => Deploy::$slug . '[send]',
                'after' => Deploy::__( 'Send test email' )
            ] );
            echo '<br />';
            self::input( [
                'type'  => 'checkbox',
                'name'  => Deploy::$slug . '[pull]',
                'after' => Deploy::__( 'Force' ) . ' ' . Deploy::get_template( 'code', [ 'content' => 'git pull' ] )
            ] );
        }

        public function status() {
            foreach( array_reverse( [ Deploy::__( 'Disable' ), Deploy::__( 'Enable' ) ], true ) as $value => $name )
                self::input( [
                    'type'    => 'radio',
                    'name'    => Deploy::$slug . '[status]',
                    'value'   => (string)$value,
                    'checked' => checked( $this->status, (bool)$value, false ),
                    'after'   => $name . '<br />'
                ] );
        }

		static public function select( $args ) {
            $args = wp_parse_args( $args, [
                'selected' => null,
                'desc'     => ''
            ] );
			extract( $args, EXTR_SKIP );

            echo Deploy::get_template( 'select', [
                'name'     => $name,
                'options'  => $options,
                'selected' => $selected,
                'desc'     => $desc
            ] );
		}

		static public function input( $args ) {
            $args = wp_parse_args( $args, [
                'type'  => 'input',
                'class' => 'regular-text'
            ] );
			extract( $args, EXTR_SKIP );

			echo Deploy::get_template( 'input', [
					'atts' => self::implode( [
							'type'  => isset( $type )  ? $type  : '',
							'class' => isset( $class ) ? $class : '',
							'name'  => isset( $name )  ? $name  : '',
							'value' => isset( $value ) ? $value : ''
						]
					),
					'checked' => isset( $checked ) ? $checked : '',
					'before'  => isset( $before )  ? $before  : '',
					'after'   => isset( $after )   ? $after   : '',
					'desc'    => isset( $desc )    ? $desc    : ''
				]
			);
		}

		static public function implode( $atts = [] ) {
			array_walk( $atts, function ( &$value, $key ) {
				$value = sprintf( '%s="%s"', $key, esc_attr( $value ) );
			} );

			return implode( ' ', $atts );
		}

		public function sanitize( $settings ) {
            array_walk( $settings, 'sanitize_text_field' );
            extract( $settings, EXTR_SKIP );

			if ( ! in_array( $host, $hosts = [ 'github', /*'gitlab',*/ 'bitbucket', 'stash' ] ) ) {
                add_settings_error(
                    Deploy::$slug,
                    'settings_updated',
                    sprintf( Deploy::__('Wrong hosting name: %s. Supported: %s' ),
                        Deploy::get_template( 'code', [ 'content' => $host ] ),
                        Deploy::get_template( 'code', [ 'content' => implode( ', ', $hosts ) ] ) ),
                    'error'
                );
                $host = $this->host;
			}

            if ( ! is_executable( $git ) ) {
                add_settings_error(
                    Deploy::$slug,
                    'settings_updated',
                    sprintf( Deploy::__('Wrong git path: %s. This is not the file or is not executable.' ),
                        Deploy::get_template( 'code', [ 'content' => $git ] ) ),
                    'error'
                );
                $git = $this->git;
            }


            $dir = trailingslashit( $dir );
            if ( ! file_exists($dir . '.git' ) ) {
                add_settings_error(
                    Deploy::$slug,
                    'settings_updated',
                    sprintf( Deploy::__('Wrong repository path: %s. There is no %s directory.' ),
                        Deploy::get_template( 'code', [ 'content' => $dir ] ),
                        Deploy::get_template( 'code', [ 'content' => '.git' ] ) ),
                    'error'
                );
                $dir = $this->dir;
            }

            $sanitized_emails = [];
            foreach( $emails as $email )
                if ( ! empty( $email ) )
                    if ( ! filter_var( $email, FILTER_VALIDATE_EMAIL ) )
                        add_settings_error(
                            Deploy::$slug,
                            'settings_updated',
                            sprintf( Deploy::__('Wrong email address: %s.' ),
                                Deploy::get_template( 'code', [ 'content' => $email ] ) ),
                            'error'
                        );
                    else $sanitized_emails[] = $email;

            $settings = [
                'host'   => $host,
                'git'    => $git,
                'dir'    => $dir,
                'branch' => $branch,
                'token'  => $token,
                'route'  => $route,
                'emails' => $sanitized_emails,
                'status' => (bool)$status
            ];

            if ( isset( $send ) and ! empty( $emails ) ) {
                $subject = '[' . Deploy::__( 'Deploy' ) . '] ' . get_site_url() . ' ' . Deploy::__( 'Test' );
                $message = Deploy::get_template( 'pre', [ 'content' => print_r( $settings, true ) ] );

                $mail = new Mail( $emails, $subject, $message );
                if ( $mail->send() )
                    add_settings_error(
                        Deploy::$slug,
                        'settings_updated',
                        sprintf( Deploy::__('Email sent successfully to all recipients: %s.' ),
                            Deploy::get_template( 'code', [ 'content' => implode( ', ', $sanitized_emails ) ] ) ),
                        'updated'
                    );
                else
                    add_settings_error(
                        Deploy::$slug,
                        'settings_updated',
                        sprintf( Deploy::__('Something went wrong when sending email to recipients: %s.' ),
                            Deploy::get_template( 'code', [ 'content' => implode( ', ', $sanitized_emails ) ] ) ),
                        'error'
                    );
            }

            if ( isset( $pull ) ) {
                chdir( $dir );
                add_settings_error(
                    Deploy::$slug,
                    'settings_updated',
                    sprintf( Deploy::__('Log: %s.' ),
                        Deploy::get_template( 'code', [ 'content' => shell_exec( $git . ' pull 2>&1' ) ] ) ),
                    'notice'
                );
            }

            if ( empty( get_settings_errors( Deploy::$slug ) ) )
                add_settings_error(
                    Deploy::$slug,
                    'settings_updated',
                    Deploy::__('Settings saved.' ),
                    'updated'
                );

            return $settings;
		}

        public function get_class_vars( $return = '' ) {
            $vars = get_class_vars( self::class );
            if ( 'names'  === $return ) return array_keys(   $vars );
            if ( 'values' === $return ) return array_values( $vars );
            return $vars;
        }

        public function get_object_vars( $return = '' ) {
            $vars = get_object_vars( $this );
            if ( 'names'  === $return ) return array_keys(   $vars );
            if ( 'values' === $return ) return array_values( $vars );
            return $vars;
        }

        public function get() {
            return get_option( Deploy::$slug );
        }

        public function add() {
            if ( $this->get() ) return $this->update();
            return add_option( Deploy::$slug, $this->get_object_vars() );
        }

        public function update() {
            if ( ! $this->get() ) return $this->add();
            return update_option( Deploy::$slug, $this->get_object_vars() );
        }

        public function delete() {
            if ( ! $this->get() ) return false;
            return delete_option( Deploy::$slug );
        }
	}
}
