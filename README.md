# php-file

Easily display files and folders in a mobile friendly, clean and cool way. Use the api from `file.php` wherever you need a listing and you are good to go.


## Usage

```php
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
```
