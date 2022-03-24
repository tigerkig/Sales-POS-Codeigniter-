<?php $this->load->view("partial/header"); ?> 
<div id="sales_page_holder">
 <div id="sale-grid-big-wrapper" class="clearfix register">
	<div class="row">
		<div class="clearfix" id="category_item_selection_wrapper">
  <div class="">
		<div class="spinner" id="grid-loader" style="display:none">
		  <div class="rect1"></div>
		  <div class="rect2"></div>
		  <div class="rect3"></div>
		</div>

  		<div class="text-center">
	  		<div id="grid_selection" class="btn-group" role="group">
				<a href="javascript:void(0);" class="<?php echo $this->config->item('default_type_for_grid') == 'categories' || !$this->config->item('default_type_for_grid') ? 'btn active' : '';?> btn btn-grid" id="by_category"><?php echo lang('reports_categories')?></a>
				<a href="javascript:void(0);" class="<?php echo $this->config->item('default_type_for_grid') == 'tags' ? 'btn active' : '';?> btn btn-grid" id="by_tag"><?php echo lang('common_tags')?></a>
			</div>
		</div>

    	<div id="grid_breadcrumbs"></div>
		<div id="category_item_selection" class="row register-grid"></div>
		<div class="pagination hidden-print alternate text-center"></div>
	</div>
</div>
	</div>
</div>

<div id="register_container" class="sales clearfix">
  <?php $this->load->view("sales/register"); ?>
</div>
 
</div>

