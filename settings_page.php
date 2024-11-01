<h2 > Settings</h2>
<div id="message" class="updated below-h2"><?php echo $msg;?></div>
<form method="post">
    <div class="postbox">	               
    <h3> General</h3>
        <div class="inside bg">
        <table class="form-table">
            <tr>
                <td>Items/Page in Lists</td>
                <td><input type="text" name="bbookmark_items_per_page" value="<?php echo $fields['bbookmark_items_per_page']; ?>" />
                    <br/> Number of Items to be shown in on page in a multipage list. default is 10</td>
            </tr>
        </table>
        </div>
    </div>
    <div class="postbox">		    
        <h3 class="title"> Disable bookmark</h3>
        <p class="description" style="margin:10px;">Restrict bookmark on pages/posts.check pages/posts which you don't want to be bookmarked.</p>
        <div class="inside bg op_settings_left_pane">		   			
            <table  cellpadding="0" cellspacing="5" border="0" width="600px">	
                <tr valign="top">
                    <td align="left"><strong>Pages/Posts</strong></td>	
                    <td align="left"> <strong>Type</strong>
                    </td>			
                </tr>
                <tr valign="top">
                    <td align="left" colspan="2">
                        <div style="overflow-y:scroll; height:200px;">
                            <table width="100%">
                                <?php $count = 0;foreach ($all_posts as $post):?>
                                <tr valign="top" style="<?php echo ($count%2)? 'background: #CACACA;': '';?>" >
                                    <td align="left" >&nbsp; 
                                    <input type="checkbox" <?php echo (is_array($fields['disable_bookmarks']['post'])&&in_array($post['ID'], $fields['disable_bookmarks']['post']))?"checked='checked'":"";
                                    ?> name="disable_bookmarks[post][<?php echo $post['ID'];?>]" value="<?php echo $post['ID'];?>" id="" />
                                    <label for="" ><?php echo $post['post_title']?></label></td>	
                                    <td  align="left" ><?php echo $post['post_type'];?>
                                    </td>			
                                </tr>									
                                <?php $count++;endforeach;?>
                            </table>
                        </div>
                    </td>
                </tr>
            </table>
        </div>	
    </div>
    <p class="submit">
        <input type="submit" name="op_edit_settngs" value="Update &raquo;" /> 
    </p>   
</form> 
