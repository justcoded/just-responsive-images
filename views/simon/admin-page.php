<div class="wrap">
	<h1>Simon Says Settings</h1>
	<form method="post" action="options.php" enctype="multipart/form-data">
		<?php
		settings_fields( 'jri-simon-page' );
		do_settings_sections( 'jri-simon-page' );
		submit_button();
		?>
	</form>
</div>
