<?php
namespace Cetera;
/**
 * Cetera CMS 3
 * 
 * Список файлов   
 *
 * @package CeteraCMS
 * @version $Id$
 * @copyright 2000-2010 Cetera labs (http://www.cetera.ru) 
 * @author Roman Romanov <nicodim@mail.ru> 
 **/
       //TEMPLATES_DIR
include('common_bo.php');
$widget = strtolower( $_REQUEST['widget'] );
$data = array();

parse_dir( CMSROOT.'/widgets/'.$widget );
parse_dir( TEMPLATES_DIR.'/widgets/'.$widget , 'в шаблонах пользователя' );
foreach (Theme::enum() as $theme)
{
	parse_dir( WWWROOT.THEME_DIR.'/'.$theme->name.'/widgets/'.$widget, 'в теме "'.$theme->title.'"' );
}

//print_r($data);

foreach ($data as $key => $value)
{
	if (count($value['redefined']))
	{
		if ($value['main'])
		{
			$data[$key]['display'] .= ' (переопределен '.implode(', ',$value['redefined']).')';
		}
		else 
		{
			$data[$key]['display'] .= ' (доступен только '.implode(', ',$value['redefined']).')';
		}
	}
	unset($data[$key]['redefined']);
}

$data = array_values($data);

function parse_dir($dir, $place = null)
{
	global $data;
	
	if (file_exists($dir) && is_dir($dir)) {

		$iterator = new \DirectoryIterator($dir);
		
		foreach ($iterator as $fileinfo) {
			if (!$fileinfo->isFile()) continue;
			
			$fn = $fileinfo->getFilename();
			
			if (!isset($data[$fn]))
			{
				$data[$fn] = array(
					'name'      => $fn,
					'display'   => $fn,
					'main'      => $place == null,
					'redefined' => array()
				);	
				if ($place)
				{
					$data[$fn]['redefined'][] = $place;
				}
			}
			else 
			{
				$data[$fn]['redefined'][] = $place;
			}

		
		}

	}	
	
}

function cmp($a, $b)
{
    $_a = strtolower($a['name']);
    $_b = strtolower($b['name']);
    if ($_a == $_b) return 0;
    return ($_a < $_b) ? -1 : 1;
}

//usort($data, "cmp");

echo json_encode(array(
    'success' => true,
    'rows'    => $data
));