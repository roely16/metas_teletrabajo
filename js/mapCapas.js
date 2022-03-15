var entrada = 0;
var mapMain;
		
// @formatter:off
require([
        "esri/map",
        "dojo/ready",
        "dojo/parser",
        "dojo/on",
        "dojo/_base/array",
        "dijit/layout/BorderContainer",
        "dijit/layout/ContentPane",
        "esri/layers/FeatureLayer",
        "esri/tasks/GeometryService",
        "esri/dijit/editing/Editor",
        "esri/dijit/editing/TemplatePicker",
        "esri/config",
		"esri/symbols/SimpleFillSymbol", 
		"esri/symbols/SimpleLineSymbol",
		"esri/symbols/TextSymbol",
		"esri/Color",
		"esri/renderers/SimpleRenderer",
		"esri/dijit/FeatureTable",		
        "esri/tasks/QueryTask","esri/tasks/query",
		"esri/layers/LabelClass",
		"esri/dijit/Print",
		"esri/tasks/PrintTemplate",
		"esri/tasks/PrintParameters",
		"dojo/dom",
		"esri/tasks/PrintTask",
		],
    function (Map,
              ready, parser, on, array,
              BorderContainer, ContentPane,FeatureLayer,GeometryService,Editor,TemplatePicker,config,SimpleFillSymbol,SimpleLineSymbol,TextSymbol,Color,SimpleRenderer,FeatureTable,QueryTask,Query,LabelClass,Print,PrintTemplate,PrintParameters,dom,PrintTask) {
// @formatter:on

        // Wait until DOM is ready *and* all outstanding require() calls have been resolved
        ready(function () {             
            // Parse DOM nodes decorated with the data-dojo-type attribute
            parser.parse();
			config.defaults.io.timeout = 160000;
            var entrada = 0;
            /*
             * Step: Specify the proxy Url
             */
            // Create the map
            mapMain = new Map("divMap", {
                basemap: "topo",
               center: [-90.483445,14.6214083],
               zoom: 12,
			   showLabels : true
            });

            var flAntenas, flZonas,flPiscinas,flPozos,prImpresion,ptTemplate;
        	
			flZonas = new FeatureLayer("http://172.23.25.246:6080/arcgis/rest/services/CiudadDeGuatemala/FeatureServer/5",{
				outFields: ['*']				
			});
		
flAntenas = new FeatureLayer("http://172.23.25.246:6080/arcgis/rest/services/Capas/FeatureServer/0",{
                outFields: ['*'],
				mode: FeatureLayer.MODE_ONDEMAND,
                visible: true,
				name: "Antenas"			
            });			

							
			//Fin impresion
			//Fin Query
            // Listen for the editable layers to finish loading
          
            // add the editable layers to the map
			var symbol = new SimpleFillSymbol(
                             SimpleFillSymbol.STYLE_SOLID,
                             new SimpleLineSymbol(
                                     SimpleLineSymbol.STYLE_SOLID,
                                     new Color([0,0,0,1]),
                                     1
                                     ),
                                     new Color([195,217,56,0.35])
                             );

			flZonas.setRenderer(new SimpleRenderer(symbol));

                    var zonasColor = new Color("#31B404");
					var zonasLabel = new TextSymbol().setColor(zonasColor);
					zonasLabel.font.setSize("1em");
                    zonasLabel.font.setFamily("arial");
					var json = {
                                "labelExpressionInfo": {"value": "{ZONA}"}
                               };
                    var labelClass = new LabelClass(json);
 					labelClass.symbol = zonasLabel;
					flZonas.setLabelingInfo([ labelClass ]);								
			
			mapMain.addLayer(flZonas);       
			mapMain.addLayer(flAntenas);
			//mapMain.addLayer(flPiscinas);
			//mapMain.addLayer(flPozos);
            mapMain.on("layer-add-result",function(){
             
			});
        });
    });