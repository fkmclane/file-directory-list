<?php

function _file_clean_title($title) {
	return ucwords(str_replace(array('-', '_'), ' ', $title));
}

function _file_get_file_ext($filename) {
	return substr(strrchr($filename, '.'), 1);
}

function _file_display_size($bytes, $precision = 2) {
	$units = array('B', 'KB', 'MB', 'GB', 'TB');
	$bytes = max($bytes, 0);
	$pow = floor(($bytes ? log($bytes) : 0) / log(1024));
	$pow = min($pow, count($units) - 1);
	$bytes /= (1 << (10 * $pow));
	return round($bytes, $precision) . '<span class="fs-0-8 bold">' . $units[$pow] . "</span>";
}

function _file_count_dir_files($file) {
	$fi = new FilesystemIterator($file, FilesystemIterator::SKIP_DOTS);
	return iterator_count($fi);
}

function _file_get_directory_size($file) {
	$bytes = 0;

	if ($file !== false && $file !== '' && file_exists($file)) {
		foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($file, FilesystemIterator::SKIP_DOTS)) as $object) {
			$bytes += $object->getSize();
		}
	}

	return $bytes;
}

function _file_display_block(&$output, $file, $file_ext, $download) {
	$output .= "	<div class=\"block\">\n";
	$output .= "		<a href=\"$file\" class=\"$file_ext\"" . ($download ? " download=\"" . basename($file) . "\"" : "") . ">\n";
	$output .= "			<div class=\"img $file_ext\"></div>\n";
	$output .= "			<div class=\"name\">\n";
	$output .= "				<div class=\"file fs-1-2 bold\">" . basename($file) . "</div>\n";
	if ($file_ext === 'dir') {
		$output .= "				<div class=\"data upper size fs-0-7\"><span class=\"bold\">" . _file_count_dir_files($file) . "</span> files</div>\n";
		$output .= "				<div class=\"data upper size fs-0-7\"><span class=\"bold\">Size:</span> " . _file_display_size(_file_get_directory_size($file)) . "</div>\n";
	}
	else {
		$output .= "				<div class=\"data upper size fs-0-7\"><span class=\"bold\">Size:</span> " . _file_display_size(filesize($file)) . "</div>\n";
		$output .= "				<div class=\"data upper modified fs-0-7\"><span class=\"bold\">Last modified:</span> " .  date("D. F jS, Y - h:ia", filemtime($file)) . "</div>\n";
	}
	$output .= "			</div>\n";
	$output .= "		</a>\n";
	$output .= "	</div>\n";
}

function _file_build_blocks(&$output, $folder, $dir, $root, $sort_by, $sub_folders, $force_download, $ignore_empty_folders, $ignore_file_list, $ignore_ext_list) {
	$objects = array();
	$objects['directories'] = array();
	$objects['files'] = array();

	$items = scandir("$dir/$folder");

	foreach ($items as $c => $item) {
		if ($item == '..' || $item == '.')
			continue;

		if (in_array($item, $ignore_file_list))
			continue;

		if ($folder)
			$item = "$folder/$item";

		$real = "$dir/$item";

		$file_ext = _file_get_file_ext($real);
		if (!$file_ext && is_dir($file))
			$file_ext = "dir";

		if (in_array($file_ext, $ignore_ext_list))
			continue;

		if (is_dir($real)) {
			$objects['directories'][] = $item;
			continue;
		}

		$file_time = date('U', filemtime($real));

		$objects['files'][$file_time . '-' . $item] = $item;
	}

	if ($sort_by == "name_asc" || $sort_by == "date_asc")
		natsort($objects['directories']);
	elseif ($sort_by == "name_desc" || $sort_by == "date_desc")
		arsort($objects['directories']);

	foreach ($objects['directories'] as $c => $file) {
		$dir_output = '';
		$sub_output = '';

		_file_display_block($dir_output, "$dir/$file", "dir", false);

		if ($sub_folders)
			_file_build_blocks($sub_output, $file, $dir, $root, $sort_by, $sub_folders, $force_download, $ignore_empty_folders, $ignore_file_list, $ignore_ext_list);

		if (!$ignore_empty_folders || !$sub_folders || $sub_output !== '') {
			$output .= $dir_output;

			if ($sub_folders) {
				$output .= "<div class=\"sub\" data-folder=\"$root/$file\">\n";
				$output .= $sub_output;
				$output .= "</div>\n";
			}
		}
	}

	if ($sort_by == "date_asc")
		ksort($objects['files']);
	elseif ($sort_by == "date_desc")
		krsort($objects['files']);
	elseif ($sort_by == "name_asc")
		natsort($objects['files']);
	elseif ($sort_by == "name_desc")
		arsort($objects['files']);

	foreach ($objects['files'] as $t => $file) {
		$file_ext = _file_get_file_ext("$dir/$file");

		_file_display_block($output, "$root/$file", $file_ext, $force_download);
	}
}

function file_head($jquery=true, $font=true, $css="https://rawgit.com/fkmclane/php-file/master/file.css") {
	$output = "";

	if ($jquery)
		$output .= "<script src=\"//ajax.googleapis.com/ajax/libs/jquery/1.7.0/jquery.min.js\"></script>\n";

	if ($font)
		$output .= "<link href=\"//fonts.googleapis.com/css?family=Lato:400,900\" rel=\"stylesheet\" type=\"text/css\"/>\n";

	$output .= "<link href=\"$css\" rel=\"stylesheet\" type=\"text/css\"/>";

	$output .= "<script>$(document).ready(function() { $(\".php-file.sub-folders a.dir\").click(function(e) { $(this).toggleClass('open'); $('.sub[data-folder=\"' + $(this).attr('href') + '\"]').slideToggle(); e.preventDefault(); }); });</script>\n";

	return $output;
}

function file_list($dir, $color='light', $root=false, $title=false, $sort_by='name_asc', $sub_folders=true, $force_download=true, $ignore_empty_folders=true, $ignore_file_list=array('.htaccess', 'Thumbs.db', '.DS_Store', 'index.php'), $ignore_ext_list=array()) {
	if (!$title)
		$title = _file_clean_title(basename($dir));

	if (!$root)
		$root = $dir;

	$output = "";

	$output .= "<div class=\"php-file $color" . ($sub_folders ? " sub-folders" : "") . "\">\n<h1>$title</h1>\n<div class=\"wrap\">\n";

	_file_build_blocks($output, false, $dir, $root, $sort_by, $sub_folders, $force_download, $ignore_empty_folders, $ignore_file_list, $ignore_ext_list);

	$output .= "</div>\n</div>\n";

	return $output;
}

?>
