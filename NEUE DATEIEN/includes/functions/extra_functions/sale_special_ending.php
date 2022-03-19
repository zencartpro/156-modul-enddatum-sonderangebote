<?php 
// Sale Special Ending - Copyright That Software Guy, Inc. 2010.
// http://www.thatsoftwareguy.com
// Some portions copyright 2003-2019 Zen Cart Development Team
// Some portions copyright 2003 osCommerce
function sale_special_ending($id, $longdate = true, $one_day_back = false) { 
   $id = (int)$id; 
   global $db; 
 
   if (zen_get_products_special_price($id, true)) {
      $specials = $db->Execute("select expires_date from " . TABLE_SPECIALS . " where products_id = '" . $id . "' and status='1' AND expires_date !='0001-01-01'");
      if (!$specials->EOF) { 
         if (!$one_day_back) { 
            $enddate = $specials->fields['expires_date']; 
         } else { 
            $enddate = $specials->fields['expires_date']; 
            $enddate_ts = strtotime("-1 day ".$enddate);
            $enddate = date("Y-m-d H:i:s", $enddate_ts);
         }
         if ($longdate) {
            $date_str = zen_date_long($enddate);
         } else { 
            $date_str = zen_date_short($enddate);
         }
         return TEXT_SPECIAL_EXPIRES . $date_str; 
      }
   } 
   if (zen_get_products_special_price($id, false)) { 
      $product_price = zen_get_products_base_price($id);
      $product_to_categories = $db->Execute("select master_categories_id from " . TABLE_PRODUCTS . " where products_id = '" . $id . "'");
      $category = $product_to_categories->fields['master_categories_id'];
      $sales = $db->Execute("select sale_date_end from " . TABLE_SALEMAKER_SALES . " where sale_categories_all like '%," . $category . ",%' and sale_status = '1' and (sale_date_start <= now() or sale_date_start = '0001-01-01') and (sale_date_end >= now() or sale_date_end = '0001-01-01') and (sale_pricerange_from <= '" . $product_price . "' or sale_pricerange_from = '0') and (sale_pricerange_to >= '" . $product_price . "' or sale_pricerange_to = '0')");
      if ($sales->EOF) return ''; 
      if ($sales->fields['sale_date_end'] == '0001-01-01') return ''; 
      if (!$one_day_back) { 
         $enddate = $sales->fields['sale_date_end']; 
      } else { 
         $enddate = $sales->fields['sale_date_end']; 
         $enddate_ts = strtotime("-1 day ".$enddate);
         $enddate = date("Y-m-d H:i:s", $enddate_ts);
      }
      if ($longdate) {
         $date_str = zen_date_long($enddate);
      } else { 
         $date_str = zen_date_short($enddate);
      }
      return TEXT_SALE_EXPIRES . $date_str; 
   }
   return ''; 
}
