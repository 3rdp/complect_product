<?php
defined('_JEXEC') or die('Restricted access');

class plgJshoppingProductsComplect_product extends JPlugin
{
	function __construct(&$subject, $config){
		parent::__construct($subject, $config);
        JSFactory::loadExtLanguageFile("addon_complect_product");
    }
    
    function onBeforeDisplayProductView(&$view){  
        $main_product = $view->product;
        $main_product_image = $view->images;   
        $db = JFactory::getDBO();    
        $jshopConfig = JSFactory::getConfig(); 
        $product = JTable::getInstance('product', 'jshop');   
        $adv_query = ""; $adv_from = ""; $adv_result = $product->getBuildQueryListProductDefaultResult();        
        $order_query = "";    

        $query = "SELECT $adv_result , complect.complect_type, complect.complect_value FROM `#__jshopping_products_complect` AS complect
            INNER JOIN `#__jshopping_products` AS prod ON complect.product_id_complect = prod.product_id
            LEFT JOIN `#__jshopping_products_to_categories` AS pr_cat ON pr_cat.product_id = complect.product_id_complect
            LEFT JOIN `#__jshopping_categories` AS cat ON pr_cat.category_id = cat.category_id
            $adv_from
            WHERE complect.product_id = '" . $view->product->product_id . "' AND cat.category_publish='1' AND prod.product_publish = '1' ".$adv_query." group by prod.product_id ".$order_query;
        $db->setQuery($query);        
        $product_complect = $db->loadObjectList();  
        $product_complect = listProductUpdateData($product_complect, 1);   

        $query = "SELECT $adv_result  FROM `#__jshopping_products` AS prod
            LEFT JOIN `#__jshopping_products_to_categories` AS pr_cat ON pr_cat.product_id = prod.product_id
            LEFT JOIN `#__jshopping_categories` AS cat ON pr_cat.category_id = cat.category_id
            $adv_from
            WHERE prod.product_id = '" . $view->product->product_id . "' AND cat.category_publish='1' AND prod.product_publish = '1' ".$adv_query." group by prod.product_id ";
        $db->setQuery($query);        
        $main_product = $db->loadObjectList();      

        $dump_main_product = 0;
        if ($dump_main_product) var_dump($main_product);
        $main_product[0]->product_price = '21000.0000';
        $main_product = listProductUpdateData($main_product, 1); 
        if ($dump_main_product) var_dump($main_product);
        // product_price_wp
        // product_price
        //
        // min_price
        $main_product =  $main_product[0]; 
         
        foreach ($product_complect as $key => $value) {
            $product_complect[$key]->product_old_price = $product_complect[$key]->product_price;
            if ($product_complect[$key]->complect_type == 1) $product_complect[$key]->product_price = $product_complect[$key]->product_price - $product_complect[$key]->complect_value; 
            else  $product_complect[$key]->product_price = $product_complect[$key]->product_price - $product_complect[$key]->product_price * $product_complect[$key]->complect_value / 100;  

            $product_complect[$key]->product_complect_price =   $product_complect[$key]->product_price + $main_product->product_price;

            $product_complect[$key]->product_link = SEFLink('index.php?option=com_jshopping&controller=product&task=view&category_id='.$value->category_id.'&product_id='.$value->product_id);
            $product_complect[$key]->buy_link = SEFLink('index.php?option=com_jshopping&controller=cart_complect&task=add&category_id='.$main_product->category_id.'&product_id='.$main_product->product_id.'&complect_product_id='.$value->product_id);        
        }       
        $in_row = 1;
        if (count($product_complect))
        {
            ob_start(); 
            ?>
            <div class="complect"> 
            <div class="related_header"><?php echo _JSHOP_PRODUCT_COMPLECT;?>: <?php echo $view->product->name?> </div>   
            <div class="jshop_list_product">
            <div class = "jshop"> 

            <?php
            foreach($product_complect as $k=>$product){
                if ($k%$in_row==0) echo '<div>';
                echo '<div width="'. 100/$in_row.'%" class="jshop_relatedd"> ';

                $product->template_block_product = "complect_product.php";
                include(JPATH_ROOT."/components/com_jshopping/templates/default/product/../".$view->folder_list_products."/".$product->template_block_product);

                //
                echo'</div>'; 
                if ($k%$in_row==$in_row-1) echo '</div>';
            }

            if ($k%$in_row!=$in_row-1) echo '</div>'; 
            echo '</div>'; 
            echo '</div>'; 
            echo '</div>';  

            $view->_tmp_product_html_end = ob_get_contents(); 
            ob_end_clean();
        }       
    }   
}
