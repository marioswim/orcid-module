<?php
/*
* Seleciona los diferentes tipos de publicaciones que hay en la base de datos.
* return un array con cada tipo de publicacion, donde la clave esta en el idioma original en el que se inserta, el item contiene la funcion t() para indicarle que es traducible.
*/
function select_types()
{
	$query="SELECT DISTINCT type from publicaciones_orcid";

	$res=db_query($query);
	$aux=array();

	if($res)
	{

		foreach ($res as $row) 
		{
			$aux[''.$row->type.'']=t($row->type);

		}
		return $aux;
	}
	else
	{
		return $aux;
	}
}