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
            
            switch($graphData['graphType']) {
                case "radar":
                // DEFAULT CHART DATASETS
                    include 'partials/epic-charts-radar.php';
                    break;
                case "line":
                // DEFAULT CHART DATASETS
                    include 'partials/epic-charts-line.php';
                    break;
                default:
                // DEFAULT CHART DATASETS
                    include 'partials/epic-charts-default.php';
                    break;
            }
		
		} else {
			$view = "";
		}
		
		return $view;
	}
	
	public static function epic_charts_shortcode( $atts, $content = "" ) {
		$atts = shortcode_atts( array(
			'chartid' => 1,
		), $atts, 'epicchart' );
		
		return $this->generate_chart_view($atts['chartid']);
	}
	
}

require_once 'partials/utilities.php';