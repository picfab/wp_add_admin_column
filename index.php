class create_admin_column{
	private string $field_id;
	private string $field_name;
	private array $allow_pts;
	private string $new_print_value;
	// private function|null $new_print_value;
	function __construct(
		$field_id,
		$field_name,
		$sortable=false,
		$allow_pts=['post'],
		$new_print_value=null
	) {
		$this->field_id = $field_id;
		$this->field_name = $field_name;
		$this->allow_pts= $allow_pts;
		$this->new_print_value =  $new_print_value;

		$this->create_column();
		if($sortable){
			$this->sortable();
		}
	}

	private function create_column(){
		foreach($this->allow_pts as $pt){

			add_filter('manage_'.$pt.'_posts_columns', function($columns) {
				$columns[$this->field_id] = $this->field_name;
				return $columns;
			});

			add_action('manage_'.$pt.'_posts_custom_column', function($column_key, $post_id) {
				$values = get_post_meta($post_id,$this->field_id,true);
				if ($column_key == $this->field_id) {
					if($this->new_print_value){
						echo $this->overide_print_value($this->new_print_value, $values,$post_id);
					}else{
						echo $this->print_value($values);
					}
				}
			}, 10, 2);
		}
		
	}

	private function print_value($values){
		ob_start();
			if(is_array($values)){
				echo '<ul style="list-style-type: disc;">';
					foreach($values as $key => $value){
						echo '<li>'.$value.'</li>';
					}
				echo '</ul>';
			}
			else {
				echo $values;
			}
		$html = ob_get_contents();
		ob_end_clean();
		return $html ;
	}

	private function overide_print_value($func, $value,$post_id)
	{
		return $func($value,$post_id);
	}

	private function sortable(){
		foreach($this->allow_pts as $pt){
			add_filter('manage_edit-'.$pt.'_sortable_columns', function($columns) {
					$columns[$this->field_id] = $this->field_id;
				return $columns;
			});
			add_action('pre_get_posts', function($query) {
				if (!is_admin()) {
						return;
				}
				$orderby = $query->get('orderby');
						
					if($orderby===$this->field_id){
									$query->set( 'meta_query', array(
										'relation' => 'OR',
										array(
												'key' => $this->field_id, 
												'compare' => 'EXISTS'
										),
										array(
												'key' => $this->field_id, 
												'compare' => 'NOT EXISTS'
										)
								) );
										$query->set('orderby', 'meta_value');
										}
	
			});
		}
	}
}


function new_column(
	$field_id,
	$field_name,
	$sortable=false,
	$allow_pts=['post'],
	$newFunction=null
	){
		add_action('acf/init',function(){

			new create_admin_column($field_id,
				$field_name,
				$sortable,
				$allow_pts,
				$newFunction
			);
		}

}	
