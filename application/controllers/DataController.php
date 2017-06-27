<?php

namespace Icinga\Module\Osm\Controllers;
 
use Icinga\Module\Monitoring\Controller;
 
class DataController extends Controller {
	private function worststatus($worststatus,$status){
		switch ($status){
			case 0:
	                        break;;
	                case 1:
	                        if($worststatus<1 || $worststatus==99){
	                            $worststatus=1;
	                        }
        	                break;;
	                case 2:
	                        if($worststatus<2 || $worststatus==3 || $worststatus==99){
	                            $worststatus=2;
	                        }
	                        break;;
	                case 3:
	                        if($worststatus<=1 || $worststatus==99){
	                            $worststatus=3;
	                        }
	                        if($worststatus==2){
	                             $worststatus=2;
	                        }
	                        break;;
	                case 99:
	                        if($worststatus==0){
	                            $worststatus=99;
				}
	                	break;;
		}
		return $worststatus;
		
	}
	private function htmlstatus($status){
		$state="";
		switch ($status){
			case 0:
				$state="<td style='background-color: #4b7; '>OK";
	                        break;;
	                case 1:
	                        $state="<td style='background-color: #fa4; '>WARNING";
        	                break;;
	                case 2:
	                        $state="<td style='background-color: #f56; '>CRITICAL";
	                        break;;
	                case 3:
	                        $state="<td style='background-color: #a4f; '>UNKNOWN";
	                        break;;
	                case 99:
	                        $state="<td style='background-color: #7af; '>PENDING";
	                	break;;
		}
		return $state;
	}
    public function indexAction()
    {

	$meinarray = array();
        $query = $this->backend
            ->select()
            ->from('customvar', array(
                'host_name',
                'varname',
		'varvalue'))
            ->where('varname', 'geolocation')
	    ->order('varvalue');

	    
	$okhosts="";
	$geoarray=array();
	if (count($query->fetchAll()) > 0 ) {
        	foreach ($query as $row) {
            		$worststatus=0;
			$query2 = $this->backend
            			->select()
	            		->from('servicestatus', array(
	                		'host_name',
	                		'service_display_name',
        				'service_host_name',
                        		'service',
	                		'service_state'))
		    		->where('service_host_name', $row->host_name);
	
			$this->applyRestriction('monitoring/filter/objects', $query2);
			
			foreach ($query2 as $row2) {
				$worststatus=$this->worststatus($worststatus,$row2->service_state);
				$geoarray[$row->varvalue][$row->host_name][$row2->service]["status"]=$row2->service_state;
				$geoarray[$row->varvalue][$row->host_name][$row2->service]["display_name"]=$row2->service_display_name;

			}
			$geoarray[$row->varvalue][$row->host_name]["status"]=$worststatus;
        	}
	}
	foreach($geoarray as $location => $hosts){
		$anz = count($hosts);
		//echo "$location : $anz <br>"; 
		$okhosts.="\n".'{'."\n"
			."\t".'"geometry" : {'."\n"
			."\t\t".'"type" : "Point",'."\n"
			."\t\t".'"coordinates" : ['.$location.']'."\n"
			."\t".'},'."\n"
			."\t".'"type" : "Feature",'."\n"
			."\t".'"properties" : {'."\n"
			."\t\t".'"popupContent" : "';
		if($anz == 1){
			// Host und Services Anzeigen
			foreach ($hosts as $host => $hostvals){
				//echo " - $host : " . $hostvals["status"] . "<br>";
      				$okhosts.='<b>Hostname: <a href=\'monitoring/host/show?host='.$host.'\' >'
					.$host.'</a></b>'
					.'<table><tr><th>Status</th><th>Service</th></tr>';
				foreach ($hostvals as $service => $servicevals ){
					if($service == "status"){
						continue;
					}
					$okhosts.='<tr>'.$this->htmlstatus($servicevals["status"]).'</td><td>'
						.'<a href=\'monitoring/service/show?host='.$host.'&service='.$service.'\' >'
						.$servicevals["display_name"].'</td></tr>';
					//echo " - - $service : " . $servicevals["status"]."<br>";
				}
			}
		}else{
			// nur Hosts anzeigen
			$okhosts.='<table><tr><th>Status</th><th>Host</th></tr>';
			foreach ($hosts as $host => $hostvals){
				//echo " - $host : " . $hostvals["status"] . "<br>";
      				//$okhosts.='<b>Hostname: <a href=\'monitoring/host/show?host='.$row->host_name.'\' >'
				$okhosts.="<tr>".$this->htmlstatus($hostvals["status"])."</td><td><a href='monitorin/host/show?host=$host'>$host</a></td></tr>";
			}
		}
		$okhosts.='</table>Location: '.$row->varvalue.'",'."\n"
			."\t".'"worststatus" : "'.$worststatus.'"'."\n"
			."\t".'},'."\n"
			."\t".'"id" : "'.$row->host_name.'"'."\n"
			."},";
	}
	$okhosts=substr($okhosts,0,-1);
	$okfeature=      '{'."\n"
			.'"type": "FeatureCollection",'."\n"
			.'"features": ['.$okhosts.']'."\n"
			.'}';
	echo $okfeature;
	//echo "<pre>";
	//print_r($geoarray);
	exit;
    }
}
