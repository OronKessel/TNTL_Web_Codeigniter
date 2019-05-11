var currentFeedCount = 0;
var currentPlay = 0;
function inputComment(obj,event)
{

    if (event.keyCode == 13)
    {
        if (obj.value != '')
        {
            $.post(baseUrl + "index.php/Home/ajaxAddComment",
                {
                    memberId: memberId,
                    postId: obj.dataset.video,
                    content: obj.value,
                },
                function(data, status) {
                    var result = JSON.parse(data);
                    if (result.result == 200)
                    {
                        obj.value = '';
                        //updateComment();
                    }
                });
        }
    }
}
function likeVideo(obj,event)
{
    if (memberId == '') return;
    $.post(baseUrl + "index.php/Home/ajaxLike",
    {
        memberId: memberId,
        postId: obj.dataset.video,
        like: (parseInt(obj.dataset.like) + 1) % 2,
    },
    function(data, status) {
        var result = JSON.parse(data);
        if (result.result == 200)
        {
            if (result.value == '1')
            {
                obj.src= baseUrl + "assets/images/like_p.png";
                obj.dataset.like = result.value;
                $('#unlike_' + obj.dataset.video).html(result.count1);
                $('#img_unlike_' + obj.dataset.video ).attr('src',baseUrl + "assets/images/unlike.png");
                $('#img_unlike_' + obj.dataset.video ).attr('data-unlike',0);
            }
            else 
            {
                obj.src= baseUrl + "assets/images/like.png";
                obj.dataset.like = result.value;
            }
            $('#like_' + obj.dataset.video).html(result.count);
        }
    });
}
function unlikeVideo(obj,event)
{
    if (memberId == '') return;
    $.post(baseUrl + "index.php/Home/ajaxUnlike",
    {
        memberId: memberId,
        postId: obj.dataset.video,
        like: (parseInt(obj.dataset.unlike) + 1) % 2,
    },
    function(data, status) {
        var result = JSON.parse(data);
        if (result.result == 200)
        {
            if (result.value == '1')
            {
                obj.src= baseUrl + "assets/images/unlike_p.png";
                obj.dataset.unlike = result.value;
                $('#like_' + obj.dataset.video).html(result.count1);
                $('#img_like_' + obj.dataset.video ).attr('src',baseUrl + "assets/images/like.png");
                $('#img_like_' + obj.dataset.video ).attr('data-like',0);
            }
            else 
            {
                obj.src= baseUrl + "assets/images/unlike.png";
                obj.dataset.unlike = result.value;
            }
            $('#unlike_' + obj.dataset.video).html(result.count);
        }
    });
}
$(document).ready(function() {
    var setTab = function(index)
    {
        $('#tab_collection').removeClass('tab_home_item_active');
        $('#tab_feed').removeClass('tab_home_item_active');
        $('#feed-container').hide();
        $('#collection-container').hide();
        switch (index)
        {
            case 0:
                $('#tab_collection').addClass('tab_home_item_active');
                $('#collection-container').show();
                break;
            case 1:
                $('#tab_feed').addClass('tab_home_item_active');
                $('#feed-container').show();
                break;
        }
    }
    
    $('label[class="tab_home_item"]').click(function() {
        var tabIndex = $(this).data('tab-index');
        setTab(tabIndex);
    });
    $('#btnFollow').click(function() {
        if (memberId == '') return;
        $.post(baseUrl + "index.php/Profile/ajaxFollow",
        {
            memberId: memberId,
            followId: profileId,
            value: (parseInt(isFollow) + 1) % 2,
        },
        function(data, status) {
            var result = JSON.parse(data);
            if (result.result == 200)
            {
                isFollow = result.value;
                $('#following_count').text(result.followings);
                $('#follower_count').text(result.followers);
                if (result.value == '1')
                {
                    $('#btnFollow').removeClass('btn-follow');
                    $('#btnFollow').addClass('btn-followed');
                    $('#btnFollow').text('Following');
                }
                else 
                {
                    $('#btnFollow').removeClass('btn-followed');
                    $('#btnFollow').addClass('btn-follow');    
                    $('#btnFollow').text('Follow');
                }
            }
        });
        
    });
    setTab(0);
});