<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Merchants_disapproval_model extends CI_Model {
	private 
		$_table	= 'merchant_disapproval_reason  merchant_disapproval_reason',
		$_table_x	= 'merchant_disapproval_reason';

	private
		$_id = "reason_id";

	function _data(
		$select = array('*'), 
		$inner_joints = array(), 
		$where = array(), 
		$where_in = array(), 
		$where_not_in = array(),
		$or_where = array(), 
		$or_where_in = array(),
		$or_where_not_in = array(),
		$order_by = array(), 
		$limit = 0, 
		$offset = 0,
		$is_regroup = false
		) {

		$this->db->select(ARRtoSTR($select), false);

		$this->db->from( $this->_table );

		if (!empty($inner_joints)) {
			foreach($inner_joints as $join) {
				if (isset($join['type'])) {
					$this->db->join(
						$join['table_name'],
						$join['condition'],
						$join['type']
					);
				} else {
					$this->db->join(
						$join['table_name'],
						$join['condition']
					);
				}
			}
		}
		
		if ($is_regroup) {
			$this->db->group_start();
		} 

		if(!empty($where_in)){
			foreach ($where_in as $i) {
				if (!isset($i['field']) && !isset($i['data'])) {
					continue;
				}
				
				$field 	= $i['field'];
				$data 	= $i['data'];
				
				$this->db->where_in(
					$field,
					$data
				);
			}
		}

		if(!empty($where_not_in)){
			foreach ($where_not_in as $i) {
				if (!isset($i['field']) && !isset($i['data'])) {
					continue;
				}
				
				$field 	= $i['field'];
				$data 	= $i['data'];
				
				$this->db->where_not_in(
					$field,
					$data
				);
			}
		}

		if(!empty($or_where)){
			foreach ($or_where as $i) {
				if (!isset($i['field']) && !isset($i['data'])) {
					continue;
				}
				
				$field 	= $i['field'];
				$data 	= $i['data'];
				$this->db->or_where(
					$field,
					$data
				);
			}
		}

		if(!empty($or_where_in)){
			foreach ($or_where_in as $i) {
				if (!isset($i['field']) && !isset($i['data'])) {
					continue;
				}
				
				$field 	= $i['field'];
				$data 	= $i['data'];
				$this->db->or_where_in(
					$field,
					$data
				);
			}
		}

		if(!empty($or_where_not_in)){
			foreach ($or_where_not_in as $i) {
				if (!isset($i['field']) && !isset($i['data'])) {
					continue;
				}
				
				$field 	= $i['field'];
				$data 	= $i['data'];
				$this->db->or_where_not_in(
					$field,
					$data
				);
			}
		}

		if ($is_regroup) {
			$this->db->group_end();
		} 

		if(!empty($where)){
			$is_multi_where = false;
			foreach ($where as $i) {
				if (!isset($i['field']) && !isset($i['data'])) {
					continue;
				}
				
				$field 	= $i['field'];
				$data 	= $i['data'];
				
				$this->db->where(
					$field,
					$data
				);

				$is_multi_where = true;
			}

			if (!$is_multi_where) {
				$this->db->where($where);
			}
		}

		if(!empty($limit)){
			$this->db->limit(
				$limit, 
				$offset
			);
		}
		
		if(!empty($order_by)) {
			$filter_by 	= $order_by['filter_by'];
			$sort_by	= $order_by['sort_by'];

			$this->db->order_by(
				$filter_by,
				$sort_by
			);
		}

		$query = $this->db->get();

		$results = $query->result_array();

		return $results;
	}

	function _datum(
		$select = array('*'), 
		$inner_joints = array(), 
		$where = array(), 
		$where_in = array(), 
		$where_not_in = array(),
		$or_where = array(), 
		$or_where_in = array(),
		$or_where_not_in = array(),
		$order_by = array(), 
		$limit = 0, 
		$offset = 0,
		$is_regroup = false
		) {

		$this->db->select(ARRtoSTR($select), false);

		$this->db->from( $this->_table );

		if (!empty($inner_joints)) {
			foreach($inner_joints as $join) {
				if (isset($join['type'])) {
					$this->db->join(
						$join['table_name'],
						$join['condition'],
						$join['type']
					);
				} else {
					$this->db->join(
						$join['table_name'],
						$join['condition']
					);
				}
			}
		}

		if ($is_regroup) {
			$this->db->group_start();
		} 

		if(!empty($where_in)){
			foreach ($where_in as $i) {
				if (!isset($i['field']) && !isset($i['data'])) {
					continue;
				}
				
				$field 	= $i['field'];
				$data 	= $i['data'];
				
				$this->db->where_in(
					$field,
					$data
				);
			}
		}

		if(!empty($where_not_in)){
			foreach ($where_not_in as $i) {
				if (!isset($i['field']) && !isset($i['data'])) {
					continue;
				}
				
				$field 	= $i['field'];
				$data 	= $i['data'];
				
				$this->db->where_not_in(
					$field,
					$data
				);
			}
		}

		if(!empty($or_where)){
			foreach ($or_where as $i) {
				if (!isset($i['field']) && !isset($i['data'])) {
					continue;
				}
				
				$field 	= $i['field'];
				$data 	= $i['data'];
				$this->db->or_where(
					$field,
					$data
				);
			}
		}

		if(!empty($or_where_in)){
			foreach ($or_where_in as $i) {
				if (!isset($i['field']) && !isset($i['data'])) {
					continue;
				}
				
				$field 	= $i['field'];
				$data 	= $i['data'];
				$this->db->or_where_in(
					$field,
					$data
				);
			}
		}

		if(!empty($or_where_not_in)){
			foreach ($or_where_not_in as $i) {
				if (!isset($i['field']) && !isset($i['data'])) {
					continue;
				}
				
				$field 	= $i['field'];
				$data 	= $i['data'];
				$this->db->or_where_not_in(
					$field,
					$data
				);
			}
		}

		if ($is_regroup) {
			$this->db->group_end();
		} 

		if(!empty($where)){
			$is_multi_where = false;
			foreach ($where as $i) {
				if (!isset($i['field']) && !isset($i['data'])) {
					continue;
				}
				
				$field 	= $i['field'];
				$data 	= $i['data'];
				
				$this->db->where(
					$field,
					$data
				);

				$is_multi_where = true;
			}

			if (!$is_multi_where) {
				$this->db->where($where);
			}
		}

		if(!empty($limit)){
			$this->db->limit(
				$limit, 
				$offset
			);
		}
		
		if(!empty($order_by)) {
			$filter_by 	= $order_by['filter_by'];
			$sort_by	= $order_by['sort_by'];

			$this->db->order_by(
				$filter_by,
				$sort_by
			);
		}

		$query = $this->db->get();

		return $query;
	}

	function _count(
		$inner_joints = array(), 
		$where = array(), 
		$where_in = array(), 
		$where_not_in = array(),
		$or_where = array(), 
		$or_where_in = array(),
		$or_where_not_in = array(),
		$limit = 0, 
		$offset = 0,
		$is_regroup = false
		) {

		$this->db->from( $this->_table );

		if (!empty($inner_joints)) {
			foreach($inner_joints as $join) {
				if (isset($join['type'])) {
					$this->db->join(
						$join['table_name'],
						$join['condition'],
						$join['type']
					);
				} else {
					$this->db->join(
						$join['table_name'],
						$join['condition']
					);
				}
			}
		}

		if ($is_regroup) {
			$this->db->group_start();
		} 

		if(!empty($where_in)){
			foreach ($where_in as $i) {
				if (!isset($i['field']) && !isset($i['data'])) {
					continue;
				}
				
				$field 	= $i['field'];
				$data 	= $i['data'];
				
				$this->db->where_in(
					$field,
					$data
				);
			}
		}

		if(!empty($where_not_in)){
			foreach ($where_not_in as $i) {
				if (!isset($i['field']) && !isset($i['data'])) {
					continue;
				}
				
				$field 	= $i['field'];
				$data 	= $i['data'];
				
				$this->db->where_not_in(
					$field,
					$data
				);
			}
		}

		if(!empty($or_where)){
			foreach ($or_where as $i) {
				if (!isset($i['field']) && !isset($i['data'])) {
					continue;
				}
				
				$field 	= $i['field'];
				$data 	= $i['data'];
				$this->db->or_where(
					$field,
					$data
				);
			}
		}

		if(!empty($or_where_in)){
			foreach ($or_where_in as $i) {
				if (!isset($i['field']) && !isset($i['data'])) {
					continue;
				}
				
				$field 	= $i['field'];
				$data 	= $i['data'];
				$this->db->or_where_in(
					$field,
					$data
				);
			}
		}

		if(!empty($or_where_not_in)){
			foreach ($or_where_not_in as $i) {
				if (!isset($i['field']) && !isset($i['data'])) {
					continue;
				}
				
				$field 	= $i['field'];
				$data 	= $i['data'];
				$this->db->or_where_not_in(
					$field,
					$data
				);
			}
		}

		if ($is_regroup) {
			$this->db->group_end();
		} 

		if(!empty($where)){
			$is_multi_where = false;
			foreach ($where as $i) {
				if (!isset($i['field']) && !isset($i['data'])) {
					continue;
				}
				
				$field 	= $i['field'];
				$data 	= $i['data'];
				
				$this->db->where(
					$field,
					$data
				);

				$is_multi_where = true;
			}

			if (!$is_multi_where) {
				$this->db->where($where);
			}
		}

		if(!empty($limit)){
			$this->db->limit(
				$limit, 
				$offset
			);
		}

		$query = $this->db->get();

		return $this->db->count_all_results();
	}

	function get_datum($id = '', $data = array(), $where_or = array(), $inner_joints = array(), $select = array()) {

		if (!empty($select)) {
			$this->db->select(ARRtoSTR($select));
		}

		$this->db->from($this->_table);
		if (!empty($inner_joints)) {
			foreach($inner_joints as $join) {
				if (isset($join['type'])) {
					$this->db->join(
						$join['table_name'],
						$join['condition'],
						$join['type']
					);
				} else {
					$this->db->join(
						$join['table_name'],
						$join['condition']
					);
				}
			}
		}

		if( !empty($data) ){
			$this->db->where( $data );
		}

		if( !empty($where_or) ){
			$this->db->or_where($where_or);
		}

		if( !empty($id) ){
			$this->db->where_not_in($this->_id, $id);
		}

		$query = $this->db->get();

		return $query;
	}

	function get_data( $select = array('*'), $data = array(), $or_where = array(), $inner_joints = array(), $order_by = array(), $offset = 0, $limit = 0, $group_by = '' ) {
		
		$this->db->select(ARRtoSTR($select),false);

		$this->db->from( $this->_table );

		if (!empty($inner_joints)) {
			foreach($inner_joints as $join) {
				if (isset($join['type'])) {
					$this->db->join(
						$join['table_name'],
						$join['condition'],
						$join['type']
					);
				} else {
					$this->db->join(
						$join['table_name'],
						$join['condition']
					);
				}
			}
		}

		if(!empty($data)){
			$this->db->where($data);
		}

		if(!empty( $or_where )){
		$this->db->or_where($or_where);
		}

		if(!empty($limit)){
			$this->db->limit($limit, $offset);
		}
		
		if( !empty( $order_by ) ) {
			$this->db->order_by( $order_by['filter'],$order_by['sort'] );
		}

		if( $group_by != '' ) {
			$this->db->group_by( $group_by );
		}

		$query = $this->db->get();

		$results = $query->result_array();

		return $results;

	}

	function get_data_where_in( $select = array('*'), $data = array(), $where_in = array(), $inner_joints = array(), $order_by = array(), $offset = 0, $limit = 0, $group_by = '' ) {
		
		$this->db->select(ARRtoSTR($select),false);

		$this->db->from( $this->_table );

		if (!empty($inner_joints)) {
			foreach($inner_joints as $join) {
				if (isset($join['type'])) {
					$this->db->join(
						$join['table_name'],
						$join['condition'],
						$join['type']
					);
				} else {
					$this->db->join(
						$join['table_name'],
						$join['condition']
					);
				}
			}
		}

		if(!empty($data)){
			$this->db->where($data);
		}

		if(!empty($where_in)){
			$this->db->where_in(
				$where_in['field'],
				$where_in['data']
			);
		}

		if(!empty($limit)){
			$this->db->limit($limit, $offset);
		}
		
		if( !empty( $order_by ) ) {
			$this->db->order_by( $order_by['filter'],$order_by['sort'] );
		}

		if( $group_by != '' ) {
			$this->db->group_by( $group_by );
		}

		$query = $this->db->get();

		$results = $query->result_array();

		return $results;

	}

	function get_count( $data = array(), $like = array(), $inner_joints = array(), $order_by = array(), $offset = 0, $count = 0 ) {
		if( !empty($data) ){
			
			$this->db->from($this->_table);

			if (!empty($inner_joints)) {
				foreach($inner_joints as $join) {
					if (isset($join['type'])) {
						$this->db->join(
							$join['table_name'],
							$join['condition'],
							$join['type']
						);
					} else {
						$this->db->join(
							$join['table_name'],
							$join['condition']
						);
					}
				}
			}

			if( !empty( $data ) ) {
				$this->db->where( $data );
			}

			if(!empty( $like )){
			$this->db->like( $like['field'], $like['value'] );
			}   

			if( !empty( $count ) ) {
				$this->db->limit( $count, $offset );
			}
			
			if( !empty( $order_by ) ) {
				$this->db->order_by( $order_by['filter'],$order_by['sort'] );
			}
								
			return $this->db->count_all_results();
		}else{
			return $this->db->count_all($this->_table_x);
		}
	}

	function insert( $data ) {
		$this->db->insert( $this->_table_x , $data);
		return $this->db->insert_id();
	}

	function update( $id = 0 , $data = array() ){

		if( !empty($data) && !empty($id) ){

			$this->db->where($this->_id, $id);
			$this->db->update( $this->_table_x, $data); 
			return $this->db->affected_rows();
		}else{
			return false;
		}
	} 

	public function delete($id){
		$this->db->where($this->_id, $id); 
		$this->db->delete($this->_table_x);
	}
}

