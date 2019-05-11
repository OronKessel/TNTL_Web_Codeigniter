var currentFeedCount = 0;
var currentPlay = 0;
function changeSearch(value)
{
    if (value == '')
    {
        $('#search-content').hide();
        return;
    }
}
function enterSearch(obj,event)
{
    var keyword = $('#input-search').val();
    if (keyword == '')
    {
        $('#search-content').hide();
        return;
    }
    if (event.keyCode == 13)
    {
        location.href = baseUrl + 'index.php/Search/index/' + keyword;
    }
    $('#search-content').html('');
    $.post(baseUrl + "index.php/Search/ajaxSearchKeyword",
    {
        keyword: keyword,
    },
    function(data, status) {
        var result = JSON.parse(data);
        if (result.result == 200)
        {
            if (result.members.length == 0 && result.videos.length == 0)
            {
                $('#search-content').hide();
            }
            else{
                
                $('#search-content').show();
            }
            if (result.members.length > 0)
            {
                addMemberView(result.members);
            }
            if (result.videos.length > 0)
            {
                addVideoView(result.videos);
            }
        }
    });
}
function addMemberView(members)
{
    $.each(members, function(key, value) {
        var memberHTML = "<a style='text-decoration:none;' href='" + baseUrl + "index.php/Profile/index/" + value.member_id + "'><div class=\"single-video-search\">\n\
        <img src=\"" + baseUrl + "assets/images/notification.png\" class=\"img-top-icon\"/>\n\
        <div class=\"search-account-info\">\n\
            <div class=\"single-video-account\">\n\
                        <p class=\"search-account-name\">" + value.username + "</p>\n\
                        <p class=\"search-account-follow\" style=\"text-align:right;\">" + value.followings + " followers</p>\n\
                    </div>\n\
                    <p class=\"search-account-desc\">" + value.description + "</p>\n\
                </div>\n\
        </div></a>";
        $('#search-content').append(memberHTML);
    });
}
function addVideoView(videos)
{
    $.each(videos, function(key, value) {
        var videoHTML = "<div class=\"separate-line\" style=\"margin-top:0px;\"></div>\n\
        <a style='text-decoration:none;' href='" + baseUrl + "index.php/Video/index/" + value.id + "'><div class=\"search-video-info\">\n\
            <p class=\"search-account-desc\">" + value.video_title + "</p>\n\
        </div></a>";
        $('#search-content').append(videoHTML);
    });
}
$(document).ready(function() {
   $('#btnHeaderNotification').click(function(){
        if ($('#notification-content').is(':visible'))
        {
            $('#notification-content').hide();
        }
        else
        {
            $('#notification-content').show();
        }
   });
});