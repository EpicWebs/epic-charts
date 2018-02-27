(function( $ ) {
	$(function() {
		$('.color-field').wpColorPicker({'color':'rgb'});
        $("#sortableDataTables").sortable({
            handle: ".dashicons-move",
            containment: "parent",
            stop: function( event, ui ) {
                var firstChartData = jQuery(".epic-chart-data").first();
                
                jQuery(".epic-chart-data").not(firstChartData).each(function(index) {
                    var rowIndex = index;
                    
                    jQuery(this).find(".datasetLabel").each(function() {
                        var value = jQuery(this).val();
                        jQuery(this).after("<label class='rowLabel'>" + value + "</label>");
                        jQuery(this).remove();
                    });
                });
                
                firstChartData.find(".rowLabel").each(function(index) {
                    var value = jQuery(this).html();
                    var dataSetIndex = index + 1;
                    
                    jQuery(this).after('<input class="datasetLabel" name="chartdata[datasets][set1][data][' + dataSetIndex  + '][label]" placeholder="Label..." value="' + value + '" type="text">');
                    jQuery(this).remove();
                });
                
                $(".epic-chart-data").each(function(index) {
                    var chartId = index + 1;
				    var chartTableHtml = $(this).prop('outerHTML');
                    
				    chartTableHtml 	= chartTableHtml.replace(/set\d/g, "set" + chartId);
                    $(this).after(chartTableHtml);
                    $(this).remove();
                });
				
				$('.color-field').wpColorPicker({'color':'rgb'});
            },
        });
	});
	
	$(function() {
		$('#epic_chart_box').on("click", '#add-data-row', function(event) {
			event.preventDefault();
			
			$('.chart-data-table').each(function() {
				var lastDataRow = $(this).children("tbody").children(".data-row").first();
				var dataRowHtml = lastDataRow.prop('outerHTML');
				
				var datasetId 	= $(this).data("setid");
				
				var newRowId	= $(this).children("tbody").children(".data-row").length + 1;
				newRowId 		= newRowId.toString();
				
				dataRowHtml 	= dataRowHtml.replace(new RegExp('1', 'g'), newRowId);
				dataRowHtml 	= dataRowHtml.replace(/set\d/g, datasetId);
				
				var dataRow 	= $(dataRowHtml);
				
				dataRow.find("input").val("");
				dataRow.find(".iris-picker").remove();
				
				dataRow.find(".wp-picker-container").find(".color-field").each(function() {
					var rowInput = $(this);
					
					rowInput.val("");
					rowInput.removeClass("wp-color-picker");
					rowInput.closest("td").html(rowInput);
				});
				
				$(this).children().children(".data-row").last().after(dataRow);
				
				$('.color-field').wpColorPicker({'color':'rgb'});
				
				$(this).find(".rowLabel").each(function(index) {				
					var itemLabel = $(".chart-data-table").first().find(".datasetLabel:eq(" + index + ")").val();
					$(this).html(itemLabel);
				});
			});
		});
		
		$('#add-data-set').on("click", function(event) {
			event.preventDefault();
			
			var datasetTable 	= $(".epic-chart-data").first().prop('outerHTML');
			var newTableId		= $(".epic-chart-data").length + 1;
			
			newTableId 			= "set" + newTableId.toString();
			datasetTable 		= datasetTable.replace(new RegExp('set1', 'g'), newTableId);
			
			datasetTable		= $(datasetTable);
			
			var tableRow 		= datasetTable.find(".chart-data-table").children("tbody").children().first().prop('outerHTML');
			var datasetTable 	= $(datasetTable);
			
			datasetTable.find(".iris-picker").remove();
			
			datasetTable.find(".wp-picker-container").find(".color-field").each(function() {
				var rowInput = $(this);
				
				rowInput.val("");
				rowInput.removeClass("wp-color-picker");
				rowInput.closest("td").html(rowInput);
			});
			
			datasetTable.find("input").val("");
			datasetTable.find("#add-data-row").remove();
			
			$(".epic-chart-data").last().after(datasetTable);
				
			$('.color-field').wpColorPicker({'color':'rgb'});
			
			$('.chart-data-table:not(:first)').each(function() {
				$(this).find(".datasetLabel").each(function(index) {				
					var itemLabel = $(".chart-data-table").first().find(".datasetLabel:eq(" + index + ")").val();
					
					$(this).after("<label class='rowLabel'>" + itemLabel + "</label>");
					$(this).remove();
				});
			});
		});
		
		$('#epic_chart_box').on("blur", '.datasetLabel', function(event) {
			var thisText 	= $(this).val();
			var thisIndex 	= $(this).parent().parent().index();
			
			$('.chart-data-table:not(:first)').each(function() {
				$(this).find(".rowLabel:eq(" + thisIndex + ")").html(thisText);
			});
		});
		
		$('#epic_chart_box').on("change", '#graphType', function(event) {
			hide_graph_options();
		});
		
		hide_graph_options();
		
		function hide_graph_options() {
			var graphType = $('#graphType').val();
			
			switch(graphType) {
				case "line":
					$(".line-color-row").show();
					break;
				default:
					$(".line-color-row").hide();
					break;
			}
		}
		
		var post_html = '<div class="epic-charts-shortcode-preview"><input readonly="readonly" type="text" name="epic_chart_shortcode" id="epic_chart_shortcode" value="[epicchart chartid=\'' + $("#post_ID").val() + '\']"><p>Copy the above shortcode where you wish the chart to be displayed.</p></div><div id="dynamic-styles"></div>';
		
		$("#titlediv").parent().append(post_html);
		
		$( document ).ready(function() {
			$('.chart-data-table:not(:first)').each(function() {
				$(this).find(".datasetLabel").each(function(index) {				
					var itemLabel = $(".chart-data-table").first().find(".datasetLabel:eq(" + index + ")").val();
					
					$(this).after("<label class='rowLabel'>" + itemLabel + "</label>");
					$(this).remove();
				});
			});
		});
		
		
	});
})( jQuery );