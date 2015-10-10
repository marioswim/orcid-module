<?php

/*
* Pide a la base de datos las publicaciones almacenadas en ella.
* $param array con los tipos de publicaciones seleccionados en el backend, para que se muestren
* return el objeto resultado de la consulta.
*/
function pedirPublicaciones($param)
{

	$publications=array();
	$query="SELECT Distinct(year) from {publicaciones_orcid} ORDER BY year DESC";

	$years=db_query($query);

	$types=array();

	$i=0;

	foreach ($param as $type)
	{

		if(!is_int($type))	
		{
			
			$types[$i]=$type;
			$i++;
		}
			
	}
	foreach ($years as $year) 
	{

		$query2=db_select("publicaciones_orcid","po")
			->fields("po")
			->condition("po.year",$year->year,"=");

		if(!in_array("any", $types))
			$query2->condition("po.type",$types,"IN");

		$results=$query2->execute();
		$publications[$year->year]=$results;

	}

	return $publications;
}
/*
* borra la tabla de las publicaciones.
*/
function deleteTable()
{
	$aux=array();
	
	$query="DELETE FROM publicaciones_orcid WHERE 1";

	$res=db_query($query);
	if(!$res)
		{
			echo "<b>error en delete </b> </br>";
			
			echo "</br></br>";
		}	
}
/*
* Obtiene el identificador de Orcid de cada usuario
* return un array con los identificadores de orcid de cada usuario.
*/
function getOrcidDatabase()
{
	$aux=array();
	//añadir filtro para pedir solo investigadores y comite cientifico
	$query="SELECT field_id_orcid_value FROM field_data_field_id_orcid";


	$res=db_query($query);

	if($res)	
	{
		$i=0;
		foreach($res as $id)
		{
			$aux[$i]=$id->{"field_id_orcid_value"};
			$i=$i+1;	
		}

	}
	else
	{
		echo "Error al pedir el orcid</br>";
	}
	//echo "<b>".count($aux)."</b></br></br>";
	return $aux;
}
/*
* Inserta en la tabla, las publicaciones de orcid
* $works un array con todas las publicaciones de orcid para cada usuario.

*/
function insterIntoDatabase($works)
{
	$aux=array();
	foreach ($works as $work ) 
	{
		if(variable_get("permitir"))
		{
			insertWork($work);
		}
		else
		{
			if($work["put-code"]!=0 and $work["year"] != -1 and $work["title"]!="" and $work["type"]!="" and $work["journal-title"]!="" and ($work["doi"]!="" or $work["eid"]!="" or $work["issn"]!="") and $work["authors"]!="" and $work["pages"]!= "")
			{
				insertWork($work);
			}
		}
	}	
}

function insertWork($work)
{
	$query="REPLACE INTO publicaciones_orcid values(
			".$work["put-code"].",".$work["year"].",'".$work["title"]."','".$work["type"]."','".$work["journal-title"]."','".$work["doi"]."','".$work["eid"]."','".$work["issn"]."','".$work["authors"]."','".$work["pages"]."')";

			$res=db_query($query);

			if(!$res)
			{
				echo "<b>error en Insert</b> </br>";
				var_dump($work);
				echo "</br></br>";
			}
}