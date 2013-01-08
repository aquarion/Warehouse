<?PHP

include("../lib/stmt_as_array.inc.php");
include("../lib/item.class.php");
$config = parse_ini_file("../config.ini", true);

$db = new mysqli($config['db']['host'], $config['db']['username'], $config['db']['password'], $config['db']['db']);

$item = new Item($db);

if(isset($_GET['uid'])){
	$item->fetch_by_uniqid($_GET['uid']);
	if(!$item->description){
		$item->description="No Item Found";
	}
} else {
	$item->fetch_random();
}

unset($item->creator_ip);
unset($item->votes_up);
unset($item->votes_down);

print json_encode($item);
