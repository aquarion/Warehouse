<?PHP

include("../lib/stmt_as_array.inc.php");
include("../lib/item.class.php");
$config = parse_ini_file("../config.ini", true);

$db = new mysqli($config['db']['host'], $config['db']['username'], $config['db']['password'], $config['db']['db']);

$item = new Item($db);

if (isset($_POST['description'])){
	$id = $item->create($_POST['author'], $_POST['description']);
	header("location: /?entry=true");
	die();
}


if(isset($_GET['uid'])){
	$item->fetch_by_uniqid($_GET['uid']);
	if(!$item->description){
		$item->description="No Item Found";
	}
} else {
	$item->fetch_random();
}
?><!DOCTYPE html>
<html>
<head>
<title>The Basement - A Grue Liability Zone</title>
<link href='http://fonts.googleapis.com/css?family=PT+Sans+Caption|Orbitron' rel='stylesheet' type='text/css'>

<style type="text/css">

body {
	background: #222;
	color: #EDEDED;
	font-family: 'PT Sans Caption', sans-serif;
	text-shadow: #000 0.1em 0.1em 0.3em;
}

header {
	text-align: center;
	margin-bottom: 2em;
}

header h1 {
	color: #222;
	font-size: 42pt;
	text-shadow: #CCC 0 -0.25em 1.5em;
	font-family: 'Orbitron', sans-serif;
}

.displayitem {
}

.hidden {
	display: none;
}

nav {
	color: #666;
}

nav a {
	color: #777;
	text-decoration: none;
}

nav a.selected {
	color: #DDD;
}

nav a:hover {
	color: #AAA;
}

label {
	float: left;
	display: block;
	width: 10em;
}

.description {
	padding-left: 10em;
}

.contentbox {
	color: #333;
	background: #EEE;
	text-shadow: none;
	padding: 2em;
	box-shadow: #666 7px 7px 14px inset, #000 7px 7px 14px;
	width: 900px;
	margin-left: auto;
	margin-right: auto;
}

.voteup {
	color: green;
}

.votedown {
	color: red;
}

#item_added {
	background: green;
	color: white;
	width: 100%;
	height: 2em;
	line-height: 2em;
	position: absolute;
	top: 0;
	left: 0;
	text-align: center;
	box-shadow: #000 7px 0 14px;
}

</style>

<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
<script type="text/javascript">
$("document").ready(function(){

	$('nav a').click(function(){
		if ($(this).hasClass("selected")){
			return;
		}
		console.log("Clicked "+this);
		var showme = $("#"+$(this).attr("rel"));
		$(".displayitem").slideUp("slow", function(){
			console.log(showme);
		});
		showme.slideDown("slow");
		$('nav a').removeClass("selected");
		$(this).addClass("selected");

	});

	$('.vote').click(function(){
		if ($(this).hasClass("voteup")){
			vote = "up";
		} else {
			vote = "down";
		}

		uid = $(this).attr("rel");

		$.ajax({
			url: "vote.php",
			data: {uid: uid, vote: vote},
			type: 'POST'
		}).done(function(data) { 
			$("#voted").html(data);
		});

	});

	if ($('#item_added')){
		$('#item_added').fadeIn();
		setTimeout("hide_added()", 3000);
	}

});

function hide_added(){
	$('#item_added').fadeOut('slow');
}

</script>

</head>

<body>
<header>
	<h1>The Basement</h1>

	<nav>
		<a href="#" rel="item" class="selected">Item</a> | 
		<a href="#" rel="about">About</a> | 
		<a href="#" rel="add">Add Item</a> | 
		<a href="/">Random</a>
	</nav>
</header>

<?PHP

if (isset($_GET['entry'])){
	echo "<div id=\"item_added\" class=\"hidden\">Thanks, your entry has been added</div>";
}
?>

<div class="contentbox">
<div id="item" class="displayitem">
<p>
	Inside a crate on level 1, you find <br/>
	<? echo nl2br($item->description); ?>
</p>
<cite><? echo $item->author ?></cite>

[ <a href="/?uid=<?PHP echo $item->uniqid?>" title="Link to this item">Link</a>
| <span id="voted"> <a href="#" rel="<?PHP echo $item->uniqid?>" class="vote voteup">I like this</a>
| <a href="#" rel="<?PHP echo $item->uniqid?>" class="vote votedown">I don't like this</a></span> ]

</div>

<form id="add" method="POST" action="." class="displayitem hidden">
<h2>Add Item</h2>

<h3>Guidelines:</h3>

<ul>
	<li>Keep it short &amp; simple</li>
	<li>Keep to the same style.</li>
	<li>Less is more. Leave it to the reader's imagination. No, more than that.</li>
	<li>We already have Jimmy Hoffa, Elvis and Lord Lucan</li>
	<li>"Seemingly Ordinary" is seemingly overused.</li>
	<li>Originality beats Hitchhikers Guide references.</li>
	<li>Keep it clean.</li>
</ul>


<label>Description</label>
<div class="description">
	Inside a crate on level 1, you find ...<br/>
	<textarea cols="40" rows="6" name="description"></textarea>
</div>

<label>Author</label><input type="text" name="author" />
<input type="submit" value="Insert" />
</form>

<div id="about"  class="displayitem hidden">
<h2>About</h2>
<p>Once upon a time, the deepest dungeons of SJGames' Warehouse 23's basement were free to rummage in by the forces of the internet. Alas, technical problems took down the basement, and it never really recovered.</p>

<p>This is an attempt to do the same thing. We don't have the ten years of submissions the old one did, nor do we have the vast vault of imagination that the old one was seeded with. We do have vote buttons, though. </p>

<p>This was created by <a href="mailto:nicholas+basement@istic.net">Nicholas Avenell</a> of <a href="http://istic.net">Istic.Networks</a>. It runs on stickyback plastic, string, PHP, MySQL &amp; the Amazon Cloud. Its favourite letter is v, and its favourite position is the cookie monster. It's not endorsed by <a href="http://www.sjgames.com">SJ Games</a> or anyone involved with the original <a href="http://warehouse23.com">Warehouse 23</a> basement, and we don't accept liablity for any distress caused by items you find in it.</p>
</div>

</div>

<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-30303090-1']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>

</body>


</html>
