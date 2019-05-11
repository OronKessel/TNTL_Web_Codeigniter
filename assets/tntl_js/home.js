var currentFeedCount = 0;
var currentPlay = 0;
function inputComment(obj, event)
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
                            //initComment(obj.dataset.video);
                            var commentHTML = "<div>\n\
                                                    <a class='author' href='" + baseUrl + "index.php/Profile/index/" + memberName + "'><label class=\"comment_author\">" + memberName + ":</label></a>\n\
                                                    <label class=\"comment\">" + obj.value + "</label>\n\
                                                </div>";
                            $('#feed-comment-' + obj.dataset.video).append(commentHTML);
                            obj.value = '';
                            //updateComment();
                        }
                    });
        }
    }
}
function addFeedComments(videoId, comments)
{
    var oldContent = $('#feed-comment-' + videoId).html();
    $('#feed-comment-' + videoId).html('');
    $.each(comments, function(key, value) {
        var commentHTML = "<div>\n\
                                <a class='author' href='" + baseUrl + "index.php/Profile/index/" + value.memberInfo.username + "'><label class=\"comment_author\">" + value.memberInfo.username + ":</label></a>\n\
                                <label class=\"comment\">" + value.content + "</label>\n\
                            </div>";
        $('#feed-comment-' + videoId).append(commentHTML);
    });
    $('#feed-comment-' + videoId).append(oldContent);
}
function initComment(videoId)
{
    $('#feed-comment-' + videoId).data('page', 0);
    $('#feed-comment-' + videoId).html('');
    loadFeedComment(videoId);
}
function loadFeedComment(videoId)
{
    var page = $('#feed-comment-' + videoId).data('page');
    $.post(baseUrl + "index.php/Home/ajaxGetComment",
            {
                videoId: videoId,
                page: parseInt(page),
            },
            function(data, status) {
                var result = JSON.parse(data);
                if (result.result == 200)
                {
                    if (result.comments.length > 0)
                    {
                        $('#feed-comment-' + result.videoId).data('page', parseInt(result.page) + 1);
                    }
                    if (result.comments.length < 5)
                    {
                        $('#feed-more-comment-' + result.videoId).hide();
                    }
                    addFeedComments(result.videoId, result.comments);
                }
            });
}
$(document).ready(function() {
    var showLoader = function()
    {
        $('.preloader').show();
        $('.preloader').preloader({
            text: '',
            percent: 100,
            duration: 3
        });
    }
    var hideLoader = function()
    {
        $('.preloader').preloader('remove');
        $('.preloader').hide();
    }
    var setTab = function(index)
    {
        $('#tab_feed').removeClass('tab_home_item_active');
        $('#tab_featured').removeClass('tab_home_item_active');
        $('#tab_funniest').removeClass('tab_home_item_active');
        switch (index)
        {
            case 0:
                $('#tab_feed').addClass('tab_home_item_active');
                break;
            case 1:
                $('#tab_featured').addClass('tab_home_item_active');
                break;
            case 2:
                $('#tab_funniest').addClass('tab_home_item_active');
                break;
        }
    }


    var addFeedViews = function(datas) {
        $.each(datas, function(key, value) {
            var commentBox = "<input data-video=\"" + value.id + "\" name=\"input-comment\" type=\"text\" placeholder=\"Must login to comment\" disabled class=\"input-comment\"/>";
            if (isLogin == "1")
            {
                commentBox = "<input data-video=\"" + value.id + "\" name=\"input-comment\"  type=\"text\" placeholder=\"Comment..\" class=\"input-comment\"/>";
            }
            ;
            var likeImage = "<img id='img_like_" + value.id + "' data-like=\"0\" data-video=\"" + value.id + "\" name=\"video-like\" src=\"" + baseUrl + "assets/images/like.png\" class=\"img-feed-left\"/>";
            if (value.like)
            {
                likeImage = "<img id='img_like_" + value.id + "' data-like=\"1\" data-video=\"" + value.id + "\" name=\"video-like\" src=\"" + baseUrl + "assets/images/like_p.png\" class=\"img-feed-left\"/>";
            }
            var unlikeImage = "<img id='img_unlike_" + value.id + "' data-unlike=\"0\"  data-video=\"" + value.id + "\" name=\"video-unlike\" src=\"" + baseUrl + "assets/images/unlike.png\" class=\"img-feed-left\"/>";
            if (value.unlike)
            {
                unlikeImage = "<img id='img_unlike_" + value.id + "' data-unlike=\"1\" data-video=\"" + value.id + "\" name=\"video-unlike\" src=\"" + baseUrl + "assets/images/unlike_p.png\" class=\"img-feed-left\"/>";
            }
            var itemHTML = "<div class=\"feed-container\">\n\
                                <div class=\"side-feed-bar\">\n\
                                    <a href=\"" + baseUrl + "index.php/Profile/index/" + value.memberInfo.username + "\"><img src=\"assets/images/avatar.png\" class=\"avatar-img\"/></a>\n\
                                        <div class=\"left-info\">\n\
                                            <img src=\"" + baseUrl + "assets/images/eye.png\" class=\"img-eye\"/>\n\
                                            <p class=\"lbl-standard\">" + value.view_count + "</p>\n\
                                        </div>\n\
                                        <div class=\"left-info\">\n\
                                            <img src=\"" + baseUrl + "assets/images/emoji.png\" class=\"img-feed-left\"/>\n\
                                            <p class=\"lbl-view\">89%</p>\n\
                                        </div>\n\
                                        <div class=\"left-info\">" + likeImage + "<p class=\"lbl-standard\" id='like_" + value.id + "'>" + value.lk_count + "</p>\n\
                                        </div>\n\
                                        <div class=\"left-info\">" + unlikeImage + "<p class=\"lbl-standard\" id='unlike_" + value.id + "'>" + value.ulk_count + "</p>\n\
                                        </div>\n\
                                </div>\n\
                                <div class=\"video-container\">\n\
                                <a href=\"" + baseUrl + "index.php/Video/index/" + value.id + "\">\n\
                                <video id='video_" + (currentFeedCount + key) + "' class=\"feed-video\">\n\
                                    <source src=\"" + baseUrl + value.file + "\" type=\"video/ogg\">\n\
                                    Your browser does not support HTML5 video.\n\
                                </video>\n\
                                </a>\n\
                                <p class=\"upload_time\">Added " + value.elapse + "&nbsp&nbsp</p>\n\
                                <div class=\"comments-container\" data-page=\"0\" id=\"feed-comment-" + value.id + "\">\n\
                                    </div><div style='text-align:right;'><a class='feed-more-comment' id='feed-more-comment-" + value.id + "'>more...</a></div><div class=\"separate-line\" style=\"margin-left:20px;margin-right:20px;\"></div>" + commentBox + "</div>\n\
                    <div class=\"side-feed-bar\" style=\"margin:auto auto;\">\n\
                        <div class=\"right-info\">\n\
                            <img src=\"" + baseUrl + "assets/images/share.png\" class=\"img-feed-right\"/>\n\
                        </div>\n\
                        <div class=\"right-info\">\n\
                        <img src=\"" + baseUrl + "assets/images/plus.png\" class=\"img-feed-right\"/>\n\
                    </div>\n\
                    <div class=\"right-info\">\n\
                        <img src=\"" + baseUrl + "assets/images/warning.png\" class=\"img-feed-right\"/>\n\
                    </div>\n\
                </div>\n\
            </div>";
            $('#feedContainer').append(itemHTML);
            loadFeedComment(value.id);
            $('#feed-more-comment-' + value.id).click(function() {
                loadFeedComment(value.id);
            });
        });
        $.each($('input[name="input-comment"]'), function(key, value) {
            value.setAttribute('onkeypress', 'javascript:inputComment(this,event)');
        });
        $.each($('img[name="video-unlike"]'), function(key, value) {
            value.setAttribute('onclick', 'javascript:unlikeVideo(this,event)');
        });
        $.each($('img[name="video-like"]'), function(key, value) {
            value.setAttribute('onclick', 'javascript:likeVideo(this,event)');
        });
        if (datas.length > 0 && currentFeedCount == 0)
        {
            var video = document.getElementById('video_0');
            video.autoplay = true;
            video.play();
        }
        currentFeedCount += datas.length;
    }
    var loadVideos = function(start) {
        showLoader();
        $.post(baseUrl + "index.php/Home/ajaxFeed",
                {
                    memberId: memberId,
                    start: currentFeedCount,
                    count: 3,
                },
                function(data, status) {
                    hideLoader();
                    var result = JSON.parse(data);
                    addFeedViews(result.feeds);
                });
    }

    var playVideo = function(pos)
    {
        if (pos == 0)
        {
            var video = document.getElementById('video_0');
            if (video != null)
            {
                video.autoplay = true;
                video.play();
                currentPlay = 0;
                return;
            }
        }
        var topBarHeight = 80;
        $.each($('div[class="feed-container"]'), function(key, value) {
            console.log($(window).scrollTop() + $(window).height() + "----" + (topBarHeight + value.offsetHeight * (key + 1)));
            if ($(window).scrollTop() + $(window).height() < parseInt(topBarHeight) + parseInt(value.offsetHeight * (key + 1)))
            {
                console.log(key);
                console.log(currentPlay);
                if (currentPlay == key)
                    return false;
                var video = document.getElementById('video_' + (key));
                video.autoplay = true;
                console.log(key);
                video.play();
                currentPlay = key;
                return false;
            }
            else
            {
                var video = document.getElementById('video_' + (key));
                video.autoplay = true;
                console.log(key);
                video.pause();
            }
        });
    }
    $('label[class="tab_home_item"]').click(function() {
        var tabIndex = $(this).data('tab-index');
        setTab(tabIndex);
    });
    //Check Scroll Bottom
    $(window).scroll(function() {
        playVideo($(window).scrollTop());
        if ($(window).scrollTop() + $(window).height() == $(document).height()) {
            if (currentFeedCount > 0)
                loadVideos(currentFeedCount);
        }
    });
    setTab(0);
    loadVideos(currentFeedCount);
});