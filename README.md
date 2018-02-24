# php-file

Easily display files and folders in a mobile friendly, clean and cool way. Use the api from `file.php` wherever you need a listing and you are good to go.


## Usage

```php
<?php require_once 'file.php'; ?>
<!DOCTYPE html>
<html>
	<head>
		<title>php-file</title>

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
			<?php echo file_list(dirname(__FILE__), 'light', '', 'php-file'); ?>
		</div>
		<div class="dark">
			<?php echo file_list(dirname(__FILE__), 'dark', '', 'php-file'); ?>
		</div>
	</body>
</html>
```
