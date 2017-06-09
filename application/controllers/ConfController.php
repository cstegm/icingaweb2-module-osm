<?php

namespace Icinga\Module\Osm\Controllers;
 
use Icinga\Module\Monitoring\Controller;
 
class ConfController extends Controller
{
    public function indexAction()
    {
	$viewx = $this->Config()->get('osm', 'latitude');
	$viewy = $this->Config()->get('osm', 'longitude');
	$zoom = $this->Config()->get('osm', 'zoom');
	$radius = $this->Config()->get('osm', 'radius');

	echo '{"x":"'.$viewx.'","y":"'.$viewy.'","zoom":"'.$zoom.'","radius":"'.$radius.'"}';
	exit;
    }
}
