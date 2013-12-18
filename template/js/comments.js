function loadComments(page) {
	$.post('/content',
	{
		'Action': 'LoadComments',
		'content': content_id,
		'page': page
	},function(data) {
		var city='';
		$('.comments').html('');
		$('.commentsCount').html('('+data['commentsCount']+')');
		
		for (var i in data['comments']) {
			$('.comments').append('<div class="contentBlock"> \
				<div class="contentTitle">\
					<b><small>'+(data['comments'][i]['name'] ? data['comments'][i]['name'] : 'Guest' )+'</b> ('+data['comments'][i]['time']+'):</small></b>\
					'+(data['comments'][i]['canDelete'] ? '<span class="right">IP: '+data['comments'][i]['ip']+' <a href="#" onclick="deleteComment('+data['comments'][i]['id']+'); return false;"><img src="/template/img/delete.png" alt="delete" /></a></span>' : '')+'\
				</div>\
				<div class="comment contentText">'+data['comments'][i]['text']+'</div>\
			</div>');
		}
		if(data['pages']>1){
			var pagesLine = 'Pages: ';
			for (var i=1; i<=data['pages']; i++) {
				pagesLine += ' <a href="#" onclick="loadComments('+i+'); return false;                                                                            "';
				if (i==page) {pagesLine += ' style="font-weight: bold" ';}
				pagesLine += '>'+i+'</a> ';
			}
			$('.pagesLine').html(pagesLine);
		} else {
			$('.pagesLine').html('');
		}
		var max_len = 150;
		$('div.comment').each(function(n) {
			var div = $(this);
			var fullhtml = div.html().replace("<br>","\n");
			
			if (div.text().length > max_len) {
				div.html('<div class="shorttext">' + fullhtml.substr(0, max_len) + '</div><div class="fulltext" style="display:none"></div>');
				div.find('.fulltext').html(fullhtml);
				div.append('<div><a class="more" href="#" onclick="var txt=$(this).closest(\'.comment\').find(\'.fulltext\').html(); $(this).closest(\'.comment\').html(txt); return false;">Весь текст...</a></div>');
			}
			
		}); 
		
	}, 'json');
}
function deleteComment(id) {
	if (confirm('Are you sure?') && id>0) {
		$.post('/content',
			{
				'Action':'DeleteComment',
				'comment': id
			},function (data) {
//  				alert(data);
 				if (data.success) {
 					jAlert('Comment deleted!','Close');

 					loadComments(1);
 				}
		},'json');
	}
}
$(document).ready(function(){
	$('.commentSend').click(function () {

		if ($.trim($('.commentTextarea').val())) {
			$.post('/content',
			{
				'Action':'AddComment',
				'content': content_id,
				'text': $.trim($('.commentTextarea').val())
			},function (data) {
//  				alert(data);
 				if (data.success) {
 					jAlert('Thank you for your comment!','Close');
 					$('.commentTextarea').val('');
 					loadComments(1);
 				}
			},'json');
		}
	});
	loadComments(1);
});