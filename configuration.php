<?php

$this->menuSection('OSM')
     ->setIcon('lightbulb')
     ->setUrl('osm');

//JavaScript includes

$this->provideJsFile('third-party/leaflet.js');
$this->provideJsFile('module.js');
$this->provideCssFile('third-party/leaflet.css');

//Configuration
//$this->provideConfigTab('config', array(
//    'title' => 'Configure this module',
//    'label' => 'Config',
//    'url' => 'config'
//));
?>
