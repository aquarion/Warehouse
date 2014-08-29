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

.item_first {
	border-color: red;
}
</style>
<script type="text/javascript">

removeIt = function(data){
	$('#'+data.uniqid).hide("fast", function(){ $('#'+data.uniqid).remove(); runUpdates() });
}

runUpdates = function (){
	console.log('Hi');
	updateCount();
	console.log($('.item')[0]);
	$($('.item')[0]).addClass('item_first');
}

updateCount = function(){
	$('#count').html($('.item:visible').length);
}

keyboardShortcuts = function(e){
	if (e.which == 121){ // y
		id = $('.item_first a.approve').attr('rel')
		$('.item_first a.approve').click();
		console.log("Yes to "+id);
	} else if (e.which == 110){ // y
		id = $('.item_first a.deny').attr('rel')
		$('.item_first a.deny').click();
		console.log("No to "+id);
	} else {
		console.log('Caught '+e.which);
	}
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

	$('body').keypress(keyboardShortcuts);

	runUpdates();
});

</script>
</head>
<body>
<div id="count">

</div>
<?PHP
foreach($items as $item){
	print_form($item);
}?>
</body>
