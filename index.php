<?php require_once 'file.php'; ?>
<!DOCTYPE html>
<html>
	<head>
		<title><?php echo dirname(__FILE__); ?></title>

		<style>
			.light {
				background: #dadada;
				padding: 20px;
			}

			.dark {
				background: #1d1c1c;
				padding: 20px;
			}
		</style>

		<?php echo file_head(); ?>
	</head>

	<body>
		<div class="light">
			<?php echo file_list(dirname(__FILE__)); ?>
		</div>
		<div class="dark">
			<?php echo file_list(dirname(__FILE__), 'dark'); ?>
		</div>
	</body>
</html>
