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