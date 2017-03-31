<?php
/* @var $meta array */
?>
<table class="form-table">
	<?php if ( ! empty( $meta['width'] ) ) : ?>
		<tr>
			<td><strong>Full size</strong></td>
			<td><strong><?php echo esc_html( "{$meta['width']} x {$meta['height']}" ); ?> px</strong>
				<?php if ( ! empty( $meta['gcd'] ) ) : ?>
					( <?php if ( ! empty( $meta['avr_ratio'] ) ) { echo '~'; } ?>
					<?php echo esc_html( "{$meta['x_ratio']}:{$meta['y_ratio']}" ); ?> )
				<?php endif; ?>
			</td>
		</tr>
	<?php endif; ?>

	<?php if ( ! empty( $meta['sizes'] ) ) : ?>
		<tr>
			<td colspan="2"><em>Additional sizes</em></td>
		</tr>
		<?php foreach ( $meta['sizes'] as $key => $params ) : ?>
			<tr style="border-top: 1px solid #eee;">
				<td><?php echo esc_html( $key ); ?></td>
				<td><?php echo esc_html( "{$params['width']} x {$params['height']}" ); ?> px</td>
			</tr>
		<?php endforeach; ?>
	<?php else : ?>
		<tr>
			<td colspan="2"><em>No additional sizes.</em><br>
				<small>It seems you didn't configure your image sizes correctly.
					You should configure responsive image sizes and regenerate thumnbails after that.
				</small>
			</td>
		</tr>
	<?php endif; ?>
</table>
