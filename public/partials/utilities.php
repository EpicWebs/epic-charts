<?php
    function returnGraphLabels($labelData) {
		$labels = array();
		
		foreach($labelData as $value) {
			array_push($labels, "'" . $value . "'");
		}
		
		return implode(",", $labels);
	}
	
	function returnGraphData($dataSets) {
		$data = array();
		
		foreach($dataSets as $key => $value) {
			array_push($data, "'" . $value['value'] . "'");
		}
		
		return implode(",", $data);		
	}
	
	function returnGraphBackgroundColours($dataSets) {
		$backgroundColor 	= array();
		
		foreach($dataSets as $key => $value) {
			array_push($backgroundColor, "'" . $value['background_color'] . "'");
		}
		
		return implode(",", $backgroundColor);		
	}
	
	function returnPointColours($dataSets) {
		$colors 	= array();
		
		foreach($dataSets as $key => $value) {
            $color = implode(",", hex2RGB($value['background_color']));
            $color = "rgba(" . $color . ", 0.6)";
            
			array_push($colors, "'" . $color . "'");
		}
		
		return implode(",", $colors);
	}

	function nice_looking_code($string) {
		echo highlight_string("<?php\n\$data =\n" . var_export($string, true) . ";\n?>");
	}
	
	function hex2RGB($hexStr, $returnAsString = false, $seperator = ',') {
		$hexStr = preg_replace("/[^0-9A-Fa-f]/", '', $hexStr); // Gets a proper hex string
		$rgbArray = array();
		if (strlen($hexStr) == 6) { //If a proper hex code, convert using bitwise operation. No overhead... faster
			$colorVal = hexdec($hexStr);
			$rgbArray['red'] = 0xFF & ($colorVal >> 0x10);
			$rgbArray['green'] = 0xFF & ($colorVal >> 0x8);
			$rgbArray['blue'] = 0xFF & $colorVal;
		} elseif (strlen($hexStr) == 3) { //if shorthand notation, need some string manipulations
			$rgbArray['red'] = hexdec(str_repeat(substr($hexStr, 0, 1), 2));
			$rgbArray['green'] = hexdec(str_repeat(substr($hexStr, 1, 1), 2));
			$rgbArray['blue'] = hexdec(str_repeat(substr($hexStr, 2, 1), 2));
		} else {
			return false; //Invalid hex color code
		}
		return $returnAsString ? implode($seperator, $rgbArray) : $rgbArray; // returns the rgb string or the associative array
	}