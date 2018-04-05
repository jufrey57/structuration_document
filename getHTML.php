<?
// TODO : Problème de commentaire. Un seul est pris par article. Il est overrider.

header("Content-Type: application/xml; charset=UTF-8");

libxml_use_internal_errors(true);

$tab = [];

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

$queryLinkArticle = "//html/body/div[@id='global']/div[@class='main']/div[@class='wrapper']/section/article/header/h2/a/@href";

$answerLinkArticle = $xpath->query($queryLinkArticle);

$article = new DOMDocument();

foreach ($answerLinkArticle as $row) {
	$tab[$row->value] = [];

	$article->loadHTML(getRawHTML($row->value));

	$xpathArticle = new DOMXPath($article);

	$queryTitleArticle = "//html/body/div[@id='global']/div[@class='main single']/div[@class='wrapper']/section/article/header/h1/a";

	$answerTitleArticle = $xpathArticle->query($queryTitleArticle);
	foreach ($answerTitleArticle as $rowTitleAritcle) {
		$tab[$row->value]["titre"] = trim($rowTitleAritcle->textContent);
	}

	$tab[$row->value]["commentaires"] = [];

	$queryAuteur = "//html/body/div[@id='global']/div[@class='main single']/div[@class='wrapper']/section/aside/div[@id='ob-comments']/div[@class='ob-list']/div[@class='ob-comment']/p[@class='ob-info']/span[@class='ob-user']/span[@class='ob-name']/span[@class='ob-website']";

	$answerAuteur = $xpathArticle->query($queryAuteur);

	$i = 0;
	foreach ($answerAuteur as $rowAuteur) {
		$tab[$row->value]["commentaires"][$i]["auteur"] = $rowAuteur->textContent;
		$i++;
	}

	$queryDate = "//html/body/div[@id='global']/div[@class='main single']/div[@class='wrapper']/section/aside/div[@id='ob-comments']/div[@class='ob-list']/div[@class='ob-comment']/p[@class='ob-info']/span[@class='ob-user']/span[@class='ob-date']";

	$answerDate = $xpathArticle->query($queryDate);

	$i = 0;
	foreach ($answerDate as $rowDate) {
		$tab[$row->value]["commentaires"][$i]["date"] = $rowDate->textContent;
		$i++;
	}

	$queryContenu = "//html/body/div[@id='global']/div[@class='main single']/div[@class='wrapper']/section/aside/div[@id='ob-comments']/div[@class='ob-list']/div[@class='ob-comment']/p[@class='ob-message']/span[@class='ob-text']";

	$answerContenu = $xpathArticle->query($queryContenu);

	$i = 0;
	foreach ($answerContenu as $rowContenu) {
		$tab[$row->value]["commentaires"][$i]["contenu"] = $rowContenu->textContent;
		$i++;
	}
}

$rssFeed =  '<?xml version="1.0" encoding="UTF-8"?>';
$rssFeed .= '<rss version="2.0">';
$rssFeed .= '	<channel>';
$rssFeed .= '	<title>RSS Commentaires over-blog</title>';
$rssFeed .= '	<link>http://le-multi-gagnant.over-blog.com/</link>';
$rssFeed .= '	<description>Commentaires de ce blog</description>';
$rssFeed .= '	<language>fr-fr</language>';
$rssFeed .= '	<copyright>Copyright (C) 2018 MIAGE M1 TAVU</copyright>';

$nbItems = 0;
foreach ($tab as $key => $value) {
	$nbItems = sizeof($value["commentaires"]);
	for ($i=0 ; $i < $nbItems ; $i++) { 
		$rssFeed .= '<item>';
		$rssFeed .= '	<title>'.$value['titre'].'</title>';
		$rssFeed .= '	<link>'.$key.'#'.$i.'</link>';
		$rssFeed .= '	<author>'.$value['commentaires'][$i]['auteur'].'</author>';
		$rssFeed .= '	<pubDate>'.$value['commentaires'][$i]['date'].'</pubDate>';
		$rssFeed .= '	<description>'.$value['commentaires'][$i]['contenu'].'</description>';
		$rssFeed .= '</item>';
	}

}
$rssFeed .= '	</channel>';
$rssFeed .= '</rss>';

echo $rssFeed;