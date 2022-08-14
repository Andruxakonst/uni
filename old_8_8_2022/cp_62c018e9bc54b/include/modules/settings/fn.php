<?php
function outCategoryOptions($id_parent = 0, $level = 0, $getCategories = []) {
    
    if (isset($getCategories["category_board_id_parent"][$id_parent])) {
        foreach ($getCategories["category_board_id_parent"][$id_parent] as $value) {

            $selected = "";

            if($cat_ids){
                if( in_array($value["category_board_id"], $cat_ids) ){
                    $selected = 'selected=""';
                }
            }

            while ($x++<$level) $retreat .= "-";

            echo '<option '.$selected.' value="' . $value["category_board_id"] . '" >'.$retreat.$value["category_board_name"].'</option>';

            $level++;
            
            outCategoryOptions($value["category_board_id"], $level, $getCategories);
            
            $level--;
            
        }
    }
}

?>