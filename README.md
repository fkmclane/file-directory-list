# Free Super Clean PHP File Directory Listing Script

Easily display files and folders in a mobile friendly, clean and cool way. Use the api from `file.php` wherever you need a listing and you are good to go.


## Usage

```php
<?php require_once 'file.php'; ?>
<!DOCTYPE html>
<html>
	<head>
		<title>Directory Listing</title>

		<?php echo file_head(); ?>
	</head>

	<body>
		<?php echo file_list('.'); ?>
	</body>
</html>
```