<script type="text/javascript">
$(document).ready(function()
{
	<?php if ($this->config->item('require_employee_login_before_each_sale') && isset($dont_switch_employee) && !$dont_switch_employee) { ?>
		$('#switch_user').trigger('click');
	<?php } ?>
			
	$(window).load(function()
	{
		setTimeout(function()
		{
		<?php if ($fullscreen) { ?>
			$('.fullscreen').click();
		<?php }
		else {
		?>
		$('.dismissfullscreen').click();	
		<?php
		} ?>
		
		}, 0);
	});	
	
	<?php if($this->config->item('virtual_keyboard') != 'desktop') { ?>
	<?php if ($this->config->item('always_show_item_grid')) { ?>
		$(".show-grid").click();
	<?php } ?>
	<?php } ?>
 	var current_category_id = null;
	var current_tag_id = null;
	
  var categories_stack = [{category_id: 0, name: <?php echo json_encode(lang('common_all')); ?>}];
  
  function updateBreadcrumbs()
  {
     var breadcrumbs = '';
     for(var k = 0; k< categories_stack.length;k++)
     {
       var category_name = categories_stack[k].name;
       var category_id = categories_stack[k].category_id;
   
       breadcrumbs += (k != 0 ? ' &raquo ' : '' )+'<a href="javascript:void(0);"class="category_breadcrumb_item" data-category_id = "'+category_id+'">'+category_name+"</a>";
     }
 
     $("#grid_breadcrumbs").html(breadcrumbs);		  
  }
  
  $(document).on('click', ".category_breadcrumb_item",function()
  {
      var clicked_category_id = $(this).data('category_id');      
      var categories_size = categories_stack.length;
      current_category_id = clicked_category_id;
      
      for(var k = 0; k< categories_size; k++)
      {
        var current_category = categories_stack[k]
        var category_id = current_category.category_id;
        
        if (category_id == clicked_category_id)
        {
          if (categories_stack[k+1] != undefined)
          {
            categories_stack.splice(k+1,categories_size - k - 1);
          }
          break;
        }
      }
      
      if (current_category_id != 0)
      {
        loadCategoriesAndItems(current_category_id,0);
      }
      else
      {
        loadTopCategories();
      }
  });
  
	function loadTopCategories()
	{    
		$('#grid-loader').show();
		$.get('<?php echo site_url("sales/categories");?>', function(json)
		{
			processCategoriesResult(json);
		}, 'json');	
	}
	
	function loadTags()
	{
		$('#grid-loader').show();
		$.get('<?php echo site_url("sales/tags");?>', function(json)
		{
			processTagsResult(json);
		}, 'json');	
	}
  
  function loadCategoriesAndItems(category_id, offset)
  {
    $('#grid-loader').show();
    current_category_id = category_id;
    //Get sub categories then items
    $.get('<?php echo site_url("sales/categories_and_items/");?>/'+current_category_id+'/'+offset, function(json)
    {
        processCategoriesAndItemsResult(json);
    }, "json");
  }
  
  function loadTagItems(tag_id, offset)
  {
     $('#grid-loader').show();
	  current_tag_id = tag_id;
     //Get sub categories then items
     $.get('<?php echo site_url("sales/tag_items/");?>/'+tag_id+'/'+offset, function(json)
     {
         processTagItemsResult(json);
     }, "json");
  }

	$(document).on('click', ".pagination.categories a", function(event)
	{
		$('#grid-loader').show();
		event.preventDefault();
		var offset = !is_int($(this).attr('href').substring($(this).attr('href').lastIndexOf('/')+1)) ? 0 : $(this).attr('href').substring($(this).attr('href').lastIndexOf('/') + 1);
	
		$.get('<?php echo site_url("sales/categories/0");?>/'+offset, function(json)
		{
			processCategoriesResult(json);
      
		}, "json");
	});
	
	$(document).on('click', ".pagination.tags a", function(event)
	{
		$('#grid-loader').show();
		event.preventDefault();
		var offset = !is_int($(this).attr('href').substring($(this).attr('href').lastIndexOf('/')+1)) ? 0 : $(this).attr('href').substring($(this).attr('href').lastIndexOf('/') + 1);
	
		$.get('<?php echo site_url("sales/tags");?>/'+offset, function(json)
		{
			processTagsResult(json);
      
		}, "json");
	});
	
  
	$(document).on('click', ".pagination.categoriesAndItems a", function(event)
	{
		$('#grid-loader').show();
		event.preventDefault();
		var offset = !is_int($(this).attr('href').substring($(this).attr('href').lastIndexOf('/')+1)) ? 0 : $(this).attr('href').substring($(this).attr('href').lastIndexOf('/') + 1);
	  	loadCategoriesAndItems(current_category_id, offset);
	 });
	 
 	$(document).on('click', ".pagination.items a", function(event)
 	{
 		$('#grid-loader').show();
 		event.preventDefault();
 		var offset = !is_int($(this).attr('href').substring($(this).attr('href').lastIndexOf('/')+1)) ? 0 : $(this).attr('href').substring($(this).attr('href').lastIndexOf('/') + 1);
 	  	loadTagItems(current_tag_id, offset);
 	 });

	$('#category_item_selection_wrapper').on('click','.category_item.category', function(event)
	{
      event.preventDefault();
      current_category_id = $(this).data('category_id');
      var category_obj = {category_id: current_category_id, name: $(this).find('p').text()};
      categories_stack.push(category_obj);
      loadCategoriesAndItems($(this).data('category_id'), 0);
	});
	
	$('#category_item_selection_wrapper').on('click','.category_item.tag', function(event)
	{
      event.preventDefault();
		current_tag_id = $(this).data('tag_id');
      loadTagItems($(this).data('tag_id'), 0);
	});
	
	$('#category_item_selection_wrapper').on('click','#by_category', function(event)
	{
	 	current_category_id = null;
		current_tag_id = null;
		$("#grid_breadcrumbs").html('');
		$('.btn-grid').removeClass('active');
		$(this).addClass('active');
		categories_stack = [{category_id: 0, name: <?php echo json_encode(lang('common_all')); ?>}];
		loadTopCategories();
	});
	
	$('#category_item_selection_wrapper').on('click','#by_tag', function(event)
	{
	 	current_category_id = null;
		current_tag_id = null;
		$('.btn-grid').removeClass('active');
		$(this).addClass('active');
		$("#grid_breadcrumbs").html('');
		loadTags();
	});
	

	$('#category_item_selection_wrapper').on('click','.category_item.item', function(event)
	{		
		$('#grid-loader').show();
		event.preventDefault();
		$.post('<?php echo site_url("sales/add");?>', {item: $(this).data('id') }, function(response)
		{
			<?php
			if (!$this->config->item('disable_sale_notifications')) 
			{
				echo "show_feedback('success', ".json_encode(lang('common_successful_adding')).", ".json_encode(lang('common_success')).");";
			}
			
			?>
			$('#grid-loader').hide();
			$("#register_container").html(response);
			$('.show-grid').addClass('hidden');
			$('.hide-grid').removeClass('hidden');
		});
	});

	$("#category_item_selection_wrapper").on('click', '#back_to_categories', function(event)
	{ 
		$('#grid-loader').show();
		event.preventDefault();
    
    //Remove element from stack
    categories_stack.pop();
    
    //Get current last element
    var back_category = categories_stack[categories_stack.length - 1];
    
    if (back_category.category_id != 0)
    {
      loadCategoriesAndItems(back_category.category_id,0);
    }
    else 
    {
      loadTopCategories();
    }
  });
  
	$("#category_item_selection_wrapper").on('click', '#back_to_tags', function(event)
	{ 
		$('#grid-loader').show();
		event.preventDefault();
	   loadTags();
	});
  

	function processCategoriesResult(json)
	{    
		$("#category_item_selection_wrapper .pagination").removeClass('categoriesAndItems').removeClass('tags').removeClass('items').addClass('categories');
		$("#category_item_selection_wrapper .pagination").html(json.pagination);
	
		$("#category_item_selection").html('');
	
		for(var k=0;k<json.categories.length;k++)
		{
			 var category_item = $("<div/>").attr('class', 'category_item category col-md-2 register-holder categories-holder col-sm-3 col-xs-6').css('background-color', json.categories[k].color).data('category_id',json.categories[k].id).append('<p> <i class="ion-ios-folder-outline"></i> '+json.categories[k].name+'</p>');
			 
			 if(json.categories[k].image_id)
			 {
				 category_item.css('background-color', 'white');
				 category_item.css('background-image', 'url(' + SITE_URL +'/app_files/view/' + json.categories[k].image_id +'?timestamp='+json.categories[k].image_timestamp+')');
			 }
			 
			$("#category_item_selection").append(category_item);
		}
    
    	updateBreadcrumbs();
		$('#grid-loader').hide();
	}
	
	function processTagsResult(json)
	{		
		$("#category_item_selection_wrapper .pagination").removeClass('categoriesAndItems').removeClass('categories').removeClass('items').addClass('tags');	
		$("#category_item_selection_wrapper .pagination").html(json.pagination);
	
		$("#category_item_selection").html('');
	
		for(var k=0;k<json.tags.length;k++)
		{
			 var tag_item = $("<div/>").attr('class', 'category_item tag col-md-2 register-holder tags-holder col-sm-3 col-xs-6').data('tag_id',json.tags[k].id).append('<p> <i class="ion-ios-pricetag-outline"></i> '+json.tags[k].name+'</p>');
			$("#category_item_selection").append(tag_item);
		}
    
		$('#grid-loader').hide();
	}
  
  function processCategoriesAndItemsResult(json)
  {	  
	 $("#category_item_selection").html('');
    var back_to_categories_button = $("<div/>").attr('id', 'back_to_categories').attr('class', 'category_item register-holder no-image back-to-categories col-md-2 col-sm-3 col-xs-6 ').append('<p>&laquo; '+<?php echo json_encode(lang('common_back_to_categories')); ?>+'</p>');
    $("#category_item_selection").append(back_to_categories_button);

    for(var k=0;k<json.categories_and_items.length;k++)
    {
		 if (json.categories_and_items[k].type == 'category')
		 {
       	var category_item = $("<div/>").attr('class', 'category_item category col-md-2 register-holder categories-holder col-sm-3 col-xs-6').css('background-color', json.categories_and_items[k].color).css('background-image', 'url(' + SITE_URL +'/app_files/view/'+ json.categories_and_items[k].image_id +'?timestamp='+json.categories_and_items[k].image_timestamp+')').data('category_id',json.categories_and_items[k].id).append('<p> <i class="ion-ios-folder-outline"></i> '+json.categories_and_items[k].name+'</p>');
      	$("#category_item_selection").append(category_item);
		 }
		 else if (json.categories_and_items[k].type == 'item')
		 {
	       var image_src = json.categories_and_items[k].image_src;
	       var prod_image = "";
	       var image_class = "no-image";
	       var item_parent_class = "";
	       if (image_src != '' ) {
	         var item_parent_class = "item_parent_class";
	         var prod_image = '<img src="'+image_src+'" alt="" />';
	         var image_class = "";
	       }
      
	       var item = $("<div/>").attr('class', 'category_item item col-md-2 register-holder ' + image_class + ' col-sm-3 col-xs-6  '+item_parent_class).attr('data-id', json.categories_and_items[k].id).append(prod_image+'<p>'+json.categories_and_items[k].name+'<br /><span class="text-bold">'+(json.categories_and_items[k].price ? '('+(json.categories_and_items[k].different_price ? '<span style="text-decoration: line-through;">'+json.categories_and_items[k].regular_price+'</span> ': '')+json.categories_and_items[k].price+')' : '')+'</span></p>');
	       $("#category_item_selection").append(item);
		 	
		 }
	 }
	 	 
    $("#category_item_selection_wrapper .pagination").removeClass('categories').removeClass('tags').removeClass('items').addClass('categoriesAndItems');
    $("#category_item_selection_wrapper .pagination").html(json.pagination);

    updateBreadcrumbs();
    $('#grid-loader').hide();
    
  }
  
  function processTagItemsResult(json)
  {
 	 $("#category_item_selection").html('');
     var back_to_categories_button = $("<div/>").attr('id', 'back_to_tags').attr('class', 'category_item register-holder no-image back-to-categories col-md-2 col-sm-3 col-xs-6 ').append('<p>&laquo; '+<?php echo json_encode(lang('common_back_to_tags')); ?>+'</p>');
     $("#category_item_selection").append(back_to_categories_button);

     for(var k=0;k<json.items.length;k++)
     {
 	       var image_src = json.items[k].image_src;
 	       var prod_image = "";
 	       var image_class = "no-image";
 	       var item_parent_class = "";
 	       if (image_src != '' ) {
 	         var item_parent_class = "item_parent_class";
 	         var prod_image = '<img src="'+image_src+'" alt="" />';
 	         var image_class = "";
 	       }
      
 	       var item = $("<div/>").attr('class', 'category_item item col-md-2 register-holder ' + image_class + ' col-sm-3 col-xs-6  '+item_parent_class).attr('data-id', json.items[k].id).append(prod_image+'<p>'+json.items[k].name+'<br /> <span class="text-bold">'+(json.items[k].price ? '('+(json.items[k].different_price ? '<span style="text-decoration: line-through;">'+json.items[k].regular_price+'</span> ': '')+json.items[k].price+')' : '')+'</span></p>');
 	       $("#category_item_selection").append(item);
		 	
 	 }
	 	 
     $("#category_item_selection_wrapper .pagination").removeClass('categories').removeClass('tags').removeClass('categoriesAndItems').addClass('items');
     $("#category_item_selection_wrapper .pagination").html(json.pagination);

     $('#grid-loader').hide();
  }
  
  <?php if ($this->config->item('default_type_for_grid') == 'tags') {  ?>
	  loadTags();
	<?php
	}else
	{
	?>
	loadTopCategories();
	<?php	
	}
	?>
});

