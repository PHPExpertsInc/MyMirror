#!/bin/env php
<?php

if (!isset($argv[1]))
{
	echo "mirror_page.php: input_url output_directory\n";
	return 1;
}

if (!isset($argv[2]))
{
	echo "ERROR: The output directory was not specificed.\n";
	return 2;
}

$output_directory = $argv[2];

// Create the directory if it doesn't exist.
if (!file_exists($output_directory))
{
	// 0775 = u+g rwx, a = rx
	// true = recursively create directories
    mkdir($output_directory, 0775, true);
}


echo "Mirroring $argv[1] to $output_directory...\n"; flush();
system("httrack --user-agent 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US) AppleWebKit/534.15 (KHTML, like Gecko) Chrome/10.0.612.3 Safari/534.15' --verbose --timeout=30 --continue --robots=0 --mirror '" . $argv[1] . "' --depth=2 '-*' '+*.css' '+*.js' '+*.jpg' '+*.gif' '+*.png' '+*.fla' '+*.swf' + '+*.flv' '+*.ico' -O " . $output_directory);
