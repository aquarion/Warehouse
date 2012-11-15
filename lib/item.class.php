<?PHP

class Item {
        public $id = 0;
        public $uniqid = false;
        public $description = "";
        public $author = "";
        public $date_added = "";
        public $votes_up = "";
        public $votes_down = "";

        private $db;

        function __construct($db){
                $this->db = $db;
        }

        function fetch_by_uniqid($id){
                $q = $this->db->prepare("SELECT * FROM item WHERE uniqid=? and removed = 0");
                $q->bind_param("s", $id);
                $q->execute();
                $item = array_pop(stmt_as_array($q));
                $this->populate($item);
        }

        function fetch_random(){
                $q = $this->db->prepare("SELECT * FROM item WHERE removed = 0 ORDER BY RAND() limit 1");
                $q->execute();
                $result = array_pop(stmt_as_array($q));
                $this->populate($result);
        }

        function create($author, $description){
                $q = $this->db->prepare("INSERT into `item` (uniqid, description, author, date_added, votes_up, votes_down, creator_ip) values (?, ?, ?, NOW(), 0, 0, ?)");

                if(!$q){
                        die($this->db->error);
                }

                $headers = apache_request_headers();
                if (array_key_exists('X-Forwarded-For', $headers)){
                        list($ip) = explode(" ", $headers['X-Forwarded-For'], 1);
                } else {
                        $ip=$_SERVER["REMOTE_ADDR"];
                }
                $q->bind_param("ssss", uniqid(), $description, $author, $ip);
                $q->execute();

        }

        function populate($array){
                foreach($array as $index => $value){
                        $this->$index = $value;
                }
        }

	function update_votes(){
		$q = $this->db->prepare("SELECT sum(vote_up) as up, sum(vote_down) as down FROM vote where item_id = ?");
	  	if(!$q){
                        die($this->db->error);
                }
		$q->bind_param("i", $this->id);
		$q->execute();

		$q->bind_result($up, $down);
		$q->fetch();
		
		$q = $this->db->stmt_init();
		$q->prepare("UPDATE `item` set votes_up = ?, votes_down = ? where id = ?");
		$q->bind_param("iii", $up, $down, $this->id);
	  	if(!$q){
                        die("db error: ".$this->db->error);
                }
		$q->execute();
	}

	function unreviewed(){
                $res = $this->db->query("SELECT * FROM item WHERE reviewed=0");
		$items = array();
		while ($row = $res->fetch_assoc()) {
			$items[] = $row;
		}
		return $items;
	}

	function approve(){
                $q = $this->db->prepare("UPDATE item SET reviewed = 1 WHERE uniqid=?");
                $q->bind_param("s", $this->uniqid);
                $q->execute();
	}

	function remove(){
                $q = $this->db->prepare("UPDATE item SET reviewed = 1, removed = 1 WHERE uniqid=?");
                $q->bind_param("s", $this->uniqid);
                $q->execute();

	}

}

