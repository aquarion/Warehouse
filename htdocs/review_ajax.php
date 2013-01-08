<?PHP

include("../lib/stmt_as_array.inc.php");
include("../lib/item.class.php");
$config = parse_ini_file("../config.ini", true);

include("../lib/force_auth.php");

$db = new mysqli($config['db']['host'], $config['db']['username'], $config['db']['password'], $config['db']['db']);


$item = new Item($db);

if(isset($_POST['uniqid'])){
	$item->fetch_by_uniqid($_POST['uniqid']);
	if(!$item->uniqid){
		$item->description="No Item Found";
	} else {
		if ($_POST['action'] == "approve"){
			$item->approve();
		} elseif ($_POST['action'] == "remove"){
			$item->remove();
		}
	}
}

print json_encode($item);
