var commentPage = 0;
var order = 0;
function loadComment()
{
    $.post(baseUrl + "index.php/Video/ajaxGetComments",
    {
        videoId: videoId,
        page: commentPage,
        order:order
    },
    function(data, status) {
        var result = JSON.parse(data);
        if (result.result == 200)
        {
            if (result.comments.length > 0)
            {
                commentPage = parseInt(result.page) + 1;
            }
            addComments(result.comments);
        }
    });
}
function addComments(comments)
{
    $.each(comments, function(key, value) {
        var commentHTML = "<div class=\"video-other-comment\">\n\
                                <img src=\"" + baseUrl + "assets/images/avatar.png\" class=\"video-img-small\"/>\n\
                                <div style=\"width:100%;margin-left:10px;\">\n\
                                    <div style=\"display:flex;\">\n\
                                        <a class='author' href='" + baseUrl + "index.php/Profile/index/" + value.memberInfo.username + "'><h2 class=\"bold-text\" style=\"font-size:14px;color:#292391;\">" + value.memberInfo.username + "</h2></a>\n\
                                        <p style=\"margin-top:2px;margin-bottom:2px;text-align:right;width:100%;color:#878787;font-size:13px;\">" + value.elapse + "</p>\n\
                                    </div>\n\
                                    <p style=\"margin-top:2px;margin-bottom:0px;\">" + value.content + "</p>\n\
                                </div>\n\
                            </div>";
        $('#video-comments').append(commentHTML);
        if (comments.length - 1 > key)
        {
            $('#video-comments').append("<div class=\"separate-line\" style=\"margin-bottom:10px;\"></div>");                        
        }        
    });
}
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
$(document).ready(function() {

    $('#sort_button').click(function(){
        if ($('#sort-content').is(":visible"))
        {
            $('#sort-content').hide();
        }
        else
        {
            $('#sort-content').show();
        }
    });
    $('#sort-recent').click(function(){
        $('#sort-content').hide();
        order = 0;
        commentPage = 0;
        $('#sort_button').text('Most Recent');
        $('#video-comments').html('');
        loadComment();
    });
    $('#sort-old').click(function(){
        $('#sort-content').hide();
        order = 1;
        commentPage = 0;
        $('#sort_button').text('Oldest');
        $('#video-comments').html('');
        loadComment();
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
                    $('#btnFollow').removeClass('btn-follow-small');
                    $('#btnFollow').addClass('btn-followed-small');
                    $('#btnFollow').text('Following');
                }
                else 
                {
                    $('#btnFollow').removeClass('btn-followed-small');
                    $('#btnFollow').addClass('btn-follow-small');    
                    $('#btnFollow').text('Follow');
                }
            }
        });
        
    });
    loadComment();
});