<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<div class="wrap">
	<form method="post" action="options.php">
		<?php
            settings_errors( $settings );
			settings_fields( $option_group );
			do_settings_sections( $page );
			submit_button();
		?>
	</form>
</div>