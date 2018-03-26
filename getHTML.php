<?

function getRawHTML() {
	ini_set('user_agent','Mozilla/4.0 (compatible; MSIE 6.0)'); 

	$optionsContext = array(
		'http'=>array(
			'method'=>"GET",
			'header'=>"Content-type: text/html"
		)
	);

	$context = stream_context_create($optionsContext);

	$rawHTML = file_get_contents('http://wonderworldweb.over-blog.com/', false, $context);

	print_r($rawHTML);
}
