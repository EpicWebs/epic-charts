<?php

class Epic_Charts_Public {
	
	private $plugin_name;
	private $version;
	
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->graphData = array();
		add_shortcode( 'epicchart', array( $this, 'epic_charts_shortcode' ) );

	}
	
	public function enqueue_styles() {
		
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/epic-charts-public.css', array(), $this->version, 'all' );

	}
	
	public function enqueue_scripts() {

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/Chart.bundle.min.js', array( 'jquery' ), $this->version, false );
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/epic-charts-public.js', array( 'jquery' ), $this->version, false );

	}
	
	public function generate_chart_view($chartId) {
		$chart_data = preg_replace_callback ( '!s:(\d+):"(.*?)";!', function($match) {      
			return ($match[1] == strlen($match[2])) ? $match[0] : 's:' . strlen($match[2]) . ':"' . $match[2] . '";';
		}, get_post_meta( $chartId, '_epic_chart_datasets', true ));
	
		$graphData = array(
			"graphWidth" 		=> get_post_meta( $chartId, '_epic_graph_width', true ),
			"graphHeight" 		=> get_post_meta( $chartId, '_epic_graph_height', true ),
			"graphType" 		=> get_post_meta( $chartId, '_epic_graph_type', true ),
			"datasets" 			=> unserialize($chart_data),
		);
		
		if(!empty($graphData['datasets'])) {
		
			$view = "<canvas id='graph-{$chartId}' width='{$graphData['graphWidth']}' height='{$graphData['graphHeight']}'></canvas>";
			
			$labelsArray = array();
			$datasetHtml = "";
			
			// CHART DATASETS
			foreach($graphData['datasets'] as $key => $dataset) {
				$dataSetId = 1;
				
				foreach($dataset as $key => $set) {				
					if($dataSetId == 1) {	
						foreach($set['data'] as $data) {
							array_push($labelsArray, html_entity_decode($data['label']));
						}
					}
					
					$set['label'] = str_replace("&#39;", "\'", $set['label']);
					
					$datasetHtml .= "{
						label: '" . html_entity_decode($set['label']) . "',
						data: [" . $this->returnGraphData($set['data']) . "],
						";
						
						switch($graphData['graphType']) {
							case "line":
								$datasetHtml .= "pointBackgroundColor: [" . $this->returnGraphBackgroundColours($set['data']) . "],";
								break;
							default;
								$datasetHtml .= "backgroundColor: [" . $this->returnGraphBackgroundColours($set['data']) . "],";
								break;
						}
					
					// Additional dataset properties
					$datasetHtml .= "
						borderWidth: 2, 
						pointRadius: 5,
					";
					
					if($graphData['graphType'] == "line") {
						$datasetHtml .= "
							borderColor: '{$set['line_color']}',
						";
					}
					
					$datasetHtml .= "},";
				
					$dataSetId++;
				}
			}
			
			// START OF CHART
			$view .= "
			<script>
			var ctx = document.getElementById('graph-{$chartId}').getContext('2d');
			var myChart = new Chart(ctx, {
				type: '{$graphData['graphType']}',
				data: {
					labels: [" . $this->returnGraphLabels($labelsArray) . "],
					datasets: [{$datasetHtml}],
					";
				
			// CHART TYPE OPTIONS
			if($graphData['graphType'] == "line") {
				$view .= "fill: false,
				";
			}
				
			// END OF CHART
				$view .= "
				},
				options: {
					title: {
						display: true,
						text: '" . html_entity_decode(get_the_title($chartId)) . "',
					},
					scales: {
						yAxes: [{
							ticks: {
								beginAtZero:true
							}
						}],
						xAxes: [{
							ticks: {
								beginAtZero:true
							}
						}],
					},";
					
				if($graphData['graphType'] == "radar") {
					$view .= "scale: {
							ticks: {
								beginAtZero:true
							}
						},";
				}
					
				$view .= "
					legend: {
						display: true,
						position: 'bottom',
					},
				}
			});
			</script>";
		
		} else {
			$view = "";
		}
		
		return $view;
	}
	
	private function returnGraphLabels($labelData) {
		$labels = array();
		
		foreach($labelData as $value) {
			array_push($labels, "'" . $value . "'");
		}
		
		return implode(",", $labels);
	}
	
	public function returnGraphData($dataSets) {
		$data = array();
		
		foreach($dataSets as $key => $value) {
			array_push($data, "'" . $value['value'] . "'");
		}
		
		return implode(",", $data);		
	}
	
	public function returnGraphBackgroundColours($dataSets) {
		$backgroundColor 	= array();
		
		foreach($dataSets as $key => $value) {
			array_push($backgroundColor, "'" . $value['background_color'] . "'");
		}
		
		return implode(",", $backgroundColor);		
	}
	
	public static function epic_charts_shortcode( $atts, $content = "" ) {
		$atts = shortcode_atts( array(
			'chartid' => 1,
		), $atts, 'epicchart' );
		
		return $this->generate_chart_view($atts['chartid']);
	}

	private function nice_looking_code($string) {
		echo highlight_string("<?php\n\$data =\n" . var_export($string, true) . ";\n?>");
	}
	
}