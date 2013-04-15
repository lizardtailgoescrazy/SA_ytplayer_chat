<?php
	function toArray(SimpleXMLElement $xml) {
    $array = (array)$xml;
	foreach ( array_slice($array, 0) as $key => $value ) {
        if ( $value instanceof SimpleXMLElement ) {
            $array[$key] = empty($value) ? NULL : toArray($value);
        }
    }
    return $array;
}
	
	function constructChain(){
	
		$searchTerm = $_GET["q"];
		$searchTerm = str_replace(" ", "+", $searchTerm);
		$tempUrl = "http://gdata.youtube.com/feeds/api/videos?q=".$searchTerm."&orderby=published&start-index=11&max-results=10&v=2";
		//$tempUrl = "http://gdata.youtube.com/feeds/api/videos/".$url."?v=2";
		$content = file_get_contents($tempUrl);
		$xml = simplexml_load_string($content);
		$xmlArray = toArray($xml);
		if(isset($xmlArray['entry'])){
			$xmlArray = $xmlArray['entry'];
			print("<ul>");	
			for($i=0 ; $i < sizeof($xmlArray) ; $i++){
				$xmlElement = $xmlArray[$i];
				$xmlNodes = (array)($xmlElement);
				$srcBits = explode('/', $xmlNodes['content']['src']);
				$srcBits = explode('?version', $srcBits[4]);
				$nextVideoId = $srcBits[0];
				
				print('<li class="result" destination="'.$nextVideoId.'">'.$xmlNodes['title']."</li>");
				//print("<br><br><br>".$xmlNodes['content']['src']."<br><br><br>");
				
			}
			print("</ul>");
		}
		else{
			//do nothing
		}
	}
	
	constructChain();

?>