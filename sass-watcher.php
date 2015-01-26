<?php
/*
 * Plugin Name: Sass Watcher
 * Plugin URI: https://github.com/reyhoun/sass-watcher
 * Description: It's PHP SCSS Compiler for WordPress and it's compatible with Advanced Custom Field 5 to send parameters(variables) to your SCSS codes.
 * Version: 1.6.0
 * Author: Reyhoun
 * Author URI: http://reyhoun.com/
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * GitHub Plugin URI: https://github.com/reyhoun/sass-watcher
 * Contributors: Mohammad Mojrian(mohammadmojrian@gmail.com), Parhum Khoshbakht(parhum.kh@gmail.com)
*/

// Save + Compile
class sass_watcher
{
	public function writedata($data)
	{
		$file = get_template_directory() . '/sass-watcher-config.txt';
		$td = '';
		foreach ($data as $key => $value) {
			$td .= $key . '=' . $value . '\n';
		}
		file_put_contents($file, $td);
	}

	public function getdata()
	{
		$file = get_template_directory() . '/sass-watcher-config.txt';
		$data = file_get_contents($file);
		$darr = explode('\n', $data);
		$gd = array();
		foreach ($darr as $value) {
			if (!is_null($value)) {
				$expdata = explode('=', $value);
				if(is_array($expdata) && count($expdata) == 2){
					$gd[$expdata[0]] = $expdata[1];
				}
			}
		}
		return $gd;
	}


	public function compile($scss_folder, $scss_filename,$stylename,$format_style = "scss_formatter")
	{
		require_once plugin_dir_path( __FILE__ ) . 'lib/scssphp/scss.inc.php';
		$scss_compiler = new scssc();
		$scss_compiler->setImportPaths($scss_folder);
		$scss_compiler->setFormatter($format_style);
		try {
			$file = $scss_filename;
			$content = file_get_contents($file);
			$string_css = $scss_compiler->compile($content);
			file_put_contents($stylename , $string_css);

		} catch (Exception $e) {
			echo $e->getMessage();
		}


	}
}

$sw = new sass_watcher;

function adminPageContant()
{
	$sw = new sass_watcher;
	$conf = $sw->getdata();

	if (isset($_POST['sasswatcher_hidden']) && $_POST['sasswatcher_hidden'] == 'true') {
		if (isset($_POST['sasswatcher_compile']) && $_POST['sasswatcher_compile'] == 'true') {
			$conf = $sw->getdata();
			$scss_forlder = get_template_directory() . '/' . $conf['sassdir'];
			$scss_filename = $scss_forlder . '/' . $conf['sassfile'];
			$scss_style = get_template_directory() . '/' . $conf['cssdir'];
			$sw->compile($scss_forlder,$scss_filename,$scss_style);
		} else {
		    $data = array();
		    $data['devmode'] = ($_POST['sasswatcher_devmode'] == 'true') ? 1 : 0;
		    $data['sassdir'] = $_POST['sasswatcher_sassdir'];
		    $data['sassfile'] = $_POST['sasswatcher_sassfile'];
		    $data['cssdir'] = $_POST['sasswatcher_cssdir'];
		    $data['varfilename'] = $_POST['sasswatcher_varfilename'];
		    if($data['sassdir'] ==  '' || $data['sassfile'] == '' || $data['cssdir'] == '' || $data['varfilename'] == ''){
	    		$tf = true;
		    }else{
		    	$tf = false;
		    	$sw->writedata($data);
		    }
		}

	}


	echo '<div class="wrap">';
	echo "<h1>" . __( 'Sass Watcher Config', 'sass-watcher' ) . "</h1>";


    if(isset($tf) && $tf == true)
        echo '<h3 style="color:red;">' . __( 'Invalide Require Filde', 'sass-watcher' ) . "</h3>";

	echo '<form name="sasswatcher_config" method="post" action="'.str_replace( '%7E', '~', $_SERVER['REQUEST_URI']).'">';
	echo '<input type="hidden" name="sasswatcher_hidden" value="true">';
	echo '<h2> ' . __( 'Initial Settings', 'sass-watcher' ) . '</h2>';

	echo '<p><strong>' . __( 'SCSS Directory Path:', 'sass-watcher' ) . '</strong> <span style="color:red;">*</span></p>';
	echo '<code>' . get_template_directory() . '/</code><input type="text" name="sasswatcher_sassdir" value="' . $conf['sassdir'] . '" size="20">';

	echo '<p><strong>' . __( 'Final SCSS file to compile:', 'sass-watcher' ) . '</strong> <span style="color:red;">*</span></p>';
	echo '<code>' . get_template_directory() . '/[SCSS Directory Path]/</code><input type="text" name="sasswatcher_sassfile" value="' . $conf['sassfile'] . '" size="20">';

	echo '<p><strong>' . __( 'File for save variables sent from ACF:', 'sass-watcher' ) . '</strong> <span style="color:red;">*</span></p>';
	echo '<code>' . get_template_directory() . '/[SCSS Directory Path]/</code><input type="text" name="sasswatcher_varfilename" value="' . $conf['varfilename'] . '" size="20">';

	echo '<p><strong>' . __( 'CSS output file:', 'sass-watcher' ) . '</strong> <span style="color:red;">*</span></p>';
	echo '<code>' . get_template_directory() . '/</code><input type="text" name="sasswatcher_cssdir" value="' . $conf['cssdir'] . '" size="20">';


	echo '<br /><br /><h3> ' . __( 'Developer Option', 'sass-watcher' ) . '</h3>';
	echo '<strong> ' . __( 'Enable it only when you\'re in development process.', 'sass-watcher' ) . '</strong>';
	echo '<p><input type="checkbox" name="sasswatcher_devmode" value="true" size="20" ';
	if($conf['devmode'] == '1'){ echo 'checked'; }
	echo ' >';
	echo __( 'Enable auto compile in each page refresh.', 'sass-watcher' );
	echo '<p class="submit"><input type="submit" name="Submit" value="Save" class="button button-primary button-large" /></p></form>';
	echo '
	    <form name="sasswatcher_compile" method="post" action="' . str_replace( '%7E', '~', $_SERVER['REQUEST_URI']) . '">
	        <input type="hidden" name="sasswatcher_hidden" value="true">
	        <input type="hidden" name="sasswatcher_compile" value="true">
	        <h3>' . __( 'Manual Compile', 'sass-watcher' ) . '</h3>
	        <p>
		        <input type="submit" name="Submit" value="' . __( 'Compile Now!', 'sass-watcher' ) .'" class="button" />
	        </p>
	    </form>
	';



	echo '</div>';

}

