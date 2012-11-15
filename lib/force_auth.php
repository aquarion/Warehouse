<?PHP

$authed = false;

if(!isset($_SERVER['PHP_AUTH_USER']) || !isset($_SERVER['PHP_AUTH_PW'])){
	$authed = false;
} else {
	$u = $_SERVER['PHP_AUTH_USER'];
	$p = $_SERVER['PHP_AUTH_PW'];
	if($u == $config['auth']['username'] && $p = $config['auth']['password']){
		$authed = true;
	} else {
		$authed = false;
	}
}

if (!isset($_SERVER['PHP_AUTH_USER'])) {
    header('WWW-Authenticate: Basic realm="Review"');
    header('HTTP/1.0 401 Unauthorized');
    exit;
}

