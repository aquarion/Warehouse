<?PHP

include("../lib/stmt_as_array.inc.php");
include("../lib/item.class.php");
$config = parse_ini_file("../config.ini", true);

$db = new mysqli($config['db']['host'], $config['db']['username'], $config['db']['password'], $config['db']['db']);

$item = new Item($db);

$error = false;

if (isset($_POST['description'])){
	if($_POST['description']){
		if($_POST['captcha'] != 12){
			$error = "Basic humanity test is required";
		}
		if($_POST['description'] != strip_tags($_POST['description'])){
			$_POST['description'] = strip_tags($_POST['description']);
			$error = "HTML is contraindicated, please review";
		}

		if($_POST['author'] != strip_tags($_POST['author'])){
			$_POST['author'] = strip_tags($_POST['author']);
			$error = "HTML is contraindicated, please review";
		}

		if($error === false){
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

$f_contents = file("../lib/agents.txt"); 
$agent = $f_contents[rand(0, count($f_contents) - 1)];
$agent = ucwords($agent);

?><!DOCTYPE html>
<html>
<head>
	<title>The Basement - A Grue Liability Zone</title>
	<link href='//fonts.googleapis.com/css?family=PT+Sans+Caption|Orbitron|Share+Tech+Mono' rel='stylesheet' type='text/css'>
	<link href='style.css' type='text/css' rel='stylesheet'>

	<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
	<script type="text/javascript" src="scripting.js"></script>
</head>

<body>
<header>
	<h1>The Basement</h1>

	<nav>
		<a href="#" rel="item" class="selected">Item</a> | 
		<a href="#" rel="about">About</a> | 
		<a href="#" rel="add">Add Item</a> | 
		<a href="/" id="random">Random</a>
	</nav>
</header>

<?PHP

if (isset($_GET['entry'])){
	echo "<div id=\"item_added\" class=\"hidden\">Submission acknowledged. Please await pickup, Agent $agent</div>";
}
?>

<div class="contentbox">

<!--- Item Tab -->
<div id="item" class="displayitem">
	<p>
		Inside a crate on level <span title="<?PHP echo $item->score ?>"><?PHP  echo $item->level ?></span>, you find <br/>
		<?PHP echo nl2br($item->description); ?>
	</p>
	<cite id="author"><? echo $item->author ?></cite>

	[ <a href="/?uid=<?PHP echo $item->uniqid?>" id="permalink" title="Link to this item">Link</a>
	| <span id="voted"> <a href="#" id="voteup" rel="<?PHP echo $item->uniqid?>" class="vote voteup">I like this</a>
	| <a href="#" id="votedown" rel="<?PHP echo $item->uniqid?>" class="vote votedown">I don't like this</a></span> ]

</div>

<!-- In a box on level 42, you find a form to add new items -->

<form id="add" method="POST" action="." class="displayitem hidden">
<h2>Warehouse form 23521/1&alpha; &mdash; New Acquisition</h2>

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
		printf('<p id="error" class="error">%s</p>', $error);
	}
?>

<label>Description</label>
<div class="description">
	Inside a crate on level 1, you find ...<br/>
	<textarea cols="40" rows="6" name="description"><?PHP echo isset($_POST['description']) ? $_POST['description'] : '' ?></textarea>
</div>

<div><label>Author</label><input type="text" name="author" value="<?PHP echo isset($_POST['author']) ? $_POST['author'] : '' ?>"/></div>
<div><label>5 + 7 =</label><input type="number" name="captcha" value=""/> (Proving your humanity<sup>[1]</sup>)</div>
<input type="submit" value="Insert" />

<p><small>[1] This doesn't actually prove your humanity, just your ability to deal with integer numerical addition, but our research
has suggested that this arrests the progress of both artifical item generators and anyone with a maths degree, and that the
latter usually get over it in time.</small></p>
</form>

<!-- In a book on level 23, you find out about the basement -->

<div id="about"  class="displayitem hidden">
<h2>About</h2>
<p>Once upon a time, the deepest dungeons of SJGames' Warehouse 23's basement were free to rummage in by the forces of the internet. Alas, technical problems took down the basement, and it never really recovered.</p>

<p>This is an attempt to do the same thing. We don't have the ten years of submissions the old one did, nor do we have the vast vault of imagination that the old one was seeded with. We do have vote buttons, though. </p>

<p>This was created by <a href="mailto:nicholas+basement@istic.net">Nicholas Avenell</a> of <a href="http://istic.net">Istic.Networks</a>. It runs on stickyback plastic, string, PHP, MySQL &amp; jQuery. Its favourite letter is v, and its favourite position is the cookie monster. It's not endorsed by <a href="http://www.sjgames.com">SJ Games</a> or anyone involved with the original <a href="http://warehouse23.com">Warehouse 23</a> basement, and we don't accept liablity for any distress caused by items you find in it.</p>
</div>

</div>

<!-- In a box on level five, you find big brother, watching you -->

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
