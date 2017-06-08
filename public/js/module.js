

(function(Icinga) {

    var Osm = function(module) {
        /**
         * YES, we need Icinga
         */
        this.module = module;

        this.idCache = {};

        this.initialize();

        this.timer;

        this.module.icinga.logger.debug('OSM module loaded');

    };
    L.Icon.Default.prototype.options.iconUrl = '';
    var tempData;
    var myLayer; 
    var map;
    var radius = 10;
    var opacity = 1;
    var fillopacity = 0.8;
    var markers = new L.FeatureGroup();
    Osm.prototype = {

        initialize: function()
        {
	    this.module.on('rendered', this.onRenderedContainer);
            this.module.icinga.logger.debug('OSM module initialized');
        },

        registerTimer: function () {
            this.timer = this.module.icinga.timer.register(
                this.updateOsmData,
                this,
		// Default refresh is 5 min
                //300000
		30000
		// End Default refresh
            );
            return this;
        },

        updateOsmData: function () {
            xhr = new XMLHttpRequest();
            xhr.open('GET', 'osm/data', true);
        function onEachFeature(feature, layer) {
        // does this feature have a property named popupContent?
             if (feature.properties && feature.properties.popupContent) {
                    layer.bindPopup(feature.properties.popupContent);
             }
        }
	    function addDataToMap(data, map) {
            markers.clearLayers();
            map.removeLayer(markers);
		    L.geoJson(data,{
                onEachFeature: onEachFeature,
    		    pointToLayer: function (feature, latlng) {
                    var options;
            	    var geojsonMarkerOptionsWarning = {
            		    radius: radius, fillColor: "#fa4",
            		    color: "#000", weight: 1,
            		    opacity: opacity, fillOpacity: fillopacity
            	    };
            	    var geojsonMarkerOptionsOk = {
            		    radius: radius, fillColor: "#4b7",
    	        	    color: "#000", weight: 1,
            		    opacity: opacity, fillOpacity: fillopacity,
            	    };
            	    var geojsonMarkerOptionsCritical = {
            		    radius: radius, fillColor: "#f56",
            		    color: "#000", weight: 1,
            		    opacity: opacity, fillOpacity: fillopacity,
             	    };
            	    var geojsonMarkerOptionsUnknown = {
            		    radius: radius, fillColor: "#a4f",
            		    color: "#000", weight: 1,
            		    opacity: opacity, fillOpacity: fillopacity,
            	    };
            	    var geojsonMarkerOptionsPending = {
            		    radius: radius, fillColor: "#0000ff",
            		    color: "#000", weight: 1,
            		    opacity: opacity, fillOpacity: fillopacity,
            	    };
                    switch(parseInt(feature.properties.worststatus)){
                        case 0:
		                    options = geojsonMarkerOptionsOk;
                            break;
                        case 1:
                            options = geojsonMarkerOptionsWarning;
                            break;
                        case 2:
                            options = geojsonMarkerOptionsCritical;
                            break;
                        case 3:
                            options = geojsonMarkerOptionsUnknown;
                            break;
                        case 99:
                            options = geojsonMarkerOptionsPending;
                            break;    
                    }
                    console.log("options: %o", options);
                    return L.circleMarker(latlng, options);
		        }
		    }).addTo(markers);
            map.addLayer(markers);
	    }
            xhr.onreadystatechange = function(e) {
	           if (xhr.readyState === 4) {
	            	if (xhr.status === 200) {
	                	var data = JSON.parse(xhr.responseText);
				        addDataToMap(data,map);
	                }
	           }
            };
            xhr.send(null);
        },
        

	onRenderedContainer: function(event) {
            xhri = new XMLHttpRequest();
            xhri.open('GET', 'osm/conf', true);
	    var confdata; 
            var hmmm=this;
            xhri.onreadystatechange = function(e) {
            if (xhri.readyState === 4) {
            	if (xhri.status === 200) {
                	confdata = JSON.parse(xhri.responseText);
	        		var view=[];
        			view[0]=parseFloat(confdata.x);	
        			view[1]=parseFloat(confdata.y);
        			var zoom=parseInt(confdata.zoom)	
        			map = L.map('map').setView(view, zoom);
        			L.tileLayer('http://{s}.tile.osm.org/{z}/{x}/{y}.png', {
        			   attribution: '&copy; <a href="http://osm.org/copyright">OpenStreetMap</a> contributors'
        			}).addTo(map);
        	   		hmmm.updateOsmData();
        	   		hmmm.registerTimer();
                }
           }
           };
           xhri.send(null);
        },
    };

    Icinga.availableModules.osm = Osm;

}(Icinga));
