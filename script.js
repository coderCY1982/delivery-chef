$(function () {
    $('a[href^="#"]').click(function () {
        var speed = 500;
        var href = $(this).attr("href");
        var target = $(href == "#" || href == "" ? 'html' : href);
        var position = target.offset().top;
        $("html, body").animate({ scrollTop: position }, speed, "swing");
        return false;
    });


    $("#referrer").val(document.referrer);
    $("#mailform").submit(function () {
        $.ajax({
            url: 'sendmail.php',
            type: 'POST',
            dataType: 'json',
            data: $(this).serialize()
        })
            .done(function (data) {
                $("#dispmsg").empty();
                $("#dispmsg").html(data.dispmsg);
                if (data.errflg != 1) {
                    $(".form-row").remove();
                    $(".btn-submit").remove();
                }
            })
            .fail(function (data) {
                alert('メール送信に失敗しました');
            })
        return false;
    });
});