function setAdminMenu()
{
	add_theme_page('Sass Watcher', 'Sass Watcher', 'activate_plugins', 'SassWatcher', 'adminPageContant');
}

add_action('admin_menu', 'setAdminMenu');

function checkdevmode()
{
	$sw = new sass_watcher;
	$conf = $sw->getdata();
	if ($conf['devmode'] == '1') {
		$scss_forlder = get_template_directory() . '/' . $conf['sassdir'];
		$scss_filename = $scss_forlder . '/' . $conf['sassfile'];
		$scss_style = get_template_directory() . '/' . $conf['cssdir'];
		$sw->compile($scss_forlder,$scss_filename,$scss_style);
	}
}

// When page load
add_action('wp_head','checkdevmode');

// Validate Background Field for Sass
function background_field_validate($var, $value, $name, $mode = background) {

    if ( empty($value['background-color']) ) {
        $value['background-color'] = 'transparent';
    }
    if ( empty($value['background-image']) ) {
        $value['background-image'] = 'none';
    }
    
    $default_bg_value = array(
        'background-color'      => 'transparent',
        'background-repeat'     => 'repeat',
        'background-clip'       => 'border-box',
        'background-origin'     => 'padding-box',
        'background-size'       => 'auto',
        'background-attachment' => 'scroll',
        'background-position'   => '0 0',
        'background-image'      => 'none',
        'media' => array(
            'id'        => null, 
            'height'    => 0, 
            'width'     => 0, 
            'thumbnail' => null,
        ),
        'background-text'      	=> 'null',
    );
    $value = array_merge($default_bg_value, $value);

    $all_value = '';
    foreach ($value as $key => $item) {
        if($key != 'media') {
            if(empty($item)) {
                $item = 'null';
            }
            if((substr($key, 11) == 'image' || substr($key, 11) == 'position') && $item != 'null') {
                $all_value = $all_value . ' "' . $item . '"';
            } else {
                $all_value = $all_value . ' ' . $item;
            }
        } else if ($value['background-image'] != 'none') {
        	$all_value = $all_value . ' ' . $item['width'] . ' ' . $item['height'];
        } else {
        	$all_value = $all_value . ' 0 0';
        }
    }
    if( $mode == 'background' ) {
        $var .= '$' . $name . ':' . $all_value . ';' . chr(13);
    } else if( $mode == 'repeater' ) {
        $var .= '(' . $all_value . '), ';
    }
    
    return $var;
}

function typography_field_validate($var, $value, $name) {
    
    $default_value = array(
    	'font-family'	=> 'null',
    	'font-weight'	=> '400',
    	'backupfont'	=> 'null',
    	'text_align'	=> 'left',
    	'direction'		=> 'ltr',
    	'font_size'		=> 'null',
    	'line_height' 	=> 'null',
    	'font_style' 	=> 'normal',
    	'text-color' 	=> 'null',
    	'letter_spacing' => '0'
    );

    $value = array_merge($default_value, $value);

	$all_value = '';
	foreach ($value as $key => $item) {
		if (!$item) {
			$value[$key] 	= $default_value[$key];
			$item 			= $value[$key];
		}
		if (( ($key == 'font-family') or ($key == 'backupfont') ) and ( $item != 'null' )) {
			$all_value = $all_value . ' "' . $item . '"';
		} else {
			$all_value = $all_value . ' ' . $item;
		}
	}

	$var .= '$' . $name . ':' . $all_value . ';' . chr(13);

	return $var;
}

