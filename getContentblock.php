<?php
/*
* Gebruik [[getContentblock? &ignore=`` &parent=`` &tvname=`` &context=`` &toPlaceholder=`` &sortby=`` &sortdir=``]]
*/

/*
Wijzigingen:

- 13/5 bug in &parent functie opgelost, object parent wordt nu eerst opgehaald en vanaf daar wordt de getMany uitgevoerd

*/

//initieel definieren
//variabele instellingen
$parent = (!empty($parent) ? $parent : $modx->resource->get('id'));
$ignore = (!empty($ignore) ? $ignore : '');
$tvname = (!empty($tvname) ? $tvname : 'templatekeuze');
$context = (!empty($context) ? $context : $modx->context->key);
$toPlaceholder = (!empty($toPlaceholder) ? $toPlaceholder : '');
$sortby = (!empty($sortby) ? $sortby : 'menuindex');
$sortdir = (!empty($sortdir) ? $sortdir : 'ASC');

//vaste waardes
$output = '';
$allowed_templates = array(2,3,7,8,9,10,11,18,21,22,23,24,25,28,29,30,31,32,35,36,37,39,40);
//$currentResource = $modx->getObject('modResource', $parent);
$parentObject = $modx->getObject('modResource', $parent);

//haal onderliggende documenten op die gepubliceerd zijn en niet verwijderd
$criteria = $modx->newQuery('modResource');
$criteria->where(array(
   'published' => 1,
   'deleted' => 0
));
$criteria->sortby($sortby,$sortdir);
$children = $parentObject->getMany('Children',$criteria);

foreach($children as $child){
	//check template voor contentblock variabele
	$template = $child->get('template');
	if(!in_array($template,$allowed_templates)){
		continue;
	}

	//ophalen waarde voor introtext
	$intro_tpl = $child->getTVValue($tvname);

	//switch for bepalen template
	//aan deze switch moet je een case toevoegen om een extra template mogelijk te maken
	//deze moet dan ook zijn toegevoegd aan desbetreffende TV
	switch ($intro_tpl) {
	  case 1:
		  $tpl = "tpl_1kolom";
		  $tv_array = array(
		  	'pagetitle' => $child->get('pagetitle'),
			'longtitle' => $child->get('longtitle'),
			'introtekst' => $child->getTVValue('introtekst'),
			'kop_icoon' => $child->getTVValue('kop_icoon'),
			'lijn' => $child->getTVValue('lijn'),
			'achtergrond' => $child->getTVValue('achtergrond')
		  );
		  break;
	  case 2:
		  $tpl = "tpl_2koloms";
		  $tv_array = array(
		  	'pagetitle' => $child->get('pagetitle'),
			'longtitle' => $child->get('longtitle'),
			'introtekst' => $child->getTVValue('introtekst'),
			'kop_icoon' => $child->getTVValue('kop_icoon'),
			'lijn' => $child->getTVValue('lijn'),
			'achtergrond' => $child->getTVValue('achtergrond')
		  );
		  break;
	  default:
		  $tpl = "tpl_1kolom";
		  $tv_array = array(
		  	'pagetitle' => $child->get('pagetitle'),
			'longtitle' => $child->get('longtitle'),
			'introtekst' => $child->getTVValue('introtekst')
		  );
		  break;
	}

	//ophalen en verwerken template
	$child_output = $modx->getChunk($tpl,$tv_array);

	//toevoegen aan output variabele
	$output .= $child_output.'
	';
}

//als output niet leeg dan output naar placeholder of direct
if(!empty($toPlaceholder)){
	$modx->toPlaceholder($toPlaceholder,$output);
}else{
	return $output;
}