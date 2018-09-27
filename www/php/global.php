<?php 
	$validTags = array("br","h1","h2","h3","h4","h5","h6","code","samp","var", "strong", "i", "u", "p", "hr", "address", "del", "ins", "q", "small", "kbd", "wbr");

	date_default_timezone_set('Europe/Stockholm');
	
	function removeTags($str) {
		foreach ($GLOBALS['validTags'] as &$value) {
			$str = str_replace("[".$value."]","",$str);
			$str = str_replace("[/".$value."]","",$str);
			$str = str_replace("[".$value."/]","",$str);
		}
		return $str;
	}

	function htmlToTags($str) {
		foreach ($GLOBALS['validTags'] as &$value) {
			$str = str_replace("<".$value.">","[".$value."]",$str);
			$str = str_replace("</".$value.">","[/".$value."]",$str);
			$str = str_replace("<".$value."/>","[".$value."/]",$str);
		}
		return $str;
	}

	function addTags($str) {
		foreach ($GLOBALS['validTags'] as &$value) {
			$str = str_replace("[".$value."]","<".$value.">",$str);
			$str = str_replace("[/".$value."]","</".$value.">",$str);
			$str = str_replace("[".$value."/]","<".$value."/>",$str);
		}
		return $str;
	}
	
	function getImageSizeKeepAspectRatio( $image, $maxWidth, $maxHeight) {
		$imageWidth = imagesx($image);
		$imageHeight = imagesy($image);
		$imageSize['width'] = $imageWidth;
		$imageSize['height'] = $imageHeight;
		if($imageWidth > $maxWidth || $imageHeight > $maxHeight) {
			if ( $imageWidth > $imageHeight ) {
				$imageSize['height'] = floor(($imageHeight/$imageWidth)*$maxWidth);
				$imageSize['width']  = $maxWidth;
			} else {
				$imageSize['width']  = floor(($imageWidth/$imageHeight)*$maxHeight);
				$imageSize['height'] = $maxHeight;
			}
		}
		return $imageSize;
	}

	// found this code here: https://stackoverflow.com/a/3810341
	function closeTags($html) {
		preg_match_all('#<(?!meta|img|br|hr|input\b)\b([a-z]+)(?: .*)?(?<![/|/ ])>#iU', $html, $result);
		$openedtags = $result[1];
		preg_match_all('#</([a-z]+)>#iU', $html, $result);
		$closedtags = $result[1];
		$len_opened = count($openedtags);
		if (count($closedtags) == $len_opened) {
			return $html;
		}

		$openedtags = array_reverse($openedtags);
		for ($i=0; $i < $len_opened; $i++) {
			if (!in_array($openedtags[$i], $closedtags)) {
				$html .= '</'.$openedtags[$i].'>';
			} else {
				unset($closedtags[array_search($openedtags[$i], $closedtags)]);
			}
		}
		return $html;
	} 
?>
