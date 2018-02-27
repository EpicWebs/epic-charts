<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Plugin_Name
 * @subpackage Plugin_Name/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Plugin_Name
 * @subpackage Plugin_Name/admin
 * @author     Your Name <email@example.com>
 */
class Epic_Charts_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->cpt = 'epic_chart';
		
		add_action( 'init', array( $this, 'epic_charts_custom_post_types' ), 0 );
		add_action( 'add_meta_boxes', array( $this, 'epic_charts_custom_metabox' ) );
		add_action( 'save_post', array( $this, 'save_epic_charts_metabox_data' ) );
		add_action( 'manage_epic_chart_posts_custom_column' , array( $this, 'epic_chart_custom_columns' ), 10, 2 );
		add_filter( 'manage_epic_chart_posts_columns' , array( $this, 'epic_charts_add_shortcode_columns' ) );

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles($hook_suffix) {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Plugin_Name_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Plugin_Name_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		if( in_array($hook_suffix, array('post.php', 'post-new.php') ) ){
			$screen = get_current_screen();

			if( is_object( $screen ) && $this->cpt == $screen->post_type ){

				wp_enqueue_style( 'wp-color-picker' ); 
				wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/epic-charts-admin.css', array(), $this->version, 'all' );

			}
		}

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts($hook_suffix) {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Plugin_Name_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Plugin_Name_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		if( in_array($hook_suffix, array('post.php', 'post-new.php') ) ){
			$screen = get_current_screen();

			if( is_object( $screen ) && $this->cpt == $screen->post_type ){

				wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/epic-charts-admin.js', array( 'jquery','wp-color-picker' ), $this->version, false );
				wp_enqueue_script( 'jquery-ui-draggable', array( 'jquery', 'jquery-ui-mouse', 'jquery-ui-widget', 'jquery-ui-droppable' ), $this->version, false );

			}
		}
		
    }
	
	public function epic_charts_custom_metabox() {
		$screens = ['epic_chart'];
		
		foreach ($screens as $screen) {
			add_meta_box(
				'epic_chart_box',
				'Chart Details',
				'epic_chart_box_html',
				$screen,
				'normal',
				'high'
			);
		}
		
		function epic_chart_box_html() {
			
			if(isset($_GET['post'])) {
				
				$post_id = $_GET['post'];
			
				$epic_graph_width 		= get_post_meta( $post_id, '_epic_graph_width', true );
				$epic_graph_height 		= get_post_meta( $post_id, '_epic_graph_height', true );
				$epic_graph_type 		= get_post_meta( $post_id, '_epic_graph_type', true );		
				$chart_data		 		= get_post_meta( $post_id, '_epic_chart_datasets', true );
				
			} else {
			
				$epic_graph_width 		= "";
				$epic_graph_height 		= "";
				$epic_graph_type 		= "";
				$chart_data		 		= array();
				
			}
			
			if(is_string($chart_data)) {
				$chart_data = preg_replace_callback ( '!s:(\d+):"(.*?)";!', function($match) {      
					return ($match[1] == strlen($match[2])) ? $match[0] : 's:' . strlen($match[2]) . ':"' . $match[2] . '";';
				}, $chart_data);
				
				$chart_data = unserialize($chart_data);
			}
		?>
			<table class="form-table">
				<tr>
					<th scope="row" style="padding-bottom:0;padding-top:0px;">
						<h3>Chart Information</h3>
					</th>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<th scope="row">
						<label>Width</label>
					</th>
					<td>
						<input type="text" name="graphWidth" id="graphWidth" placeholder="Graph width..." value="<?php echo $epic_graph_width; ?>">
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label>Height</label>
					</th>
					<td>
						<input type="text" name="graphHeight" id="graphHeight" placeholder="Graph height..." value="<?php echo $epic_graph_height; ?>">
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label>Graph Type</label>
					</th>
					<td>
						<select name="graphType" id="graphType">
							<option value="">Select type...</option>
							<option value="line" <?php if($epic_graph_type == "line") { ?>selected="selected"<?php } ?>>Line</option>
							<option value="bar" <?php if($epic_graph_type == "bar") { ?>selected="selected"<?php } ?>>Bar</option>
							<option value="horizontalBar" <?php if($epic_graph_type == "horizontalBar") { ?>selected="selected"<?php } ?>>Horizontal Bar</option>
							<option value="radar" <?php if($epic_graph_type == "radar") { ?>selected="selected"<?php } ?>>Radar</option>
							<option value="pie" <?php if($epic_graph_type == "pie") { ?>selected="selected"<?php } ?>>Pie</option>
							<option value="doughnut" <?php if($epic_graph_type == "doughnut") { ?>selected="selected"<?php } ?>>Doughnut</option>
							<option value="polarArea" <?php if($epic_graph_type == "polarArea") { ?>selected="selected"<?php } ?>>Polar Area</option>
							<option value="bubble" <?php if($epic_graph_type == "bubble") { ?>selected="selected"<?php } ?>>Bubble</option>
							<option value="scatter" <?php if($epic_graph_type == "scatter") { ?>selected="selected"<?php } ?>>Scatter</option>
						</select>
					</td>
				</tr>
				<tr>
					<table class="form-table">
						<tr>
							<th colspan="2" scope="row" style="padding-bottom:0;padding-top:40px;">
								<h3 style="margin: 0;">Dataset(s)</h3>
								<p style="font-style:italic;">Note: The first data row assigns labels to the subsequent data rows.</p>
							</th>
						</tr>
						<tr>
							<td colspan="2" style="padding:0;" id="sortableDataTables">
								<?php
								if(!empty($chart_data)) {
									$i = 1;
									
									foreach($chart_data['datasets'] as $dataset) {
								?>
								
								<table class="form-table epic-chart-data">
									<tr>
										<th scope="row">
                                            <span class="dashicons-before dashicons-move"></span>
											<label>Dataset Label</label>
										</th>
										<td>
											<input type="text" name="chartdata[datasets][set<?php echo $i; ?>][label]" id="chartdata[datasets][set<?php echo $i; ?>][label]" placeholder="Dataset label..." value="<?php if(isset($dataset['label'])) { echo $dataset['label']; } ?>">
										</td>
									</tr>
									<?php /**
									<tr>
										<th scope="row">
											<label>Background Color</label>
										</th>
										<td>
											<input type="text" name="chartdata[datasets][set<?php echo $i; ?>][background_color]" id="chartdata[datasets][set<?php echo $i; ?>][background_color]" class="color-field" placeholder="Background color..." value="<?php if(isset($dataset['background_color'])) { echo $dataset['background_color']; } ?>">
										</td>
									</tr>
									**/ ?>
									<tr class="line-color-row">
										<th scope="row">
											<label>Line Color</label>
										</th>
										<td>
											<input type="text" name="chartdata[datasets][set<?php echo $i; ?>][line_color]" id="chartdata[datasets][set<?php echo $i; ?>][line_color]" class="color-field" placeholder="Line color..." value="<?php if(isset($dataset['line_color'])) { echo $dataset['line_color']; } ?>">
										</td>
									</tr>
									<tr>
										<th scope="row">
											<label>Data</label>
										</th>
										<td>
											<table data-setid="set<?php echo $i; ?>" class="chart-data-table">
												<thead>
													<tr>
														<th class="label">Label</th>
														<th class="value">Value</th>
														<th class="background-color">Color</th>
													</tr>
												</thead>
												<tbody>
													<?php create_data_row_view($dataset, $i); ?>
												</tbody>
											</table>
											<div class="actions">
												<button id="add-data-row" class="button button-primary">Add Row of Data</button>
											</div>
										</td>
									</tr>
								</table>
								
								<?php $i++; } } else { ?>

								<table class="form-table epic-chart-data">
									<tr>
										<th scope="row">
											<label>Dataset Label</label>
										</th>
										<td>
											<input type="text" name="chartdata[datasets][set1][label]" id="chartdata[datasets][set1][label]" placeholder="Dataset label..." value="">
										</td>
									</tr>
									<?php /**
									<tr>
										<th scope="row">
											<label>Background Color</label>
										</th>
										<td>
											<input type="text" name="chartdata[datasets][set1][background_color]" id="chartdata[datasets][set1][background_color]" class="color-field" placeholder="Background color..." value="">
										</td>
									</tr>
									**/ ?>
									<tr class="line-color-row">
										<th scope="row">
											<label>Border Color</label>
										</th>
										<td>
											<input type="text" name="chartdata[datasets][set1][line_color]" id="chartdata[datasets][set1][line_color]" class="color-field" placeholder="Line color..." value="">
										</td>
									</tr>
									<tr>
										<th scope="row">
											<label>Data</label>
										</th>
										<td>
											<table data-setid="set1" class="chart-data-table">
												<thead>
													<tr>
														<th class="label datasetLabelHeader">Label</th>
														<th class="value">Value</th>
														<th class="background-color">Color</th>
													</tr>
												</thead>
												<tbody>
													<?php create_data_row_view(array(), 1); ?>
												</tbody>
											</table>
											<div class="actions">
												<button id="add-data-row" class="button button-primary">Add Row of Data</button>
											</div>
										</td>
									</tr>
								</table>
								
								<?php } ?>
							</td>
						</tr>
						<tr>
							<td colspan="2">
								<div class="actions">
									<button style="float:right;" id="add-data-set" class="button button-primary">Add New Dataset</button>
								</div>
							</td>
						</tr>
					</table>
				</tr>
			</table>
		<?php
		}
	
		function create_data_row_view($data, $setid) {			
			if(!empty($data)) {
				$data = $data['data'];
				
				$i = 1;
				
				foreach($data as $data_row) {
					?>
					<tr data-rowid="<?php echo $i; ?>" class="data-row">
						<td><input class="datasetLabel" type="text" name="chartdata[datasets][set<?php echo $setid; ?>][data][<?php echo $i; ?>][label]" placeholder="Label..." value="<?php if(isset($data_row['label'])) { echo $data_row['label']; } ?>"></td>
						<td><input type="text" name="chartdata[datasets][set<?php echo $setid; ?>][data][<?php echo $i; ?>][value]" placeholder="Value..." value="<?php if(isset($data_row['value'])) { echo $data_row['value']; } ?>"></td>
						<td><input type="text" name="chartdata[datasets][set<?php echo $setid; ?>][data][<?php echo $i; ?>][background_color]" class="color-field" placeholder="Color..." value="<?php if(isset($data_row['background_color'])) { echo $data_row['background_color']; } ?>"></td>
					</tr>
					<?php
					$i++;
				}
				
			} else {
				
				?>
				<tr data-rowid="1" class="data-row">
					<td><input class="datasetLabel" type="text" name="chartdata[datasets][set1][data][1][label]" placeholder="Label..." value=""></td>
					<td><input type="text" name="chartdata[datasets][set1][data][1][value]" placeholder="Value..." value=""></td>
					<td><input type="text" name="chartdata[datasets][set1][data][1][background_color]" class="color-field" placeholder="Color..." value=""></td>
				</tr>
				<?php
				
			}
		}
	}
	
	public function save_epic_charts_metabox_data($post_id) {
		if (array_key_exists('graphWidth', $_POST)) {
			update_post_meta($post_id, '_epic_graph_width', $_POST['graphWidth']);
		}
		
		if (array_key_exists('graphHeight', $_POST)) {
			update_post_meta($post_id, '_epic_graph_height', $_POST['graphHeight']);
		}
		
		if (array_key_exists('graphType', $_POST)) {
			update_post_meta($post_id, '_epic_graph_type', $_POST['graphType']);
		}
		
		if (array_key_exists('chartdata', $_POST)) {
			$chartdata = serialize($_POST['chartdata']);
			$chartdata = str_replace("\'", "&#39;", $chartdata);
		
			update_post_meta($post_id, '_epic_chart_datasets', $chartdata);
		}
	}

	public static function epic_charts_custom_post_types() {
		// Register Custom Post Type
		$labels = array(
			'name'                  => _x( 'Charts', 'Post Type General Name', 'epic-charts' ),
			'singular_name'         => _x( 'Chart', 'Post Type Singular Name', 'epic-charts' ),
			'menu_name'             => __( 'Charts', 'epic-charts' ),
			'name_admin_bar'        => __( 'Charts', 'epic-charts' ),
			'archives'              => __( 'Chart Archives', 'epic-charts' ),
			'attributes'            => __( 'Chart Attributes', 'epic-charts' ),
			'parent_item_colon'     => __( 'Parent Chart:', 'epic-charts' ),
			'all_items'             => __( 'All Charts', 'epic-charts' ),
			'add_new_item'          => __( 'Add New Chart', 'epic-charts' ),
			'add_new'               => __( 'Add New', 'epic-charts' ),
			'new_item'              => __( 'New Chart', 'epic-charts' ),
			'edit_item'             => __( 'Edit Chart', 'epic-charts' ),
			'update_item'           => __( 'Update Chart', 'epic-charts' ),
			'view_item'             => __( 'View Chart', 'epic-charts' ),
			'view_items'            => __( 'View Charts', 'epic-charts' ),
			'search_items'          => __( 'Search Chart', 'epic-charts' ),
			'not_found'             => __( 'Not found', 'epic-charts' ),
			'not_found_in_trash'    => __( 'Not found in Trash', 'epic-charts' ),
			'featured_image'        => __( 'Featured Image', 'epic-charts' ),
			'set_featured_image'    => __( 'Set featured image', 'epic-charts' ),
			'remove_featured_image' => __( 'Remove featured image', 'epic-charts' ),
			'use_featured_image'    => __( 'Use as featured image', 'epic-charts' ),
			'insert_into_item'      => __( 'Insert into chart', 'epic-charts' ),
			'uploaded_to_this_item' => __( 'Uploaded to this chart', 'epic-charts' ),
			'items_list'            => __( 'Charts list', 'epic-charts' ),
			'items_list_navigation' => __( 'Charts list navigation', 'epic-charts' ),
			'filter_items_list'     => __( 'Filter charts list', 'epic-charts' ),
					);
		$args = array(
			'label'                 => __( 'Chart', 'epic-charts' ),
			'description'           => __( 'Charts using the chartjs project.', 'epic-charts' ),
			'labels'                => $labels,
			'supports'              => array( 'title' ),
			'hierarchical'          => false,
			'public'                => true,
			'show_ui'               => true,
			'show_in_menu'          => true,
			'menu_position'         => 25,
			'menu_icon'             => 'dashicons-chart-area',
			'show_in_admin_bar'     => false,
			'show_in_nav_menus'     => false,
			'can_export'            => false,
			'has_archive'           => false,
			'exclude_from_search'   => true,
			'publicly_queryable'    => true,
			'capability_type'       => 'page',
			'show_in_rest'          => false,
		);
		
		register_post_type( 'epic_chart', $args );
	}
	
	public function epic_charts_add_shortcode_columns($columns) {
		unset($columns['date']);
		
		return array_merge($columns, 
			array(
				'chart_type' => __('Chart Type'),	
				'chart_width' => __('Chart Width'),	
				'chart_height' => __('Chart Height'),	
				'shortcode' => __('Shortcode'),			
			)
		);
	}

	public function epic_chart_custom_columns( $column, $post_id ) {
		switch ( $column ) {
			case 'chart_type':
				$chartType = get_post_meta( $post_id, '_epic_graph_type', true );
				$chartType = ucwords(implode(" ", preg_split('/(?=[A-Z])/', $chartType) ) );
				
				echo $chartType;
				break;
			case 'chart_width':
				echo get_post_meta( $post_id, '_epic_graph_width', true );
				break;
			case 'chart_height':
				echo get_post_meta( $post_id, '_epic_graph_height', true );
				break;
			case 'shortcode':
				echo '[epicchart chartid="' . $post_id . '"]';
				break;
		}
	}
}
