$("#submit").click(function(e) {

    if ($("#user").val() == '' || $("#passwd").val() == '') {

    } else {
        e.preventDefault();
        $("#load-dh").css("display", "flex");
        $.ajax({
            type: "POST", //方法类型
            dataType: "json", //预期服务器返回的数据类型
            url: "core/mark.php", //url
            data: $('#thisForm').serialize(),
            success: function(result) {
                console.log(result);
                if (result.status == 'success') {
                    $("#studentname").text(result['姓名']);
                    $("#studentpoint").text('绩点：' + result['绩点']);
                    $("title")[0].text = result['姓名'] + ' | 绩点 ' + result['绩点'];
                    ssssss(result);
                    $("#bg").css("display", "none");
                    $("#mark").css("display", "block");
                    $("#load-dh").css("display", "none");
                } else {
                    if (result.status == 'fail') {
                        tip('请重新验证！');
                    } else if (result.status == 'password') {
                        tip('学号与密码不匹配！');
                    } else if (result.status == 'longtime') {
                        tip('请求超时！');
                    } else {
                        tip('抱歉，服务器异常！');
                    }
                    $("#load-dh").css("display", "none");
                }
            },
            error: function(res) {
                $("#load-dh").css("display", "none");
                tip('服务器异常，请重试！');
                console.log(res.responseText)
            }
        });
    }



});

function tip(str) {
    $("#notice")[0].className = "alert show";
    $("#notice").text(str);
    setTimeout(function() {
        $("#notice")[0].className = "hide";
    }, 2000);
}

function ssssss(data) {
    // var res = $.parseJSON(data);

    var chengji = data['result'];
    var chengjiHtml = "";
    for (var i = 0; i <= chengji.length - 1; i++) {
        chengjiHtml += '<tr>';
        chengjiHtml += '<th class="th-trem">' + chengji[i]['学期'] + '</th>';
        chengjiHtml += '<th class="th-course-name">' + chengji[i]['课程名称'] + '</th>';
        chengjiHtml += '<th class="th-category">' + chengji[i]['类别'] + '</th>';
        chengjiHtml += '<th class="th-credit">' + chengji[i]['学分'] + '</th>';
        chengjiHtml += '<th class="th-daily-mark">' + chengji[i]['平时'] + '</th>';
        chengjiHtml += '<th class="th-midterm-mark">' + chengji[i]['期中'] + '</th>';
        chengjiHtml += '<th class="th-exam-mark">' + chengji[i]['期末'] + '</th>';
        chengjiHtml += '<th class="th-final-mark">' + chengji[i]['总评成绩'] + '</th>';
        chengjiHtml += '<th class="th-exam-nature">' + chengji[i]['考试性质'] + '</th>';
        chengjiHtml += '<th class="th-mark-point">' + chengji[i]['绩点'] + '</th>';
        chengjiHtml += '<th class="th-course-code">' + chengji[i]['课程代码'] + '</th>';
        chengjiHtml += '<th class="th-class-hour">' + chengji[i]['学时'] + '</th>';
        chengjiHtml += '</tr>';
    }
    $("#chengji-body").html(chengjiHtml);
}