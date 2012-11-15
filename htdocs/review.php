<?PHP

include("../lib/stmt_as_array.inc.php");
include("../lib/item.class.php");
$config = parse_ini_file("../config.ini", true);

include("../lib/force_auth.php");

$db = new mysqli($config['db']['host'], $config['db']['username'], $config['db']['password'], $config['db']['db']);

$item = new Item($db);

$items = $item->unreviewed();

function print_form($item){
	$template = <<<EOW
<div class="item" id="%s">
	<div>Id: %s</div>
	<div>Author: %s</div>
	<div>Created: %s</div>
	<div>Content:</div><div>%s</div>
	<div><a href="#" class="approve" rel="%1\$s">Approve</a> | <a href="#" class="deny" rel="%1\$s">Delete</a></div>
</div>
EOW;
	$desc = htmlentities($item['description']);
	printf($template, $item['uniqid'], $item['id'], $item['author'], $item['date_added'], $desc, $item['uniqid']);
}
?>
<!DOCTYPE>
<html>
<head>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
<style type="text/css">
.item {
	border: 1px solid black;
	width: 800px;
	padding: 1em;
	margin: 1em;
}
</style>
<script type="text/javascript">

removeIt = function(data){
	console.log(data);
	$('#'+data.uniqid).hide("slow");
}

$(document).ready(function(){
	$(".approve").click(function(){
		console.log(this);
		$.post("review_ajax.php", { uniqid: $(this).attr("rel"), action : "approve" }, removeIt, "json");
		return false;
	});
	$(".deny").click(function(){
		$.post("review_ajax.php", { uniqid: $(this).attr("rel"), action : "remove"}, removeIt, "json");
		return false;
	});
});

</script>
</head>
<body>
<?PHP
foreach($items as $item){
	print_form($item);
}?>
</body>
