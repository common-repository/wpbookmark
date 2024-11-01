<form id="binnash-bookmark-list-form">
    <table id="binnash-bookmark-list" class="widefat wp-list-table">
        <thead>
            <tr>
                <th>ID</th>
                <th colspan="3">Title</th>
                <th>Date</th>
                <th>Type</th>            
                <th><a href="#list" id="binnash-bookmark-list-remove">remove</a></th>
            </tr>
        </thead>
        <tbody></tbody>
        <tfoot></tfoot>
    </table>
</form>  
<script type="text/javascript">
    jQuery(document).ready(function ($){
        var loading = '<tr class="loading"><td colspan="4"></td></tr>';
        var paginator = $('#binnash-bookmark-list').paginateN({
            url:'<?php echo $ajaxurl;?>',
            api:true,
            loading: loading, 
            params:{'action':'binnash_bookmark_list'},
            paging_container:'binnash-bookmark-page-link'
        });        
        $('#binnash-bookmark-list-remove').click(function(){
            var elems = $('#binnash-bookmark-list-form').serializeArray();
            var selected = new Array();
            for (var i in elems)selected.push(elems[i]["value"]);
            //selected = selected.join(',');
            var origConfig = paginator.config;
            paginator.updateConfig({params:{
                    'remove':selected.join(','),
                    'action':'binnash_bookmark_list'
                }
            });
            paginator.load();
            paginator.config = origConfig;
        });
    });
</script>