<?php if (!$this->agent->is_mobile()) { ?>
	var last_focused_id = null;
	
	setTimeout(function(){$('#item').focus();}, 10);
<?php } ?>
</script>


<script>
//Keyboard events...only want to load once
$(document).keyup(function(event)
{
	var mycode = event.keyCode;
	
	//tab
	if (mycode == 9)
	{
		var $tabbed_to = $(event.target);
		
		if ($tabbed_to.hasClass('xeditable'))
		{
			$tabbed_to.trigger('click').editable('show');
		}
	}

});

$(document).keydown(function(event)
{	
	var mycode = event.keyCode;
	
	//F2
	if (mycode == 113)
	{
		$("#item").focus();
		return;
	}

	//F4
	if (mycode == 115)
	{
		event.preventDefault();
		$("#finish_sale_alternate_button").click();
		$("#finish_sale_button").click();
		return;
	}

	//F7
	if (mycode == 118)
	{
		event.preventDefault();
		$("#amount_tendered").focus();
		$("#amount_tendered").select();
		return;
	}

	//ESC
	if (mycode == 27)
	{
		event.preventDefault();
		$("#cancel_sale_button").click();
		return;
	}	
	
	
});

</script>
<?php if($this->config->item('virtual_keyboard') == 'all' || ($this->agent->is_mobile() && $this->config->item('virtual_keyboard')  == 'mobile') ||  (!$this->agent->is_mobile() && $this->config->item('virtual_keyboard')  == 'desktop')) { ?>
		<script src="<?php echo base_url().'assets/js/onscreen_keyboard_initial.js'. '?' . ASSET_TIMESTAMP;?>" type="text/javascript" charset="UTF-8"></script>
<?php } ?>
<?php $this->load->view("partial/footer"); ?>