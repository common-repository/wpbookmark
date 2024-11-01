/**
 * PaginateN plugin 1.0
 *
 * Copyright (c) 2010 Nur Hasan 
 * Dual licensed under the MIT (MIT-LICENSE.txt)
 * and GPL (GPL-LICENSE.txt) licenses.
 */

/**
 * For comments, suggestions or bug reporting,
 * email me at: nur858ATgmailDOTcom
 */

(function($) {
	$.fn.paginateN = function(config){
		this.each(function(){						
			el = new $paginateN(this, config);
		});
		return config.api?el:this;
	};
	var defaults = {
			url: '#',
			loading: '<tr class="loading"><td colspan="0"></td></tr>',
			method: 'get',
			api:false,
			params: {},
                        paging_container: 'page-link'
	};
	$.paginateN = function(element, config){
	    this.config = $.extend({}, defaults, config || {});
	    this.target = element;
		this.load();
	};
	
    $paginateN    = $.paginateN;
    $paginateN.fn = $paginateN.prototype = {};
    $paginateN.fn.extend = $paginateN.extend = $.extend;
    $paginateN.fn.extend({
        updateConfig:function(config){
	    this.config = $.extend({}, this.config, config || {});            
        },
    	adjustTbody:function(){
    	     var height = $('tbody', this.target).height();
    	     if(height<50) height= 50;
    	     $('tbody', this.target).height(height + 'px');
        },
    	load:function(url){
        	this.adjustTbody();
        	url = url?url:this.config.url;
    	    $('tbody', this.target).html(this.config.loading);
    	    var $this = this;
    	    $.get(url, this.config.params,function(data){
    	    	data = $(data);                
                $('tbody', $this.target).html(data.filter('tbody').html());    	 
                $('tfoot', $this.target).html(data.filter('tfoot').html());
                window.eval(data.filter('script').html());
                $('tfoot div.'+$this.config.paging_container+' a', $this.target).click(function(e){
                    $this.config.params['page']= $(this).attr('href');
                    $this.load();
                    e.preventDefault();
                });    	    
    	    });
        }
    });
	
})(jQuery);