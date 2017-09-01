
<?php 

switch ($layout){
	case "layout_default":
        include('default_layout_default.tpl');
        break;
		
    case "layout_home2":
        include('default_layout_home2.tpl');
        break;
		
	case "layout_home4":
       include('default_layout_home4.tpl');
        break;
		
	case "layout_home5":
        include('default_layout_home5.tpl');
        break;
		
	case "layout_popup":
		include('default_layout_popup.tpl');
		break;
		
    default:
        include('default_layout_default.tpl');
}
	

?>