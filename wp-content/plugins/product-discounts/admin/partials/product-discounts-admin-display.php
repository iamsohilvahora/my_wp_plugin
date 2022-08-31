<?php
    // Extending class
    class ProductDiscountsTableList extends WP_List_Table{
        function __construct(){      
            //Set parent defaults
            parent::__construct(array(
                'singular'  => 'discount_name', 
            ));
        }
        // Bind table with columns, data and all
        public function prepare_items(){
            if(isset($_POST['page']) && isset($_POST['s'])){
                $this->items = $this->wp_list_table_data($_POST['s']);
            }
            else{
                $this->items = $this->wp_list_table_data();
            }

            $this->_column_headers = array( 
                 $this->get_columns(),       // columns
                 array(),           // hidden
                 $this->get_sortable_columns(),  // sortable
            );
            $this->process_bulk_action(); // action for delete data

            /* pagination */
            // $per_page = $this->get_items_per_page('items_per_page', 2);
            $per_page = $this->get_items_per_page('items_per_page', 3);
            $current_page = $this->get_pagenum();
            $total_items = count($this->items);
            $this->items = array_slice($this->items, (($current_page - 1) * $per_page), $per_page);

            $this->set_pagination_args(array(
                  'total_items' => $total_items, // total number of items
                  'per_page'    => $per_page // items to show on a page
            ));
            usort($this->items, array(&$this, 'usort_reorder'));
            $this->items = $this->items;
        }
        public function wp_list_table_data($search = ""){
            // If user is not admin
            if(!is_admin()){
                return;
            }
            // $current_page = admin_url("admin.php?page=".$_GET["page"]); // get current admin page url ?>
            <div class="wrap">
                <h1 class="wp-heading-inline">Product Discounts</h1>
                <a href="<?php echo admin_url('admin.php?page=product_discounts&action=add'); ?>" class="page-title-action" id="product-discounts-save">Add New</a>
                <hr class="wp-header-end">
            </div>

            <?php 
            if($_GET['page'] == "product_discounts"):
                global $wpdb;
                $table_name = $wpdb->prefix.'product_discounts';
                $perpage = 10;
                $curpage = isset($_GET['pagenum']) ? intval($_GET['pagenum']) : 1;
                $product_discounts = $wpdb->get_results("SELECT * FROM $table_name");

                $total = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
                $pages = ceil($total/$perpage);
                $limit = 10;
                $offset = $perpage*($curpage-1);
                if(!is_int($offset) || $offset < 0){ $offset = 0; }
                // query for get all product discount details
                if (!empty($search)) {
                    $product_discounts = $wpdb->get_results(
                            "SELECT * FROM $table_name WHERE 
                            ID Like '%{$search}%' OR 
                            discount_name Like '%{$search}%' OR 
                            discount_value Like '%{$search}%'"
                      );
                }
                else{
                    $product_discounts = $wpdb->get_results("SELECT * FROM $table_name ORDER BY id DESC LIMIT $limit OFFSET $offset");
                }

                $id = 1;
                if(!empty($product_discounts)):
                    $posts_array = array();
                    $id = (($perpage * ($curpage - 1)) + 1);
                    if(!is_int($id) || $id < 0){ $id = 1; }
                    foreach($product_discounts as $discount):
                        $table_id = $discount->id; 
                        $discount_name = $discount->discount_name; 
                        $discount_value = $discount->discount_value; 
                        // get admin page url
                        $admin_url = admin_url('admin.php?page=product_discounts&action=edit&edit_id='.$discount->id.'');
                        
                        $action = "<a href='".$admin_url."' class='btn btn-success mx-1 text-light edit-discount'>Edit</a><a href='javascript:void(0);'' class='btn btn-danger mx-1 text-light delete-discount' data-id='".$discount->id."'>Delete</a>";

                        $posts_array[] = array(
                                "id" => $table_id,
                                "sr" => $id++,
                                "discount_name" => $discount_name,
                                "discount_value" => $discount_value,
                                "action" => $action,
                                );
                    endforeach;
                    return $posts_array;                
                endif;
            endif;  
        }
        // Define table columns
        public function get_columns(){
            $columns = array(
                "cb" => '<input type="checkbox" />',
                "sr" => "SR",
                "discount_name" => "Discount Name",
                "discount_value" => "Discount Value",
                "action" => "Action",
            );
            return $columns;
        }
        // bind data with column
        public function column_default($item, $column_name){
            switch ($column_name){
                case 'sr': echo $item['sr']; break;
                case 'discount_name': echo $item['discount_name']; break;
                case 'discount_value': echo $item['discount_value']; break;
                case 'action': echo $item['action']; break;
                default:
                    return "no value";
            }
        }
        // Add sorting to columns
        public function get_sortable_columns(){
            $sortable_columns = array(
                'sr' => array('sr', true),
                'discount_name' => array('discount_name', true),
                'discount_value' => array('discount_value', true),
            );
            return $sortable_columns;
        }
        // Sorting function
        function usort_reorder($a, $b){
            // If no sort, default to sr
            $orderby = (!empty($_GET['orderby'])) ? $_GET['orderby'] : 'sr';
            // If no order, default to asc
            $order = (!empty($_GET['order'])) ? $_GET['order'] : 'asc';
            // Determine sort order
            $result = strcmp($a[$orderby], $b[$orderby]);
            // Send final sort direction to usort
            return ($order === 'asc') ? $result : -$result;
        }
        function get_bulk_actions(){
            $actions = array(
                'delete'    => 'Delete'
            );
            return $actions;
        }
        function process_bulk_action(){
            // Detect when a bulk action is being triggered...
            global $wpdb;
            $table_name = $wpdb->prefix."product_discounts";

            if('delete' === $this->current_action()){
                $ids = isset($_REQUEST['record']) ? $_REQUEST['record'] : array();
                if(is_array($ids)) $ids = implode(',', $ids);

                if(!empty($ids)){
                    $wpdb->query("DELETE FROM $table_name WHERE id IN($ids)");
                    $product_discount_page = admin_url("admin.php?page=product_discounts"); // get page url
                    // header('Location: '.$product_discount_page); // redirect url
                    wp_redirect($product_discount_page);
                }
            }
        }
        // column_column_name
        function column_discount_name($item){ 
            //Build row actions
            $actions = array(
                'edit'      => sprintf('<a href="?page=%s&action=%s&edit_id=%s">Edit</a>',$_REQUEST['page'],'edit',$item['id']),
                'delete'    => sprintf('<a href="javascript:void(0);" class="delete-discount" data-id="%s">Delete</a>',$item['id']),
            );
            
            // Return the title contents
            return sprintf('%1$s %2$s',
                /*$1%s*/ $item['discount_name'],
                /*$3%s*/ $this->row_actions($actions)
            );
        }
        // To show checkbox with each row
        function column_cb($item){
            return sprintf(
                    '<input type="checkbox" name="record[]" value="%d" />', $item['id']
                    );
        }
        // custom pagination
        public function show_pagination(){
                global $wpdb;
                $table_name = $wpdb->prefix.'product_discounts';
                $perpage = 10;
                $curpage = isset($_GET['pagenum']) ? intval($_GET['pagenum']) : 1;

                if($_GET['page'] == "product_discounts"){
                     // query for get total product
                    $total = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
                    $pages = ceil($total/$perpage);
                    $limit = 10;
                    $offset = $perpage*($curpage-1);
                    if(!is_int($offset) || $offset < 0){ $offset = 0; }
                    // query for get all product discount details
                    $product_discounts = $wpdb->get_results("SELECT discount_name, discount_value FROM $table_name ORDER BY id DESC LIMIT $limit OFFSET $offset");
                }

                if(!empty($product_discounts)):
                    // Display pagination 
                    $pagin = array();
                    if(isset($_GET['pagenum'])){
                        $pagenum = $_GET['pagenum'];
                    }
                    if(isset($_GET['page_list'])){
                        $page_list = $_GET['page_list'];
                    }
                    $link = "";
                    for($i = 1; $i <= $pages; $i++){
                        if(!empty($pagenum) && ($pagenum == $i) && !empty($page_list)):
                            $firstClass = ($page_list == 'first') ? 'active' : '';
                            $lastClass = ($page_list == 'last') ? 'active' : '';
                            $middleClass = ($page_list == 'middle') ? 'active' : '';
                        else:
                            $firstClass = '';
                            $lastClass = '';
                            $middleClass = '';  
                        endif;

                        $url = $_SERVER['REQUEST_URI'] . "&pagenum=$i";
                        if($i == 1):
                            $link .= "<li class='page-item $firstClass'><a class='page-link' href='$url&page_list=first'>First</a></li>";
                        endif;  
                        $link .= "<li class='page-item $middleClass'><a class='page-link' href='$url&page_list=middle'>$i</a></li>";
                        if($i == $pages):
                            $link .= "<li class='page-item $lastClass'><a class='page-link' href='$url&page_list=last'>Last</a></li>";  
                        endif;
                        if ($curpage != $i) $link = str_replace( '~', '', $link );
                    }
                    $pagin[] = $link;
                    if($pages > 1):
                        echo '<div id="post_pagination"><ul class="pagination justify-content-center">'. implode( '', $pagin ) .'</ul></div>';
                    endif;
                endif;
        }
    }
    function show_product_discounts_list_table(){
        // Creating an instance
        $product_discounts_table = new ProductDiscountsTableList();
        // Prepare table
        $product_discounts_table->prepare_items(); ?>
        <form method="post">
              <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
              <?php $product_discounts_table->search_box('search', 'search_id');
                // Display table
                $product_discounts_table->display(); ?>
        </form>
        <?php    
        // $product_discounts_table->show_pagination();
        // $product_discounts_table->get_sortable_columns();
        // $product_discounts_table->column_name();
    }
    show_product_discounts_list_table();