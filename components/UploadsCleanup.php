<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 3/24/17
 * Time: 16:47
 */

namespace jri\components;

use jri\core\Controller;

class UploadsCleanup extends Controller {
	/**
	 * Init all wp-actions
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'adminMenu' ) );
	}

	/**
	 * Init menu item and index page for plugin
	 */
	public function adminMenu() {
		$page_title = __( 'Uploads Cleanup' );

		add_management_page( $page_title, $page_title, 'manage_options', 'jri_cleanup', array( $this, 'actionIndex' ) );
	}

	/**
	 * Render index page
	 */
	public function actionIndex() {
		$results = null;
		$uploads_dir = WP_CONTENT_DIR . '/uploads';

		if ( ! empty( $_POST['do_cleanup'] ) && check_admin_referer( 'jri_cleanup_uploads' ) ) {
			$results = $this->cleanup( $uploads_dir );
			$results = implode( '<br>', $results );
			$results = str_replace( '{indent}', '&nbsp;&nbsp;- ', $results );
		}

		if ( ! empty( $_POST['do_stats'] ) && check_admin_referer( 'jri_cleanup_uploads' ) ) {
			$results = $this->get_stats( $uploads_dir );
		}

		// load template.
		return $this->_render('cleanup/index', array(
			'results' => $results,
		));
	}

	/**
	 * Run image uploads cleanup
	 *
	 * @param string  $dir Directory to cleanup.
	 * @param integer $depth Directory depth.
	 * @return array
	 */
	protected function cleanup( $dir, $depth = 0 ) {
		$res = array();
		$res[] = str_repeat( '{indent}', $depth ) . "Start cleaning: $dir";

		$entries = scandir( $dir );
		if ( empty( $entries ) ) {
			$res[] = str_repeat( '{indent}', $depth + 1 ) . 'directory is empty';
			return $res;
		}

		$entries = array_reverse( $entries ); // (because . is after -)
		$original_names = array();

		foreach ( $entries as $entry ) {
			if ( '.' === $entry || '..' === $entry ) {
				continue;
			}

			if ( is_dir( "$dir/$entry" ) ) {
				if ( $recursive_res = $this->cleanup( "$dir/$entry", $depth + 1 ) ) {
					$res = array_merge( $res, $recursive_res );
				}
			} elseif ( is_file( "$dir/$entry" ) ) {
				$filename = "$dir/$entry";
				if ( preg_match( '/^(.*?)(\-[0-9]+x[0-9]+)\.(jpeg|jpg|png)$/i', $entry, $match ) ) {
					$base_name = str_replace( $match[0], "{$match[1]}.{$match[3]}", $entry );
					if ( ! isset( $original_names[ $base_name ] ) ) {
						$res[] = str_repeat( '{indent}', $depth + 1 ) . "Found base name: $entry";
						$original_names[ $entry ] = 1;
						continue;
					} else {
						$res[] = str_repeat( '{indent}', $depth + 2 ) . "Cleaning subsize: $entry";
						// this is a partial size - need cleanup.
						if ( wp_is_writable( $filename ) ) {
							unlink( $filename );
						} else {
							$res[] = str_repeat( '{indent}', $depth + 2 ) . 'FAILED: NOT WRITABLE!';
						}
					}
				} elseif ( preg_match( '/\.(jpeg|jpg|png)$/i', $entry, $match ) ) {
					$res[] = str_repeat( '{indent}', $depth + 1 ) . "Found base name: $entry";
					$original_names[ $entry ] = 1;
				}
			}
		}

		return $res;
	}

	/**
	 * Calculate disk space usage.
	 *
	 * @return string
	 */
	protected function get_stats() {
		// TODO: stats feature.
		return null;
	}
}
