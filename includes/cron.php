<?php

require_once("DB.php");

/*
* funcion principal que inicia la tarea al ejecutarse el cron.
*/
function getOrcidPublications()
{
	
	deleteTable();
	$users=getUsers();

	foreach ($users as $userOrcid)
	{

		$XML=getWorksXML($userOrcid);

		
		$works=parseXML($XML);
		
		insterIntoDatabase($works);
		
	}


}
/*
* Pide los usuarios a la base de datos.
* return un array con los orcid de los usuarios.
*/
function getUsers()
{
	$users=array();
	$users=getOrcidDatabase();
	return $users;
}

/*
* Obtiene para cada usuario el XML que proporciona la API de orcid con sus publicaciones.
* $user el identificador de orcid de un usuario.
* return un objeto en XML.
*/

function getWorksXML($user)
{
	$url="http://pub.orcid.org/v1.2/".$user."/orcid-works";
	//echo $url."</br>";
	$xml= simplexml_load_file($url);	
	
	
	$works=$xml->{"orcid-profile"}->{'orcid-activities'}->{"orcid-works"}->{"orcid-work"};
	return $works;
}

/*
* Parsea el XML de publicaciones de un usuario en un array asociativo.
* $XML el XML de publicaciones de un usuario.
* return un array con las publicaciones del usuario.
*/
function parseXML($XML)
{
	$works=array();

	$limit_year=variable_get("año_limite");
	foreach ($XML as $work) {
		$aux=array();	
		$aux["year"]=getOrcidYear($work);
		if($aux["year"]>=$limit_year && $aux["year"]!=-1)
		{


			$title=getOrcidTitle($work);

			$aux["put-code"]=getOrcidPutCod($work);
			
			$aux["title"]=str_replace("'", "''", $title);
			$aux["type"]=getOrcidType($work);

			
			$aux["journal-title"]=getOrcidJournalTitle($work);
			


			$aux["doi"]="";
			$aux["issn"]="";
			$aux["eid"]="";



			$identifiers=getOrcidIdentifiers($work);

			foreach ($identifiers as $id )
			{
				$type=$id->{"work-external-identifier-type"};
				$value=$id->{"work-external-identifier-id"};
				$value=str_replace("issn ", "",strtolower($value));
				$aux[''.$type.'']=$value;
			}

			

			echo "<br><br>";
			
			$aux["authors"]=getOrcidAuthors($work);
			


			$aux["pages"]=getOrcidPages($work);

			array_push($works, $aux);
		}
	}

	return $works;
}


/*
* obtiene el put-code de la publicacion.
* $work, el sub-arbol XML de una publicacion.
* return un string con el put-code
*/
function getOrcidPutCod($work)
{
	return $work["put-code"];
}

/*
* Obtiene el año de la publicion, si no esta en el XML de la publicacion, lo busca en el bibtext.
* $work, el sub-arbol XML de una publicacion.
* return el año de la publicacion si existe, en caso contrario -1
*/
function getOrcidYear($work)
{
	if($work->{"publication-date"}->year)
		return $work->{"publication-date"}->year;
	else
	{	
		if($work->{"work-citation"}->{"work-citation-type"}=="bibtex")
		{	
			$bibtex=$work->{"work-citation"}->citation;
			$yearPos=strpos($bibtex, "year");


			if(!$yearPos)
				$yearPos=strpos($bibtex, "Year");


			if($yearPos)
			{	
				$leftBracket=strpos($bibtex,"{",$yearPos)+1;
				$rightBracket=strpos($bibtex, "}",$yearPos);
				$length=$rightBracket-$leftBracket;

				$year=substr($bibtex,$leftBracket,$length);
			}
			return $year;
		}
		else
		{
			return -1;
		}		
	}
}
/*
* Obtiene el tipo de publicacion de la publicacion.
* $work, el sub-arbol XML de una publicacion.
* return un string con el tipo de publicacion.
*/
function getOrcidType($work)
{
	return $work->{"work-type"};
}
/*
* Obtiene el titulo de publicacion del XML.
* $work, el sub-arbol XML de una publicacion.
* return un string con el nombre de la publicacion.
*/
function getOrcidTitle($work)
{
	return $work->{"work-title"}->title;
}
/*
* Obtiene el nombre de la revista o el subtitulo de la publicacion
* $work, el sub-arbol XML de una publicacion.
* return un String.
*/
function getOrcidJournalTitle($work)
{
	if($work->{"journal-title"})
		return str_replace("'", "''", $work->{"journal-title"});
	else
		if($work->{"work-title"}->subtitle)
			return str_replace("'", "''", $work->{"work-title"}->subtitle);
}
/*
* Obtiene los identificadores de orcid
* $work, el sub-arbol XML de una publicacion.
* return un fragmento XML.
*/
function getOrcidIdentifiers($work)
{
	return $work->{"work-external-identifiers"}->{"work-external-identifier"};
}

/*
* Obtiene los autores de la publicacion, lo hace del bibtex debido a la heterogeneidad de las publicaciones.
* El campo mas homogeneo en este aspecto es el campo author en el bibtex.
* $work, el sub-arbol XML de una publicacion.
* return un array con los autores seperados por | 
*/
function getOrcidAuthors($work)
{
	
	if($work->{"work-citation"}->{"work-citation-type"}=="bibtex")
	{
			$bibtex=$work->{"work-citation"}->citation;
			$autPos=strpos($bibtex, "author"); //search the position of author tag in the bibtex string


			if(!$autPos)
				$autPos=strpos($bibtex, "Author");


			if($autPos)
			{	
				$leftBracket=strpos($bibtex,"{",$autPos)+1;
				$rightBracket=strpos($bibtex, "},",$autPos);

				$length=$rightBracket-$leftBracket;

				$authors=substr($bibtex,$leftBracket,$length);
				$authors=str_replace("'", "", $authors);

				$authors=str_replace("and", "|", $authors);

			}
			else
			{
				$authors="";
			}
	}
	else
	{
		$authors="";
	}

	return $authors;

}
/*
* Obtiene el numero de paginas de la publicacion, lo hace del bibtex debido a que no existe en el XML ningun campo destinado
* a las paginas
* $work, el sub-arbol XML de una publicacion.
* return un array con el numero de paginas
*/
function getOrcidPages($work)
{
	if($work->{"work-citation"}->{"work-citation-type"}=="bibtex")
	{
			$bibtex=$work->{"work-citation"}->citation;
			$pagPos=strpos($bibtex, "pages");


			if(!$pagPos)
				$pagPos=strpos($bibtex, "Pages");


			if($pagPos)
			{	
				$leftBracket=strpos($bibtex,"{",$pagPos)+1;
				$rightBracket=strpos($bibtex, "}",$pagPos);
				$length=$rightBracket-$leftBracket;

				$pages=substr($bibtex,$leftBracket,$length);
			}
			else
			{
				$pages="";
			}
	}
	else
	{
		$pages="";
	}

	return $pages;
}
