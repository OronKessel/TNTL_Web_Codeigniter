var currentFeedCount = 0;
var currentPlay = 0;
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
            var itemHTML = "<div class=\"video_item\">\n\
                    <div class=\"row\">\n\
                        <div class=\"col-md-2\">\n\
                            <div class=\"well-box corner-radius\">\n\
                            <div class=\"left-info\">\n\
                                        <a href=\"index.php/Profile/index\"><img src=\"assets/images/avatar.png\" style=\"width:70%;height:100%;\"/></a>\n\
                                    </div>\n\
                                    <div class=\"left-info\">\n\
                                        <img src=\"assets/images/eye.png\" style=\"width:30%;height:100%;\"/>\n\
                                        <p>" + value.memberInfo.view_count + "</p>\n\
                                    </div>\n\
                                    <div class=\"left-info\">\n\
                                        <img src=\"assets/images/like.png\" style=\"width:30%;height:100%;\"/>\n\
                                        <p>" + value.memberInfo.lk_count + "</p>\n\
                                    </div>\n\
                                    <div class=\"left-info\">\n\
                                        <img src=\"assets/images/unlike.png\" style=\"width:30%;height:100%;\"/>\n\
                                        <p>" + value.memberInfo.ulk_count + "</p>\n\
                                    </div>\n\
                            </div>\n\
                        </div>\n\
                        <div class=\"col-md-10 post-holder\">\n\
                                <div class=\"well-box corner-radius\">\n\
                                    <div class=\"row\">\n\
                                        <div class=\"col-md-11\">\n\
                                            <video id='video_" + (currentFeedCount + key) + "' width=\"100%\" style=\"border-radius: 10px;\">\n\
                                                <source src=\"" + baseUrl + value.file + "\" type=\"video/ogg\">\n\
                                                Your browser does not support HTML5 video.\n\
                                            </video>\n\
                                            <p class=\"upload_time\">Added 2 days ago</p>\n\
                                            <div class=\"comments\">\n\
                                                <div class=\"row\">\n\
                                                    <div class=\"col-md-2\">\n\
                                                        <label class=\"comment_author\">Jonathan:</label>\n\
                                                    </div>\n\
                                                    <div class=\"col-md-10\">\n\
                                                        <label class=\"comment\">Dog is crazy!</label>\n\
                                                    </div>\n\
                                                </div>\n\
                                                <div class=\"row\">\n\
                                                    <div class=\"col-md-2\">\n\
                                                        <label class=\"comment_author\">Jonathan:</label>\n\
                                                    </div>\n\
                                                    <div class=\"col-md-10\">\n\
                                                        <label class=\"comment\">Dog is crazy!</label>\n\
                                                    </div>\n\
                                                </div>\n\
                                                <div class=\"row\">\n\
                                                    <div class=\"col-md-2\">\n\
                                                        <label class=\"comment_author\">Jonathan:</label>\n\
                                                    </div>\n\
                                                    <div class=\"col-md-10\">\n\
                                                        <label class=\"comment\">Dog is crazy!</label>\n\
                                                    </div>\n\
                                                </div>\n\
                                            </div>\n\
                                            <p class=\"loadmore\">Load more...</p>\n\
                                            <div class=\"input-group\">\n\
                                                <input type=\"text\" placeholder=\"Comment here...\" class=\"form-control\"/>\n\
                                                <span class=\"input-group-btn\">\n\
                                                    <button class=\"btn tp-btn-default tp-btn-lg\" type=\"button\">Submit</button>\n\
                                                </span>\n\
                                            </div>\n\
                                        </div>\n\
                                        <div class=\"col-md-1\">\n\
                                            <div class=\"left-info\">\n\
                                                <img src=\"assets/images/share.png\" style=\"width:70%;height:100%;\"/>\n\
                                            </div>\n\
                                            <div class=\"left-info\">\n\
                                                <img src=\"assets/images/plus.png\" style=\"width:70%;height:100%;\"/>\n\
                                            </div>\n\
                                            <div class=\"left-info\">\n\
                                                <img src=\"assets/images/warning.png\" style=\"width:70%;height:100%;\"/>\n\
                                            </div>\n\
                                        </div>\n\
                                    </div>\n\
                                </div>\n\
                            </div>\n\
                    </div>\n\
                </div>";
            $('#feedContainer').append(itemHTML);
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
                    memberId: currentFeedCount,
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
        var topBarHeight = $('#headersticky-sticky-wrapper').height();
        $.each($('div[class="video_item"]'), function(key, value) {
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