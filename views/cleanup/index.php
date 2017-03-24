<?php
	/* @var $results string */
?>
<div class="wrap">
	<h1>Uploads Cleanup</h1>

	<?php if ( empty($results) ) : ?>
		<div class="notice notice-warning">
			<p><strong>Warning!</strong> We strongly recommend to make the backup of your uploads folder before doing any actions on this page.</p>
		</div>
	<?php else : ?>
		<div class="notice notice-success">
			<p>Operation completed.</p>
		</div>
	<?php endif; ?>

	<p>This page can help you to remove all cropped and resized images from uploads folder.</p>
	<p>This is useful in development phase, when you set different image sizes and regenerate them often.</p>
	<p>After cleanup you will need to regenerate your thumbnails one more time!
		Plugins similar to <a href="https://wordpress.org/plugins/regenerate-thumbnails/" target="_blank">Regenerate thumbnails</a> can help with this.</p>
	<p>The script will affect only filesystem and doesn't affect database at all.</p>

	<form action="" method="post" onsubmit="if( ! confirm('Are you sure you want to remove all cropped and resized images?') ) return false;">
		<?php wp_nonce_field( 'jri_cleanup_uploads' ); ?>
		<input type="hidden" name="do_cleanup" value="1">

		<!-- TODO: add feature to calculate stats
		<button type="submit" class="button button-primary">Calculate disk space usage</button>
		-->
		<button type="submit" class="button button-primary">Remove cropped/resized images</button>
	</form>

	<?php if ( ! empty( $results ) ) : ?>
		<h3>Results</h3>
		<p>
			<?php echo $results; ?>
		</p>
	<?php endif; ?>
</div>