<?php

namespace jri\core;

/**
 * 	Main controller
 */
class Controller
{

	/**
	 * Controller constructor.
	 */
	public function __construct()
	{
		
	}

	/**
	 * Function for render views
	 * @param string $__template   file name to be rendered
	 * @param array $__params     array of variables to be passed to the view file
	 * @return boolean
	 */
	protected function _render( $__template, $__params = array() )
	{
		extract($__params);
		include( JRI_ROOT . '/views/' . $__template . '.php' );

		return true;
	}

	/**
	 * Function for render views inside AJAX request
	 * Echo rendered content directly in output buffer
	 *
	 * @param string $template   file name to be rendered
	 * @param string $format    json|html   control which header content type should be sent
	 * @param array $params     array of variables to be passed to the view file
	 */
	protected function _renderAjax( $template = null, $format = 'json', $params = array() )
	{
		if ( $format == 'json' ) {
			$responce = json_encode($params);
			header("Content-Type: application/json; charset=" . get_bloginfo('charset'));
		}
		else {
			header("Content-Type: text/html; charset=" . get_bloginfo('charset'));
			ob_start();
			$this->_render($template, $params);
			$responce = ob_get_clean();
		}
		echo $responce;
		exit();
	}

}
