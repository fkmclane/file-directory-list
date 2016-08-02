<?php require_once 'file.php'; ?>
<!DOCTYPE html>
<html>
	<head>
		<title><?php echo dirname(__FILE__); ?></title>

		<style>
			body {
				background: #f5f5f5;
			}
		</style>

		<?php echo file_head(); ?>
	</head>

	<body>
		<?php echo file_list(dirname(__FILE__)); ?>
	</body>
</html>
