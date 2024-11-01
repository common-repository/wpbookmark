<script type="text/javascript">
jQuery(document).ready(function($){
    $(".binnashbookmarkbutton").find("a").each(function(i){
            $(this).click(function(e){
                    e.preventDefault();
                      var id = $(this).attr("href");
                      var op = $(this).attr("op");
                      if(!id) return;
                      var $this = this;
                  $.get('<?php echo admin_url( "admin-ajax.php" ); ?>',{
                        action: "binnash_bookmark",
                        id: id,
                        'type':'post',
                        op: op
                     },
                     function(data){
                         if(data.status){
                             data = data.msg;
                             $($this).attr('op', data.op);
                             $($this).attr('title',data.title);
                             $($this).html(data.text);
                         }
                     },
                     "json"
                 );			
                    });
            });     
});    
</script>