<tbody>
    <?php $i=0; foreach ($result as $row){?>
    <tr valign="top" <?php echo ($i%2)?'class="alternate"':''?>>
        <td><?php echo $row->ID;?></td>
        <td colspan="3"><a target="_blank" href="<?php echo get_permalink($row->ID);?>"><?php echo $row->post_title;?></a></td>
        <td><?php echo $row->date;?></td>
        <td><?php echo $row->post_type;?></td>        
        <td><input type="checkbox" value="<?php echo $row->ID;?>" name="selected[<?php echo $row->ID;?>]"></td>
    </tr>
    <?php $i++;}?>
</tbody>
<tfoot>
    <tr>
        <th  scope="col"></th>
        <th class="manage-column" colspan="4" scope="col">
        <div class="binnash-bookmark-page-link">&lt;&lt;
        <?php
        if($pages>0){
             for($i = 1;$i<=$pages;$i++){
                    if($page ==$i){
                ?>
                <em><?php echo $i;?></em>
                <?php 
                    }
            else{?>
                <em><a href="<?php echo $i;?>"><?php echo $i;?></a></em>
                <?php
               } 
           }
        }
        else{
        ?>        
        No Data Found
        <?php }?>
        &gt;&gt;</div>
        </th>  
        <th></th>
        <th></th>
    </tr>
    </tfoot>