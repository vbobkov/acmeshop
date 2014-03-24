<?php
/* Vic Bobkov
* 2014-03-24
* sitemap_splitter.php
*/

// $argv[1]
$ssp1 = new SSplitter();
$ssp1->splitSitemap('sitemap.xml', 45000);

class SSplitter {
	public function __construct() {
	}



	public function splitSitemap($filename = 'sitemap.xml', $url_limit = 45000, $index_filename = 'sitemap_index.xml', $url_base = 'http://acmelectronics.com/') {
		$xml_header = '<?xml version="1.0" encoding="UTF-8"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
		$xml_footer = '</urlset>';
		$arr = $this->xml2array(file_get_contents($filename), 1, 'tag', 'http://www.sitemaps.org/schemas/sitemap/0.9:');
		$sitemaps = array();

		$j = 0;
		$sitemap = $xml_header;
		$total_size = sizeof($arr['urlset']['url']);
		for($i = 0; $i < $total_size; $i++) {
			if($j >= $url_limit || $i >= $total_size - 1) {
				$sitemaps[] = $sitemap . $xml_footer;
				$sitemap = $xml_header;
				$j = 0;
			}
			else {
				$sitemap .= $this->array2xml($arr['urlset']['url'][$i], new SimpleXMLElement('<url></url>'));
				$j++;
			}
		}

		$k = 1;
		$sitemap_filename;
		$sitemap_index = '<?xml version="1.0" encoding="UTF-8"?><sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
		foreach($sitemaps as $sitemap) {
			$sitemap_filename = explode('.', $filename)[0] . '-' . $k . '.' . explode('.', $filename)[1];
			file_put_contents($sitemap_filename, $sitemap);
			$sitemap_index .= $this->array2xml(
				array(
					'loc' => $url_base . $sitemap_filename,
					'lastmod' => date('Y-m-d\TH:i:s', filemtime($sitemap_filename))
				),
				new SimpleXMLElement('<sitemap></sitemap>')
			);
			$k++;
		}
		file_put_contents($index_filename, $sitemap_index . '</sitemapindex>');
	}



	private function array2xml($array, $xml_element, $xpath = null, $list_tags = array()) {
		if($xpath != null) {
			$xpath_result = $xml_element->xpath($xpath);
			if(is_array($xpath_result) && sizeof($xpath_result) > 0) {
				$this->array2xml_helper($array, $xpath_result[0], $list_tags);
			}
		}
		else {
			$this->array2xml_helper($array, $xml_element, $list_tags);
		}
		$qbxml_string = $xml_element->asXML();
		return substr($qbxml_string, strpos($qbxml_string, '?>') + 2);
	}
	private function array2xml_helper($data, &$xml_element, $list_tags = array()) {
		foreach($data as $key => $value) {
			if(is_array($value)) {
				if(in_array($key, $list_tags, true)) {
					foreach($value as $element) {
						if(is_array($element)) {
							$subnode = $xml_element->addChild($key);
							$this->array2xml_helper($element, $subnode, $list_tags);
						}
						else {
							$xml_element->addChild("$key", htmlspecialchars("$element"));
						}
					}
				}
				else if(!is_numeric($key)) {
					$subnode = $xml_element->addChild("$key");
					$this->array2xml_helper($value, $subnode, $list_tags);
				}
				else{
					$subnode = $xml_element->addChild("item$key");
					$this->array2xml_helper($value, $subnode, $list_tags);
				}
			}
			else {
				$xml_element->addChild("$key", htmlspecialchars("$value"));
				// $xml_element->addChild("$key", "$value");
			}
		}
	}

