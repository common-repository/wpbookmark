<?php
/**
 * Description of class-binnash-wpbookmark
 *
 * @author nur
 */
require_once ('lang/eng.php');
require_once ('class-binnash-wpbookmark-config.php');
if (!class_exists('WPBookmark')) {
class WPBookmark {
    function WPBookmark(){
        $this->__construct();
    }
    
    function __construct() {
        register_activation_hook( WPBOOKMARK_DIR . '/' . 'wpbookmark.php', array(&$this,'activate') );        	
        register_deactivation_hook( WPBOOKMARK_DIR . '/' . 'wpbookmark.php', array(&$this,'deactivate') );
        add_action('init', array(&$this, 'loadLibrary'));
        add_action('admin_menu', array(&$this, 'adminMenu'));
        add_filter('the_content', array(&$this, 'bookmarkHandler'));
        add_action('wp_footer', array(&$this, 'addJsCode'));
        add_action('wp_ajax_binnash_bookmark', array(&$this, 'doBookmark'));
        add_action('wp_ajax_binnash_bookmark_list', array(&$this, 'doBookmarkList'));
        add_shortcode('binnash_bookmark_list', array(&$this, 'bookmarkListShorcodeHandler'));
    }
    function doBookmarkList(){        
        if(is_user_logged_in()){
            global $wpdb;
            $userInfo = wp_get_current_user();
            $conf = WPBookmarkConfig::getInstance();            
            $page = isset($_GET['page'])?$_GET['page']-1:0;
            if(isset($_GET['remove'])&&!empty($_GET['remove'])){
                $wpdb->query("DELETE FROM ".$conf->bookmarks_tbl .
                        " WHERE user_id = " . $userInfo->ID .
                        " AND post_id IN (" . 
                        strip_tags($_GET['remove']) . ")");
            }
            
            $limit =  isset($conf->bbookmark_items_per_page)?$conf->bbookmark_items_per_page:10;
            $start = $page*$limit;
            $query = "SELECT COUNT(" . $wpdb->posts . " .ID) FROM  " .
                    $wpdb->posts ." LEFT JOIN " . $conf->bookmarks_tbl . " ON (" . 
                    $wpdb->posts . " .ID = ". $conf->bookmarks_tbl . ".post_id".                    
                    ") WHERE " .$conf->bookmarks_tbl . ".user_id=" .$userInfo->ID;            
            $row_count = $wpdb->get_var($query);             
            $pages = ceil($row_count/$limit);                                    
            $query = "SELECT " . $wpdb->posts . " .ID, post_title,post_type,date FROM  " .
                    $wpdb->posts ." LEFT JOIN " . $conf->bookmarks_tbl . " ON (" . 
                    $wpdb->posts . " .ID = ". $conf->bookmarks_tbl . ".post_id".                    
                    ") WHERE " .$conf->bookmarks_tbl . ".user_id=" .$userInfo->ID .
                    " ORDER BY " . $wpdb->posts . " .post_title ASC LIMIT " . $start . ', ' . $limit;

            $result = $wpdb->get_results($query, OBJECT);
            ob_start();
            include_once('bookmark_list.php');
            $output = ob_get_contents();            
            ob_end_clean();             
            echo $output;
            exit();
        }
    }
    function bookmarkListShorcodeHandler($attrs, $contents, $codes){
        if(is_user_logged_in()){
            $ajaxurl = admin_url('admin-ajax.php');
            ob_start();
            include_once('bookmark_list_container.php');
            $output = ob_get_contents();
            ob_end_clean();
            return $output;
        }
        return BBOOKMARK_LOGIN_REQUEST;
    }
    function doBookmark(){
        if(is_user_logged_in()){
            global $wpdb;
            $conf = WPBookmarkConfig::getInstance();
            $userInfo = wp_get_current_user();
            if('add' == $_GET['op']){
                $result = $wpdb->insert( 
                    $conf->bookmarks_tbl, 
                    array( 
                        'user_id' => $userInfo->ID, 
                        'post_id' => $_GET['id'], 
                        'type' => $_GET['type'],
                        'date' => date('Y-m-d')
                    ), 
                    array( 
                        '%d', 
                        '%d',
                        '%s',
                        '%s'
                    ) 
                );        
                if($result){
                    $removeTitle = BBOOKMARK_REMOVE;
                    $a1 = '<span class="c"><b>&radic;</b></span><br/>
                                <span class="t">'.BBOOKMARK_BOOKMARK.'</span>
                           ';                
                    $a1 = array('title'=>$removeTitle,'op'=>'remove', 'text'=>$a1);
                    echo json_encode(array('status'=>1,'msg'=>$a1));
                }
                else
                    echo json_encode(array('status'=>0,'msg'=>$a1));
                exit(0);        
            }
            else if ("remove" == $_GET['op']){
                $query = 'DELETE FROM ' . $conf->bookmarks_tbl .
                         ' WHERE post_id =' . $_GET['id'] . ' AND '.
                         ' user_id = ' .$userInfo->ID . ' AND '.
                         ' type = \'post\'';
                $result = $wpdb->query($query);
                if($result){
                    $addTitle = BBOOKMARK_ADD_FAV;
                    $a1 = '<span class="c"><b>+</b></span><br/>
                           <span class="t">'.BBOOKMARK_BOOKMARK.'</span>';                  
                    $a1 = array('title'=>$addTitle,'op'=>'add', 'text'=>$a1);
                    echo json_encode(array('status'=>1,'msg'=>$a1));
                }
                else
                     echo json_encode(array('status'=>0,'msg'=>$a1));
                exit(0);
            }
        }
    }
    function addJsCode(){
        include_once ('bookmark_button_js.php');
    }
    function bookmarkHandler($content){
        global $post;
        if(is_category()) return $content;
        if(is_feed()) return $content;
        if(is_search()) return $content; 
        $conf = WPBookmarkConfig::getInstance();
        $disabled = isset($conf->disable_bookmarks)?$conf->disable_bookmarks:array();
        $disabled = isset($disabled['post'])?$disabled['post']:array();
        if(in_array($post->ID, $disabled)) return $content;         
        if(is_user_logged_in()){
            $userInfo = wp_get_current_user();
            global $wpdb;
            $query = 'SELECT * FROM ' . $conf->bookmarks_tbl .
                     ' WHERE user_id = ' . $userInfo->ID .
                     ' AND post_id = ' . $post->ID.
                     ' AND type = \'post\'';
            $results = $wpdb->get_results($query);            
            $addTitle = BBOOKMARK_ADD_FAV;
            $removeTitle = BBOOKMARK_REMOVE;
            $link  =  $post->ID;
            if(!empty($results)){
                $a1 = '<a title="' . $removeTitle . '" op="remove" target="_parent" href="'.$link.'" class="count">
                            <span class="c"><b>&radic;</b></span><br/>
                            <span class="t">'.BBOOKMARK_BOOKMARK.'</span>
                        </a>';                
            }
            else{
                $a1 = '<a title="' . $addTitle . '" op="add" target="_parent" href="'.$link.'" class="count">
                            <span class="c"><b>+</b></span><br/>
                            <span class="t">'.BBOOKMARK_BOOKMARK.'</span>
                        </a>';
            }   
        }
        else{
            $title = BBOOKMARK_LOGIN_TO_BOOKMARK;
            $link  = "";
            $a1 = '<div title="' . $addTitle . '"  class="count">
                        <span class="c"><b style="color:red;">x</b></span><br/>
                        <span class="t">'.BBOOKMARK_BOOKMARK.'</span>
                    </div>';
        }
        $button = '<div class="binnash-bookmark-button">
                       <div  class="binnashbookmarkbutton">
                       '.$a1.'				
                </div>
            </div>
            ';
        return $button . $content;        
    }
    
    function loadLibrary(){
        wp_enqueue_script('jquery');
        if(!is_admin()){
             wp_enqueue_style('wpbbookmark', WPBOOKMARK_URL . '/css/wpbbookmark.css'); 
             wp_enqueue_style('jquery.paginateN-1.0', WPBOOKMARK_URL . '/css/jquery.paginateN-1.0.css'); 
             wp_enqueue_script('jquery.paginateN-1.0', WPBOOKMARK_URL . '/js/jquery.paginateN-1.0.js');
        }
        if(is_admin()&&isset($_GET['page'])&&($_GET['page']=='wp_bookmark_manage')){                
            wp_enqueue_style('wpbbookmark-admin', WPBOOKMARK_URL . '/css/wpbbookmark-admin.css'); 
            wp_enqueue_style('jquery.paginateN-1.0', WPBOOKMARK_URL . '/css/jquery.paginateN-1.0.css'); 
            wp_enqueue_script('jquery.paginateN-1.0', WPBOOKMARK_URL . '/js/jquery.paginateN-1.0.js');
        }
    }
    
    function adminMenu(){
        add_options_page('WPBookmark', 'WPBookmark', 'manage_options', "wp_bookmark_manage",array(&$this,'wpBookmarkHook'));
        add_submenu_page(__FILE__, __("WPBookmarK", 'wp_bookmark'), __("WPBookmark", 'wp_boomark'), 'add_users', 'wp_bookmark_manage', array(&$this,'wpBookmarkHook'));        
    }
    
    function wpBookmarkHook(){
        $menuInfo = $this->drawMenu();
        $page_content = 'Content Not Found.';
        $menu = $menuInfo['menu'];
        switch ($menuInfo['current_menu_id']){
            case 'settings':
                $page_content = $this->settingsPage();
                break;
            default:
                $page_content = $this->managePage();
                break;
        }
        include_once ('bookmark_manage.php');
    }
    function managePage(){
        ob_start();
        include_once('manage_page.php');
        $content = ob_get_contents();
        ob_end_clean();			
        return $content;			                
    }
    function settingsPage(){        
        global $wpdb;
        
        $conf = WPBookmarkConfig::getInstance();
        if(isset($_POST['op_edit_settngs'])){
            unset($_POST['op_edit_settngs']);				
            $fields = $_POST;
            if(!isset($_POST['disable_bookmarks']))
                $_POST['disable_bookmarks']['post'] = array();
            $conf->updateConfig($_POST);
            $d = isset($_POST['disable_bookmarks']['post'])?
                $_POST['disable_bookmarks']['post']:array();
            if(!empty($d)){
                $idStrings = implode(',',$_POST['disable_bookmarks']['post']);
                $query = "DELETE FROM " . $conf->bookmarks_tbl . 
                        " WHERE post_id in (" . $idStrings . ') AND type= \'post\'';
                $wpdb->query($query);
            }
        }
        $fields['disable_bookmarks'] = $conf->disable_bookmarks;
        $fields['bbookmark_items_per_page'] =$conf->bbookmark_items_per_page; 
        $query  = "SELECT ID,post_title, post_type FROM $wpdb->posts ";
        $query .= " WHERE post_status = 'publish'";
        $all_posts = $wpdb->get_results($query,ARRAY_A);
        ob_start();
        include_once('settings_page.php');
        $content = ob_get_contents();
        ob_end_clean();			
        return $content;			        
    }
    function drawMenu(){
        $menuInfo = WPBookmarkConfig::menu();
        $menuIds = array_keys($menuInfo);
        $requestedMenu = isset($_GET['menu_id'])? $_GET['menu_id']: 'settings';        
        $currentMenu = in_array($requestedMenu, $menuIds)? $requestedMenu: 'settings';
        $menu = '<ul class="binnash-bookmark-submenu">';
        foreach($menuInfo as $key=>$value){
            $menu .= "<li ";			     
            if($currentMenu ==$key) $menu .= 'class="current"';
            $menu .= '><a href="'.$value['link'].'">'.$value['title'].'</a></li>';
        }   				
        $menu .='</ul>';
        return array('menu'=>$menu,'current_menu_id'=>$currentMenu);
    }
    
    function activate(){
        $conf = WPBookmarkConfig::getInstance();
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        $sql = "CREATE TABLE " . $conf->bookmarks_tbl . " (
        `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
        `post_id` int NOT NULL ,
        `user_id` int NOT NULL ,
        `date`   date NOT NULL ,
        `type`  varchar(20) NOT NULL DEFAULT 'post',
        unique index(`post_id`, `user_id`, `type`)
        ) AUTO_INCREMENT=1";
        dbDelta($sql);
        if(!isset($conf->disable_bookmarks))
            $conf->updateConfig(array(
                'disable_bookmarks'=>array(),
                'bbookmark_items_per_page'=>10
                ));

        $the_page = get_page_by_title($conf->bookmark_list_page);

        if (!$the_page){
            $_p = array();
            $_p['post_title']     = $conf->bookmark_list_page;
            $_p['post_content']   = "[binnash_bookmark_list].";
            $_p['post_status']    = 'publish';
            $_p['post_type']      = 'page';
            $_p['comment_status'] = 'closed';
            $_p['ping_status']    = 'closed';
            $_p['post_category'] = array(1);
            $this->page_id = wp_insert_post($_p);
        }
        else{
            $this->page_id = $the_page->ID;
            $the_page->post_status = 'publish';
            $this->page_id = wp_update_post($the_page);
        }        
    }
    
    function deactivate(){
        
    }
}
}
