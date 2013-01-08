<?PHP

include("../lib/stmt_as_array.inc.php");
include("../lib/item.class.php");
$config = parse_ini_file("../config.ini", true);

$db = new mysqli($config['db']['host'], $config['db']['username'], $config['db']['password'], $config['db']['db']);

$item = new Item($db);

$error = false;

if (isset($_POST['description'])){
	if($_POST['description']){
		if($_POST['description'] != strip_tags($_POST['description'])){
			$_POST['description'] = strip_tags($_POST['description']);
			$error = "HTML is contraindicated, please review";
		} else {
			$id = $item->create($_POST['author'], $_POST['description']);
			header("location: /?entry=true");
			die();
		}
	} else {
		$error = "Empty boxes are contraindicated";	
	}
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
<link href='style.css' type='text/css' rel='stylesheet'>
<style type="text/css">


</style>

<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
<script type="text/javascript">
$("document").ready(function(){

	$('nav a').click(function(){
		if ($(this).hasClass("selected")){
			return;
		}
		var showme = $("#"+$(this).attr("rel"));
		$(".displayitem").slideUp("slow");
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

<?PHP if($error){ ?>

	$(".displayitem").hide();
	$("#add").slideDown();

<?PHP } ?>

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
		<a href="/  id="random">Random</a>
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
	Inside a crate on level <?PHP  echo $item->level ?>, you find <br/>
	<?PHP echo nl2br($item->description); ?>
</p>
<cite id="author"><? echo $item->author ?></cite>

[ <a href="/?uid=<?PHP echo $item->uniqid?>" id="permalink" title="Link to this item">Link</a>
| <span id="voted"> <a href="#" id="voteup" rel="<?PHP echo $item->uniqid?>" class="vote voteup">I like this</a>
| <a href="#" id="votedown" rel="<?PHP echo $item->uniqid?>" class="vote votedown">I don't like this</a></span> ]

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
	<li>Boxes within in boxes within boxes within recursion within overuse within warehouse.</li>
	<li>Originality beats Hitchhikers Guide references.</li>
	<li>Keep it clean.</li>
</ul>

<?PHP
	if($error){
		printf('<p class="error">%s</p>', $error);
	}
?>

<label>Description</label>
<div class="description">
	Inside a crate on level 1, you find ...<br/>
	<textarea cols="40" rows="6" name="description"><?PHP echo isset($_POST['description']) ? $_POST['description'] : '' ?></textarea>
</div>

<label>Author</label><input type="text" name="author" value="<?PHP echo isset($_POST['author']) ? $_POST['author'] : '' ?>"/>
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
