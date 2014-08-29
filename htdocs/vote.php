<?PHP

include("../lib/stmt_as_array.inc.php");
include("../lib/item.class.php");
$config = parse_ini_file("../config.ini", true);

$db = new mysqli($config['db']['host'], $config['db']['username'], $config['db']['password'], $config['db']['db']);

class Vote {

	function __construct($db){
		$this->db = $db;
	}


	function vote($uid, $vote){
		$item = new Item($this->db);
		$item->fetch_by_uniqid($uid);
		if(!$item->description){
			die("Couldn't find item");
		}
               $headers = apache_request_headers();
                if (array_key_exists('X-Forwarded-For', $headers)){
                        list($ip) = explode(" ", $headers['X-Forwarded-For'], 1);
                } else {
                        $ip=$_SERVER["REMOTE_ADDR"];
                }
		
		$ua = $_SERVER['HTTP_USER_AGENT'];
		
		$id = md5($ua.$ip.$uid);

		if($vote == "up"){
			$vote_up = 1;
			$vote_down = 0;
		} elseif ($vote == "down"){
			$vote_up = 0;
			$vote_down = 1;
		} else {
			die("Invalid value for vote");
		}

		$q = $this->db->prepare("REPLACE into `vote` (id, vote_up, vote_down, item_id, date_added) values (?, ?, ?, ?, NOW())");
	
                if(!$q){
                        die($this->db->error);
                }
	
		$q->bind_param("siii", $id, $vote_up, $vote_down, $item->id);
                $q->execute();

		$item->update_votes();

	}
}

$vote = new Vote($db);
$vote->vote($_POST['uid'], $_POST['vote']);

echo "Noted. Thank you.";
