<?php
require_once(drupal_get_path('module', 'Orcid')."/includes/admin.php");


/*
* Funcion que carga el form en el backend para seleccionar los diferente tipos de publicaciones a mostrar.
*/

function orcid_configuracion() {  

	$values=select_types();
	$form['tipo_publicaciones'] = array(
		'#title' => 'Tipo de publicaciones para mostrar',  
		'#options' => $values,
		'#type' => 'checkboxes',
		"#default_value" => variable_get("tipo_publicaciones")

	);
	$form["año_limite"]=array(
		'#title' => 'Filtrar publicaciones a partir del año:',
		 "#default_value" => variable_get("año_limite"),
		'#type' => "textfield",
		);
	$form["vacios"]=array(
		"#title" => "Permitir publicaciones incompletas",
		
		"#type" => "checkbox",
		"#default_value" => variable_get("permitir"),
		);
	$form['submit'] = array(
	  '#type' => 'submit',
	  '#value' => 'Guardar configuración',

	);

	$form['#submit'][] = 'saludar_configuracion_submit';  
return $form;
}

function saludar_configuracion_submit($form, $form_state) {

  variable_set('tipo_publicaciones', $form_state['values']['tipo_publicaciones']);
  variable_set('año_limite', $form_state['values']['año_limite']);
  variable_set("permitir",$form_state["values"]["vacios"]);
  drupal_set_message("Se han guardado las opciones de configuración.");
}