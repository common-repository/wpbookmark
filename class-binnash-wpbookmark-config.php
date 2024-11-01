<?php
/**
 * Description of class-binnash-config
 *
 * @author nur
 */
class WPBookmarkConfig {
    static $_this;
    function WPBookmarkConfig(){
        $this->__construct();
    }
    function __construct(){
        global $wpdb;
        $this->bookmarks_tbl = $wpdb->prefix . 'wpbinnashbookmarks';
        $this->bookmark_list_page = BBOOKMARK_LIST_TITLE;
        $this->options = get_option('wpbinnashbookmarks_options');
        if(!empty($this->options))
        foreach($this->options as $key=>$value)$this->$key = $value;
    }
    static function getInstance(){
        if(null === self::$_this){
            self::$_this = new WPBookmarkConfig();        
        }        
        return self::$_this;
    }
    function updateConfig($options){
        foreach ($options as $key => $value) {
            $this->options[$key] = $value;
            $this->$key = $value;
        }
        update_option('wpbinnashbookmarks_options', $this->options);
    }
    static function menu(){
        $urlPrefix = 'admin.php?page=wp_bookmark_manage&menu_id=';
        return array(
            /*'manage'=>array('title'=>'Manage',
                            'link'=>$urlPrefix.'manage'
                           ),*/
            'settings'=>array('title'=>'Settings',
                              'link'=>$urlPrefix.'settings')
        );
    }
}