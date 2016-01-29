<?php


	function createOrderLink($class, $order, $dir_request)
    {
    	
    	$dir = 'DESC';
    	if ($class == $order && $dir_request == 'DESC')
    		$dir = 'ASC';
    	
    	return "?order=" . $class . "&dir=" . $dir;
    	
    }
    
    function getColorDate($date) {
    	
    	$datetime1 = new DateTime($date);
    	$datetime2 = new DateTime('2016-01-29');
    	$difference = $datetime2->diff($datetime1);
    	$difference = $difference->format('%R%a');
    	 
    	$color = '';
    	
    	if ($difference <= 7)
    		$color = "btn-info";
    	if ($difference <= 3)
    		$color = 'btn-warning';
    	if ($difference < 0)
    		$color = 'btn-danger';
    	
    	return " btn " . $color;
    	
    }
    
    
?>