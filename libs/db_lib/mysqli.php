<?php


if (!function_exists('mysqli_connect'))
    die('This PHP environment doesn\'t have Improved MySQL (mysqli) support built in.');
    
class SQL //MySQLi
{
    var $link_id;
    var $query_result;
    var $num_queries = 0;

    function connect($db_host, $db_username, $db_password, $db_name = NULL, $use_names = '', $pconnect = true, $newlink = false) {
        global $lang_global;

        if (strpos($db_host, ':') !== false) list($db_host, $db_port) = explode(':', $db_host);

        if (($db_host === 'pipe') || ($db_host === 'socket'))
            $this->link_id = @mysqli_connect('localhost', $db_username, $db_password, $db_name, null, $db_port);
        elseif (isset($db_port))
            $this->link_id = @mysqli_connect($db_host, $db_username, $db_password, $db_name, $db_port);
        else
            $this->link_id = @mysqli_connect($db_host, $db_username, $db_password, $db_name);

        if ($this->link_id){
            if (!empty($use_names)) $this->query("SET NAMES '$use_names'");
        } else die($lang_global['err_sql_conn_db']);
    }

    function db($db_name) {
        global $lang_global;
        if ($this->link_id){
            if (@mysqli_select_db($this->link_id, $db_name)) return $this->link_id;
                else die($lang_global['err_sql_open_db']." ('$db_name')");
        } else die($lang_global['err_sql_conn_db']);
    }

    function query($sql){
        $this->query_result = @mysqli_query($this->link_id, $sql);

        if ($this->query_result){
            ++$this->num_queries;
            return $this->query_result;
        } else return false;
    }

    function result($query_id = 0, $row = 0, $field = NULL){
        if ($query_id){
            if ($row) @mysqli_data_seek($query_id, $row);
            if ($field !== NULL) return (@mysqli_fetch_assoc($query_id))[$field];
            $cur_row = @mysqli_fetch_row($query_id);
            return $cur_row[0];
        } else return false;
    }
	
	function data_seek($query_id = 0, $row = 0){
		return ($query_id) ? @mysqli_data_seek($query_id, $row) : false;
	}

    function fetch_row($query_id = 0){
        return ($query_id) ? @mysqli_fetch_row($query_id) : false;
    }
    
    function fetch_array($query_id = 0){
        return ($query_id) ? @mysqli_fetch_array($query_id, MYSQLI_BOTH) : false;
    }
    
    function fetch_assoc($query_id = 0){
        return ($query_id) ? @mysqli_fetch_assoc($query_id) : false;
    }

    function num_rows($query_id = 0){
        return ($query_id) ? @mysqli_num_rows($query_id) : false;
    }

    function num_fields($query_id = 0){
        return ($query_id) ? @mysqli_num_fields($query_id) : false;
    }
    
    function affected_rows(){
        return ($this->link_id) ? @mysqli_affected_rows($this->link_id) : false;
    }

    function insert_id(){
        return ($this->link_id) ? @mysqli_insert_id($this->link_id) : false;
    }

    function get_num_queries(){
        return $this->num_queries;
    }
    
    function free_result($query_id = false){
        return ($query_id) ? @mysqli_free_result($query_id) : false;
    }

    function field_type($query_id = 0,$field_offset){
        return false; //TODO
    }

    function field_name($query_id = 0,$field_offset){
        return false; //TODO
    }

    function quote_smart($value){
    if( is_array($value) ) {
        return array_map( array('SQL','quote_smart') , $value);
    } else {
        if( get_magic_quotes_gpc() ) $value = stripslashes($value);
        if( $value === '' ) $value = NULL;
        return mysqli_real_escape_string($this->link_id, $value);
        }
    }

    function error(){
        return mysqli_error($this->link_id);
    }

    function close(){
        global $tot_queries;
        $tot_queries += $this->num_queries;
        if ($this->link_id){
            if ($this->query_result) @mysqli_free_result($this->query_result);
            return @mysqli_close($this->link_id);
        } else return false;
    }
    
    function start_transaction(){
        return;
    }

    function end_transaction(){
        return;
    }
}
?>