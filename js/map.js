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

            var flAntenas, flZonas,flManzanas,flPredios,prImpresion,ptTemplate;
        	
			flZonas = new FeatureLayer("http://172.23.25.246:6080/arcgis/rest/services/CiudadDeGuatemala/FeatureServer/5",{
				outFields: ['*']				
			});
		
flAntenas = new FeatureLayer("http://172.23.25.246:6080/arcgis/rest/services/Cisternas/MapServer/0",{
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
			var printer = new Print({
				  map: mapMain,
				  templates:ptTemplate,
//                  url: "http://gisapps.fortsmithar.gov/arcgis/rest/services/Utilities/PrintingTools/GPServer/Export%20Web%20Map%20Task",//Impresion 
                  url: "http://172.23.25.246:6080/arcgis/rest/services/Utilities/PrintingTools/GPServer/Export%20Web%20Map%20Task"
				  }, dom.byId("printButton"));
            printer.startup();
            	
            mapMain.on("layer-add-result",function(){
                flManzanas.on("click",function(evt){	
 				      zona = evt.graphic.attributes.ZONA;
					  manzana = evt.graphic.attributes.MANZANA;
           		     //dibujar los predios
					 flPredios = new FeatureLayer("http://172.23.25.246:6080/arcgis/rest/services/TestMapaPot/FeatureServer/0",{
						 outFields: ['*'],
				         mode: FeatureLayer.MODE_ONDEMAND,
                         visible: true
					 });
					
					var symbolB = new SimpleFillSymbol(
                             SimpleFillSymbol.STYLE_SOLID,
                             new SimpleLineSymbol(
                                     SimpleLineSymbol.STYLE_SOLID,
                                     new Color([0,0,0,1]),
                                     1
                                     ),
                                     new Color([159,129,247,0.35])
                             );

			        flPredios.setRenderer(new SimpleRenderer(symbolB));
					//Etiqueta del predio
					var prediosColor = new Color("#FFF");
					var prediosLabel = new TextSymbol().setColor(prediosColor);
					prediosLabel.font.setSize("8pt");
                    prediosLabel.font.setFamily("arial");
					var json = {
                                "labelExpressionInfo": {"value": "{PREDIO}"}
                               };
                    var labelClass = new LabelClass(json);
 					labelClass.symbol = prediosLabel;
					flPredios.setLabelingInfo([ labelClass ]);					
					// alert("Zona"+evt.graphic.attributes.ZONA+" Manzana "+evt.graphic.attributes.MANZANA);
					 flPredios.setDefinitionExpression("ZONA="+zona+" AND MANZANA="+manzana);
					 mapMain.addLayer(flPredios);
					 cuentaPredios = parseInt(document.getElementById('cuentaPredios').value);
					 cuentaPredios = cuentaPredios + 1;
			         document.getElementById('cuentaPredios').value = parseInt(cuentaPredios);										 
			    });
			});
            flZonas.on("click",function(evt){
				zona = evt.graphic.attributes.ZONA;
				//remover la capa que ya existia
				cuenta = parseInt(document.getElementById('cuenta').value);
				cuentaPredios = parseInt(document.getElementById('cuentaPredios').value);
				if(cuenta>0){
				    mapMain.removeLayer(flManzanas);
					if(cuentaPredios>0){
					    mapMain.removeLayer(flPredios);
					}
				    //Dibujar la capa  de la zona que se selecciono
				    flManzanas = new FeatureLayer("http://172.23.25.246:6080/arcgis/rest/services/CiudadDeGuatemala/FeatureServer/3",{
                    outFields: ['*'],
				    mode: FeatureLayer.MODE_ONDEMAND,
                    visible: true,
				    name: "Manzanas"			
                    });
					
					var symbolA = new SimpleFillSymbol(
                             SimpleFillSymbol.STYLE_SOLID,
                             new SimpleLineSymbol(
                                     SimpleLineSymbol.STYLE_SOLID,
                                     new Color([0,0,0,1]),
                                     1
                                     ),
                                     new Color([255,128,0,0.35])
                             );

			        flManzanas.setRenderer(new SimpleRenderer(symbolA));
					
					var manzanasColor = new Color("#FF0000");
					var manzanasLabel = new TextSymbol().setColor(manzanasColor);
					manzanasLabel.font.setSize("10pt");
					manzanasLabel.font.setFamily("arial");
					var json = {
						        "labelExpressionInfo":{"value":"{MANZANA}"}
					           };
					var labelClass = new LabelClass(json);
					labelClass.symbol = manzanasLabel;
					flManzanas.setLabelingInfo([labelClass]);
			        flManzanas.setDefinitionExpression("ZONA="+zona);
                    mapMain.addLayer(flManzanas);
			        cuenta = cuenta + 1;
			        document.getElementById('cuenta').value = parseInt(cuenta);
				}else{
					cuentaPredios = parseInt(document.getElementById('cuentaPredios').value);
					if(cuentaPredios>0){
					    mapMain.removeLayer(flPredios);
					}
					flManzanas = new FeatureLayer("http://172.23.25.246:6080/arcgis/rest/services/CiudadDeGuatemala/FeatureServer/3",{
                        outFields: ['*'],
				        mode: FeatureLayer.MODE_ONDEMAND,
                        visible: true,
				        name: "Antenas"			
                    });
				var symbolA = new SimpleFillSymbol(
                                  SimpleFillSymbol.STYLE_SOLID,
                              new SimpleLineSymbol(
                                     SimpleLineSymbol.STYLE_SOLID,
                                     new Color([0,0,0,1]),
                                     1
                                     ),
                                     new Color([255,128,0,0.35])
                             );

			flManzanas.setRenderer(new SimpleRenderer(symbolA));
			
					
					
					var manzanasColor = new Color("#FF0000");
					var manzanasLabel = new TextSymbol().setColor(manzanasColor);
					manzanasLabel.font.setSize("10pt");
                    manzanasLabel.font.setFamily("arial");
					var json = {
                                "labelExpressionInfo": {"value": "{MANZANA}"}
                               };
                    var labelClass = new LabelClass(json);
 					labelClass.symbol = manzanasLabel;
					flManzanas.setLabelingInfo([ labelClass ]);		   
			        flManzanas.setDefinitionExpression("ZONA="+zona);
                    mapMain.addLayer(flManzanas);
			        cuenta = cuenta + 1;
			        document.getElementById('cuenta').value = parseInt(cuenta);				
				}			
			});
        });
    });