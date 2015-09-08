<?php
require_once('../../../wp-load.php');
global $wpdb;
$body = @file_get_contents('php://input'); 
if ( !empty( $body ) ) {
	$event_json = json_decode($body);
	
	if($event_json->{'api_version'} != '1.0'){
	    $id = $event_json->{'id'};
	    $product_id = $event_json->order_info->{'order_id'};
    } else {
    	$id = $event_json->data->object->{'id'};
    	$product_id = $event_json->data->object->payment_details->{'product_id'};
    }
	
	$status = $event_json->{'type'};
	if ( $status == 'charge.pending' ) {
		$status = 'pending';
	} elseif ( $status == 'charge.success' ) {
		$status = 'processing';
	}elseif ( $status == 'charge.declined' ) {
		$status = 'refunded';
	}elseif ( $status == 'charge.deleted' ) {
		$status = 'refunded';
	}
	/*else {die();}
	Se necesita un escape para estados desconocidos para evitar que woocommerce oculte los pedidos con estos estados.
*/
	
	compropago_status_function( $product_id, $status );

	echo json_encode( $event_json );
}
?>
