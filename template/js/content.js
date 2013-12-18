function setRating(r) {
	$('.stars .star:lt('+r+') img').attr('src','/template/img/starFull.png');
	if (r>0) {
		$('.stars .star:gt('+(r-1)+') img').attr('src','/template/img/starEmpty.png');
	} else {
		$('.stars .star img').attr('src','/template/img/starEmpty.png');
	}
}
function setYourRating(r) {
	$('.stars .star:lt('+r+') img').attr('src','/template/img/starYour.png');
	if (r>0) {
		$('.stars .star:gt('+(r-1)+') img').attr('src','/template/img/starEmpty.png');
	} else {
		$('.stars .star img').attr('src','/template/img/starEmpty.png');
	}
}
$(function () {
	if (!yourVote || yourVote==0) {
		setRating(rating);
		if (canVote) {
			$('.stars .star').mouseenter(function()  {
				if (canVote) {
					var rt = $(this).attr('id').slice(4); 
					setYourRating(rt);
				}
			}).click(function () {
				var rt = $(this).attr('id').slice(4);
				yourVote=rt;
				canVote=false;
				$('.stars').unbind('mouseleave mouseenter');
				$.post('/content',
					{
						'Action':'SetVote',
						'content': content_id,
						'vote':rt
					},function(data) {
						if (data.success) {
							$('.rating .stat .bold').html(parseFloat(data.rating).toFixed(2));
							$('.rating .stat .count').html(data.cnt);
						}
					},'json'
				);
			});
			$('.stars').mouseleave(function () {
				setRating(rating);
			});
		}
	} else {
		setYourRating(yourVote);
	}
});