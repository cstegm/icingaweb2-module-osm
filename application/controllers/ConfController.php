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

	echo '{"x":"'.$viewx.'","y":"'.$viewy.'","zoom":"'.$zoom.'"}';
	exit;
    }
}
