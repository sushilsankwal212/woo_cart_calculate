add_action( 'woocommerce_product_options_pricing', 'bbloomer_add_RRP_to_products' );      
  
function bbloomer_add_RRP_to_products() {          
    woocommerce_wp_text_input( array( 
        'id' => 'rrp', 
        'class' => 'short wc_input_price', 
        'label' => __( 'In person price', 'woocommerce' ) . ' (' . get_woocommerce_currency_symbol() . ')',
        'data_type' => 'price', 
    ));      
}
  
// -----------------------------------------
// 2. Save RRP field via custom field
  
add_action( 'save_post_product', 'bbloomer_save_RRP' );
  
function bbloomer_save_RRP( $product_id ) {
    global $typenow;
    if ( 'product' === $typenow ) {
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
        if ( isset( $_POST['rrp'] ) ) {
            update_post_meta( $product_id, 'rrp', $_POST['rrp'] );
        }
    }
}
  
//pass custom value 
add_filter('woocommerce_add_cart_item_data','wdm_add_item_data',1,10);
function wdm_add_item_data($cart_item_data, $product_id) {

    global $woocommerce;
    $new_value = array();
    if(isset($_POST['add-to-cart']))
    {
        if(isset($_POST['checked_price']))
        {   
            $checked_price = $_POST['checked_price'];
            if($checked_price=='in_person')
            {
                $new_value['in_person'] = true;
            }
            else{
                $new_value['in_person'] = false;
            }

        }
        else{
            $new_value['in_person'] = false;
        }
        
    }
    
    if(empty($cart_item_data)) {
        return $new_value;
    } else {
        return array_merge($cart_item_data, $new_value);
    }
}
//calculate totals in add to cart
add_action( 'woocommerce_before_calculate_totals', 'wk_update_price',10,2);
function wk_update_price( $cart_object) {
    $cart_items = $cart_object->cart_contents;
    if ( ! empty( $cart_items ) ) {   
        foreach ( $cart_items as $key => $value ) {
            $product = $value['data'];
          if($value['in_person'] == 'in_person')
          {
               $price = get_post_meta($product->get_id(), 'rrp', true);  
          }
          else{
               $price = $product->get_price();
          }  
              
            $value['data']->set_price( $price );
            
        }
    
    }
}