// Number '20' it's mean after save and '1' for get data before save
add_action('acf/save_post', 'watcher_acf_save_post', 20);
function watcher_acf_save_post($post_id)
{
    if ( 'options' == $post_id ) {
        $fields = get_field_objects('options');
        
		// load from post
        if( $fields )
		{
			$var = $var1 = '';
			foreach ($fields as $key => $value) {
				$finfo = get_field_object($key, $post_id, array("load_value" => true));

				if (is_array($finfo)) {
					if (substr($finfo['name'], 0,2) == 's_') {
						
						$type = $finfo['type'];

						switch ($type):
							case 'repeater':
								// $var .= chr(13);
								if (empty($finfo['value'])) {
									$var .= '$' . $finfo['name'] . ': null;' . chr(13) ;
								} else {
									$i = 1;
									$pvar = '$' . $finfo['name'] . ': (' . count($finfo['value']) . '), (';
									$prx = '$' . $finfo['name'] . '_';
									foreach ($finfo['value'] as $row) {

                                        // Uncomment below line to create an id for each skin:
										// $skin_name = $finfo['name'] . '_' . $i;
                                        
										$pvar .= $prx . $i . ', ';
										$rvar = $prx . $i++ . ': ';
                                        
                                        // Uncomment below line to create an id for each skin:
										// $rvar .= $skin_name . ', ';

										foreach ( $row as $key => $val ) {
											if( empty( $val ) ) {
												$val = 'null';
											}
                                            
                                            // If name of field have 'ns_' prefix this field don't display in sass settings.
                                            if( substr($key, 0, 3) != 'ns_' ) {
                                                if($key == 'skin_name') {
                                                    $rvar .= sanitize_title($val) . ', ';
                                                } else if( substr($key, 0, 3) == 'bg_' ) {
                                                    // $rvar .= $val . ', ';
													// echo '<pre>';
													// print_r(background_field_validate($rvar, $val, $key));
													// echo '</pre>';
                                                    $rvar = background_field_validate($rvar, $val, $key, 'repeater');
                                                } else {
                                                    $rvar .= $val . ', ';
                                                }
                                            }
										}
										if(substr($rvar,-2) == ', '){
											$rvar = substr($rvar, 0, -2);
										}
										$rvar .=  ';' . chr(13);
										$var1 .= $rvar;
									}
									if (substr($pvar,-2) == ', ') {
										$pvar = substr($pvar, 0, -2);
										$pvar .=  ');' . chr(13);
									}
									$var1 .= $pvar;
								}
								// $var .= chr(13);
								break;
							case 'text':
								if (is_null($finfo['value']) || $finfo['value'] == '') {
									$var .= '$' . $finfo['name'] . ': null;' . chr(13) ;
								} else {
									$var .= "$" . $finfo['name'] . ": '" . $finfo['value'] . "';" . chr(13);
								}
								break;
							case 'background':
                                $var = background_field_validate($var, $finfo['value'], $finfo['name'], 'background');    
								break;
							case 'true_false':
								if ($finfo['value'] == 1) {
									$var .= '$' . $finfo['name'] . ': true;' . chr(13) ;
								} else {
									$var .= '$' . $finfo['name'] . ': false;' . chr(13) ;
								}
								break;
							case 'typography':
							    $var = typography_field_validate( $var, $finfo['value'], $finfo['name'] );
								break;
							default:
								if (is_null($finfo['value']) || $finfo['value'] == '') {
									$var .= '$' . $finfo['name'] . ': null;' . chr(13) ;
								} else {
									if ( is_array($finfo['value']) ) {
										$var .= '$' . $finfo['name'] . ': ';

										foreach ($finfo['value'] as $key => $value) {
											if ( is_null($value) ) {
												$value = 'null';
											}
										    $var .= '(' . $key . ', ' . $value . '), ';
										}
										$var = rtrim($var, ', ');

										$var .= ';' . chr(13);
									} else {
										$var .= '$' . $finfo['name'] . ': ' . $finfo['value'] . ';' . chr(13);
									}
								}
						endswitch;
					}
				}
			}
		}

		if (isset($var1)) {
			$var .= $var1;
		}

		$sw = new sass_watcher;
		$conf = $sw->getdata();
		$scss_forlder = get_template_directory() . '/' . $conf['sassdir'];
		$scss_filename = $scss_forlder . '/' . $conf['sassfile'];
		$scss_style = get_template_directory() . '/' . $conf['cssdir'];
		$varfilename = $scss_forlder . '/' . $conf['varfilename'];
		file_put_contents($varfilename,$var);
		$sw->compile($scss_forlder,$scss_filename,$scss_style);

	}
}
?>
