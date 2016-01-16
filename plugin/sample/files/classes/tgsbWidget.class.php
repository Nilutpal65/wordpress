<?php
/**
 * Widget Class for the HTML searchboxes for Travel-Search plugin
 *
 * @package Travel-Search
 * @subpackage Widget Class
 * @author Travelgrove Labs (http://labs.travelgrove.com/)
 * @since 1.2
 */
 
class tgsbWidget extends WP_Widget {
	
	private $tgsb_sizes;

        public function __construct() {
		// widget actual processes
		$this->tgsb_sizes	= array('300x250', '300x533', '160x600', '728x90','dynamic');
		$widget_ops	= array(
			// the name of the class
			'classname'	=> 'tgsbWidget',
			// the description for the widget that will appear on the Widgets screen
			'description'	=> 'Travel Search Widget');
		parent::__construct(
			// ID for the list item of the widget
	 		'tgsbWidget',
			// name displayed in the Widgets screen
			'Travel Search Widget',
			// Args
			$widget_ops);
	}

	/**	the form which will appear inside the Widgets Admin Page	*/
 	public function form( $instance ) {
		// outputs the options form on admin
		$defaults	= array('size' => '300x250');
		/**	merging instance settings w/ default values	*/
		$instance	= wp_parse_args( (array) $instance, $defaults);
		$size		= $instance['size'];
		/** get_field_name -> to uniquely identify form fields in the configuration form of Widgets */
		?>
		<p>Size:<select name="<?php echo $this->get_field_name('size'); ?>">
		<?php foreach($this->tgsb_sizes as $tgsb_size) : ?>
			<option value="<?php echo esc_attr($tgsb_size) ?>" <?php selected($size, $tgsb_size) ?>><?php echo esc_attr($tgsb_size); ?></option>
		<?php endforeach; ?>
		</select><br /><br />
		See Travel Search > Travel Search Page to customize the search settings. 
		<?php
	}

	/**	validating the inputs for the widget (size only currently)	*/
	public function update( $new_instance, $old_instance ) {
		$instance		= $old_instance;
		$instance['size']	= strip_tags( $new_instance['size'] );
		/**	use 300x250 as fallback if the size is not in the given list	*/
		$instance['size']	= (!in_array($instance['size'], $this->tgsb_sizes)) ? '300x250' : $instance['size'];
		return $instance;
	}

	/**	outputs the content of the widget	*/
	public function widget( $args, $instance ) {
		$options = ($instance['size'] == '300x250') ? '' : ' options=\'{"size":"'.$instance['size'].'"}\'';
		echo $args['before_widget'];
		echo do_shortcode('[tg_searchboxes'.$options.']');
		echo $args['after_widget'];
	}
}
?>