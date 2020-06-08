<?php

/*
Plugin Name: Kwo Plugin Form
Plugin URI:
Description: Plugin para cracion de formularios personalizados mediante el shortcode [kwo_plugin_form]
Version: 1.0.0
Author: Luber Navarrete
Author URI: https://lubernavarrete.github.io/web/
License: GLP2 License URI: https://www.gnu.org/Licenses/glp-2,0.html
Text Domain:
*/

// Funciones ejecutadas al instalar
register_activation_hook( __FILE__, 'Kwo_plugin_init');

function Kwo_plugin_init(){
	global $wpdb;

	$tabla_encuesta = $wpdb->prefix . 'encuesta';
	$charset_collate = $wpdb->get_charset_collate();

	$query = "CREATE TABLE IF NOT EXISTS $tabla_encuesta(
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		nombre varchar(40) NOT NULL,
		correo varchar(40) NOT NULL,
		nivel_html smallint(4) NOT NULL,
		nivel_js smallint(4) NOT NULL,
		acept smallint(4) NOT NULL,
		created_at datetime NOT NULL,
		unique (id)
	) $charset_collate";

	include_once ABSPATH . 'wp-admin/includes/upgrade.php';
	dbDelta($query);
}

// Shortcode para pintar formulario
add_shortcode( 'kwo_plugin_form', 'KWO_plugin_form' );

function KWO_plugin_form(){

	global $wpdb;
	$tabla_encuesta = $wpdb->prefix . 'encuesta';

	if($_POST){
		$wpdb->insert($tabla_encuesta, 
			array('nombre' => sanitize_text_field($_POST['nombre']),
				'correo' => sanitize_text_field($_POST['correo']),
				'nivel_html' => sanitize_text_field($_POST['nivel_html']),
				'nivel_js' => sanitize_text_field($_POST['nivel_js']),
				'acept' => sanitize_text_field((int)$_POST['acept']),
				'created_at' => date('Y-m-d H:i:s')
			));
	}

	ob_start();
	?>
	<form method="post" class="cuestionario" action="<?php get_permalink(); ?>">
		<?php wp_nonce_field('encuesta','encuesta_nonce'); ?>
		<div class="form-input">
			<label for="nombre">Nombre</label>
			<input type="text" name="nombre" id="nombre" required>
		</div>
		<div class="form-input">
			<label for="correo">Correo</label>
			<input type="email" name="correo" id="correo" required>
		</div>
		<div class="form-input">
			<label for="nombre">Nivel de HTML</label>
			<br><input type="radio" name="nivel_html" id="nivel_html" value="1" required> Nada
			<br><input type="radio" name="nivel_html" id="nivel_html" value="2" required> Aprendiendo
			<br><input type="radio" name="nivel_html" id="nivel_html" value="3" required> Experiencia
			<br><input type="radio" name="nivel_html" id="nivel_html" value="4" required> Experto
		</div>
		<div class="form-input">
			<label for="nombre">Nivel de JavaScript</label>
			<br><input type="radio" name="nivel_js" id="nivel_js" value="1" required> Nada
			<br><input type="radio" name="nivel_js" id="nivel_js" value="2" required> Aprendiendo
			<br><input type="radio" name="nivel_js" id="nivel_js" value="3" required> Experiencia
			<br><input type="radio" name="nivel_js" id="nivel_js" value="4" required> Experto
		</div>
		<div class="form-input">
			<label for="nombre">La informacion facilitada sera usada para fines practicos</label>
			<br><input type="checkbox" name="acept" id="acept" value="1" required> Entiendo y acepto las condiciones
		</div>
		<div class="form-input">
			<input type="submit" value="Enviar">
		</div>
	</form>
	<?php
	return ob_get_clean();
}

//Menu de administracion
add_action('admin_menu', 'kwo_plugin_menu');

function kwo_plugin_menu(){
	add_menu_page( "Formulario Encuestas", "Encuestas", "manage_options", 'kwo_plugin_menu', 'kwo_plugin_admin', 'dashicons-feedback', 75);
}

function kwo_plugin_admin(){
	
	global $wpdb;
	$tabla_encuesta = $wpdb->prefix . 'encuesta';
	$encuestas = $wpdb->get_results("SELECT * FROM $tabla_encuesta");

	echo '<div class="wrap"><h1>Lista de Encuestas</h1>';
	echo '<table class="wp-list-table widefat fixed striped">';
	echo '<thead><tr><th width="30%">Nombre</th><th width="20%">Correo</th>';
	echo '<th>HTML</th><th>JS</th></tr></thead>';
	echo '<tbody id="the-list">';

	foreach($encuestas as $encuesta){
		echo "<tr><td>".esc_textarea($encuesta->nombre)."</td>";
		echo "<td>".esc_textarea($encuesta->correo)."</td>";
		echo "<td>".(int)$encuesta->nivel_html."</td>";
		echo "<td>".(int)$encuesta->nivel_js."</td></tr>";
	}
	echo "</tbody></table></div>";

}