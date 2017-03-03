<input type="text"
	   class="regular-text"
	   name="<?php echo esc_attr( $name ); ?>"
	   id="<?php echo esc_attr( preg_replace( '/[^a-z0-9\-\_]/i', '-', $name ) ); ?>"
	   value="<?php echo esc_attr( $value ); ?>"/>
