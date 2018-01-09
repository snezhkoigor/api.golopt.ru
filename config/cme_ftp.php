<?php

return [
	'url' => env('CME_FTP_URL', 'ftp.cmegroup.com'),
	'user' => env('CME_FTP_LOGIN', 'anonymous'),
	'save_path' => env('CME_PARSER_FORWARD_POINTS_SAVE_FOLDER', 'history/forward_points'),
	'ftp_folder' => env('CME_FTP_CONTENTS_FORWARD_POINTS_FOLDER', 'forwardpoints'),
];
