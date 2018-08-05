<?php  
class LinkModel extends Model
{
	public $table = 'link';

	public function get_all_data(){
		return $this->all();
	}
}