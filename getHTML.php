<?

libxml_use_internal_errors(true);

function getRawHTML($url) {

	$optionsContext = array(
		'http'=>array(
			'user_agent'=> 'Mozilla/4.0 (compatible; MSIE 6.0)',
			'method'=>"GET",
			'header'=>"Content-type: text/html"
		)
	);

	$context = stream_context_create($optionsContext);

	$rawHTML = file_get_contents($url, false, $context);

	return $rawHTML;
}

$document = new DOMDocument();
$document->loadHTML(getRawHTML("http://le-multi-gagnant.over-blog.com/"));

$xpath = new DOMXPath($document);

$query = "//html/body/div[@id='global']/div[@class='main']/div[@class='wrapper']/section/article/header/h2/a/@href";

$answer = $xpath->query($query);

$article = new DOMDocument();

foreach ($answer as $row) {
	echo "<h1 style='color:green'>$row->value </h1><br><br>";

	$article->loadHTML(getRawHTML($row->value));

	$xpathArticle = new DOMXPath($article);

	$queryAuteur = "//html/body/div[@id='global']/div[@class='main single']/div[@class='wrapper']/section/aside/div[@id='ob-comments']/div[@class='ob-list']/div[@class='ob-comment']/p[@class='ob-info']/span[@class='ob-user']/span[@class='ob-name']/span[@class='ob-website']";

	$answerAuteur = $xpathArticle->query($queryAuteur);

	foreach ($answerAuteur as $rowAuteur) {
		echo $rowAuteur->textContent."<br><br>";
	}
}