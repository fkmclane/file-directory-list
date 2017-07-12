<?php

function _file_clean_title($title) {
	return ucwords(str_replace(array('-', '_'), ' ', $title));
}

function _file_get_file_ext($filename) {
	return substr(strrchr($filename, '.'), 1);
}

function _file_format_size($file) {
	$bytes = filesize($file);
	if ($bytes < 1024)
		return $bytes.'b';
	elseif ($bytes < 1048576)
		return round($bytes/1024, 2) . 'kb';
	elseif ($bytes < 1073741824)
		return round($bytes/1048576, 2) . 'mb';
	elseif ($bytes < 1099511627776)
		return round($bytes/1073741824, 2) . 'gb';
	else
		return round($bytes/1099511627776, 2) . 'tb';
}

function _file_display_block(&$output, $file, $file_ext, $download) {
	$output .= "	<div class=\"block\">\n";
	$output .= "		<a href=\"$file\" class=\"$file_ext\"" . ($download ? " download=\"" . basename($file) . "\"" : "") . ">\n";
	$output .= "			<div class=\"img $file_ext\">&nbsp;</div>\n";
	$output .= "			<div class=\"name\">\n";
	$output .= "				<div class=\"file\">" . basename($file) . "</div>\n";
	$output .= "				<div class=\"date\">Size: " . ($file_ext == 'dir' ? "-" : _file_format_size($file)) . "<br/>Last modified: " .  date("D. F jS, Y - h:ia", filemtime($file)) . "</div>\n";
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

		$file_ext = is_dir($real) ? 'dir' : _file_get_file_ext($real);

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

function file_head($jquery=true, $font=true, $css="https://gitcdn.link/repo/fkmclane/php-file/master/file.css") {
	$output = "";

	if ($jquery)
		$output .= "<script src=\"//ajax.googleapis.com/ajax/libs/jquery/1.7.0/jquery.min.js\"></script>\n";

	if ($font)
		$output .= "<link href=\"//fonts.googleapis.com/css?family=Lato:400\" rel=\"stylesheet\" type=\"text/css\"/>\n";

	$output .= "<link href=\"$css\" rel=\"stylesheet\" type=\"text/css\"/>";

	$output .= "<script>$(document).ready(function() { $(\".php-file.sub-folders a.dir\").click(function(e) { $('.sub[data-folder=\"' + $(this).attr('href') + '\"]').slideToggle(); e.preventDefault(); }); });</script>\n";

	return $output;
}

function file_list($dir, $root=false, $title=false, $sort_by='name_asc', $sub_folders=true, $force_download=true, $ignore_empty_folders=true, $ignore_file_list=array('.htaccess', 'Thumbs.db', '.DS_Store', 'index.php'), $ignore_ext_list=array()) {
	if (!$title)
		$title = _file_clean_title(basename($dir));

	if (!$root)
		$root = $dir;

	$output = "";

	$output .= "<div class=\"php-file" . ($sub_folders ? " sub-folders" : "") . "\">\n<h1>$title</h1>\n<div class=\"wrap\">\n";

	_file_build_blocks($output, false, $dir, $root, $sort_by, $sub_folders, $force_download, $ignore_empty_folders, $ignore_file_list, $ignore_ext_list);

	$output .= "</div>\n</div>\n";

	return $output;
}

?>
