<?PHP

// Various display handlers
function wikidata_link ( $q, $text, $color ) {
	$style = '';
	if ( $color != '' ) {
		$style = "style='color:$color'" ;
	}
	return "<a href='//www.wikidata.org/wiki/$q' target='_blank' $style'>$text</a>" ;
}

function getORCIDurl ( $s ) {
	return "https://orcid.org/orcid-search/quick-search?searchQuery=" . urlencode($s) ;
}

function print_footer () {
	print "<hr/><a href='https://github.com/arthurpsmith/author-disambiguator/issues' target='_blank'>Feedback</a><br/><a href='https://github.com/arthurpsmith/author-disambiguator/'>Source and documentation (at github)</a><br/>" ;
	print get_common_footer() ;
}

function compress_display_list($list, $highlights, $total_limit, $limit_first, $limit_nbr) {
	if (count($list) <= $total_limit) return $list ;
	$compressed_list = array();

        $int_highlights = array();
	foreach ($highlights as $hl_index) {
                $hl_value = $hl_index ;
		if (! is_numeric($hl_index) ) {
		    $hl_value = preg_replace('/-.*$/', '', $hl_index);
		}
		$int_highlights[] = $hl_value ;
        }
	foreach ($list AS $index => $item) {
		if ($index == 'unordered') {
			$compressed_list[] = $item;
			continue;
		}
		if ($index <= $limit_first) {
			$compressed_list[] = $item;
			continue;
		}
		$in_highlight = 0;
		foreach ($int_highlights as $hl_index) {
			if ( $hl_index == 'unordered' ) {
				continue ;
			}
			if (($index >= $hl_index - $limit_nbr) && ($index <= $hl_index + $limit_nbr)) {
				$compressed_list[] = $item ;
				$in_highlight = 1;
				break(1) ;
			}
		}
		if ($in_highlight == 0) {
			if ($compressed_list[count($compressed_list) - 1] != '...') $compressed_list[] = '...' ;
		}
	}
	return $compressed_list ;
}

function generate_batch_id () {
	return substr(uniqid(), -6); # Last 6 letters of uniqid
}

// Generate the string the EditGroups app expects for this application
function edit_groups_string ( $batch_id = '' ) {
    if ($batch_id == '') {
	$batch_id = generate_batch_id();
    }
    return "([[:toollabs:editgroups/b/AD/$batch_id|details]])";
}

function disambig_header ($use_oauth_menu) {
	$title = 'Author Disambiguator';
	if ( !headers_sent() ) {
		header('Content-type: text/html; charset=UTF-8');
		header('Cache-Control: no-cache, must-revalidate');
	}
	$dir = __DIR__ . '/../resources/html' ;
	$f1 = file_get_contents ( "$dir/index_bs4.html" ) ;
	$f2 = "";
	if ( $use_oauth_menu ) {
		$f2 = file_get_contents( "$dir/menubar_oauth.html" );
	} else {
		$f2 = file_get_contents ( "$dir/menubar_bs4.html" ) ;
	}
	$f3 = '<div id="main_content" class="container"><div class="row"><div class="col-sm-12" style="margin-bottom:20px;margin-top:10px;">' ;
	$s = $f1 . $f2 . $f3 ;
	$s = str_replace ( '</head>' , "<title>$title</title></head>" , $s ) ;

	$s = str_replace ( '$$TITLE$$' , $title , $s ) ;
	return $s ;
}

?>