	// modified version of http://php.net/xml_parse#87920
	private function xml2array($xml, $get_attributes = 1, $priority = 'tag', $exclude_from_tag = '')
	{
		$contents = "";
		if (!function_exists('xml_parser_create_ns')) {
			return array ();
		}

		$parser = xml_parser_create_ns();
		xml_parser_set_option($parser, XML_OPTION_TARGET_ENCODING, "UTF-8");
		xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
		xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
		xml_parse_into_struct($parser, trim($xml), $xml_values);
		xml_parser_free($parser);
		if (!$xml_values)
			return; //Hmm...
		$xml_array = array ();
		$parents = array ();
		$opened_tags = array ();
		$arr = array ();
		$current = & $xml_array;
		$repeated_tag_index = array ();
		foreach ($xml_values as $data)
		{
			unset ($attributes, $value);
			extract($data);

			// my addition - ability to exclude annoyingly-long namespaces from tags (xmlns, etc)
			if ($exclude_from_tag != '') {
				$tag = str_replace($exclude_from_tag, '', $tag);
			}

			$result = array ();
			$attributes_data = array ();
			if (isset ($value))
			{
				if ($priority == 'tag')
					$result = $value;
				else
					$result['value'] = $value;
			}
			if (isset ($attributes) and $get_attributes)
			{
				foreach ($attributes as $attr => $val)
				{
					if ($priority == 'tag')
						$attributes_data[$attr] = $val;
					else
						$result['attr'][$attr] = $val; //Set all the attributes in a array called 'attr'
				}
			}
			if ($type == "open")
			{ 
				$parent[$level -1] = & $current;
				if (!is_array($current) or (!in_array($tag, array_keys($current))))
				{
					$current[$tag] = $result;
					if ($attributes_data)
						$current[$tag . '_attr'] = $attributes_data;
					$repeated_tag_index[$tag . '_' . $level] = 1;
					$current = & $current[$tag];
				}
				else
				{
					if (isset ($current[$tag][0]))
					{
						$current[$tag][$repeated_tag_index[$tag . '_' . $level]] = $result;
						$repeated_tag_index[$tag . '_' . $level]++;
					}
					else
					{ 
						$current[$tag] = array (
							$current[$tag],
							$result
						); 
						$repeated_tag_index[$tag . '_' . $level] = 2;
						if (isset ($current[$tag . '_attr']))
						{
							$current[$tag]['0_attr'] = $current[$tag . '_attr'];
							unset ($current[$tag . '_attr']);
						}
					}
					$last_item_index = $repeated_tag_index[$tag . '_' . $level] - 1;
					$current = & $current[$tag][$last_item_index];
				}
			}
			elseif ($type == "complete")
			{
				if (!isset ($current[$tag]))
				{
					$current[$tag] = $result;
					$repeated_tag_index[$tag . '_' . $level] = 1;
					if ($priority == 'tag' and $attributes_data)
						$current[$tag . '_attr'] = $attributes_data;
				}
				else
				{
					if (isset ($current[$tag][0]) and is_array($current[$tag]))
					{
						$current[$tag][$repeated_tag_index[$tag . '_' . $level]] = $result;
						if ($priority == 'tag' and $get_attributes and $attributes_data)
						{
							$current[$tag][$repeated_tag_index[$tag . '_' . $level] . '_attr'] = $attributes_data;
						}
						$repeated_tag_index[$tag . '_' . $level]++;
					}
					else
					{
						$current[$tag] = array (
							$current[$tag],
							$result
						); 
						$repeated_tag_index[$tag . '_' . $level] = 1;
						if ($priority == 'tag' and $get_attributes)
						{
							if (isset ($current[$tag . '_attr']))
							{ 
								$current[$tag]['0_attr'] = $current[$tag . '_attr'];
								unset ($current[$tag . '_attr']);
							}
							if ($attributes_data)
							{
								$current[$tag][$repeated_tag_index[$tag . '_' . $level] . '_attr'] = $attributes_data;
							}
						}
						$repeated_tag_index[$tag . '_' . $level]++; //0 and 1 index is already taken
					}
				}
			}
			elseif ($type == 'close')
			{
				$current = & $parent[$level -1];
			}
		}
		return ($xml_array);
	}
}
