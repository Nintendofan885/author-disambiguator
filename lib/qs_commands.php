<?PHP

// Quickstatements V1 commands for creating new author item:
function new_author_qs_commands ( $name, $orcid_author ) {
	$commands = array() ;
	$commands[] = "CREATE" ;
	$commands[] = "LAST\tLen\t\"$name\""  ;
	$commands[] = "LAST\tP31\tQ5"  ;
	if ( $orcid_author != '' ) $commands[] = "LAST\tP496\t\"$orcid_author\"" ;
	return $commands ;
}


// Quickstatements V1 commands for replacing author name strings with author items:
function replace_authors_qs_commands ( $wil, $papers, $names, $author_q ) {
	$commands = array() ;
	foreach ( $papers AS $paperq ) {
		$i = $wil->getItem ( $paperq ) ;
		if ( !isset($i) ) continue ;
		$authors = $i->getClaims ( 'P2093' ) ;
		foreach ( $authors AS $a ) {
			if ( !isset($a->mainsnak) or !isset($a->mainsnak->datavalue) ) continue ;
			$author_name = $a->mainsnak->datavalue->value ;
			if ( !in_array ( $author_name , $names ) ) continue ;
			$num = '' ;
			if ( isset($a->qualifiers) and isset($a->qualifiers->P1545) ) {
				$tmp = $a->qualifiers->P1545 ;
				$num = $tmp[0]->datavalue->value ;
			}
			$add = "$paperq\tP50\t$author_q" ;
			if ( $num != "" ) $add .= "\tP1545\t\"$num\"" ;
			
			$add .= "\tP1932\t\"$author_name\"" ;
			
#			$commands[] = "-STATEMENT\t" . $a->id ; # Deactivated as per https://www.wikidata.org/wiki/Wikidata_talk:WikiProject_Source_MetaData#Author_names
			$commands[] = $add ;
		}
	}
	return $commands ;
}

?>
