<?php


	function createOrderLink($class, $order, $dir_request)
    {
    	
    	$dir = 'DESC';
    	if ($class == $order && $dir_request == 'DESC')
    		$dir = 'ASC';
    	
    	return "?order=" . $class . "&dir=" . $dir;
    	
    }
    
    
?>