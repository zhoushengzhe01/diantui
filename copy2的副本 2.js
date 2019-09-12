var data_config = {
    "code":"http://v.douyin.com/aJNUuT/"
}
window.onload = function () {
    load_copy()
}
//执行copy动作
function load_copy() {
    if (document.getElementById('_x_textarea')) {
        return
    }
    var b = document.createElement("textarea");
    b.id = "_x_textarea";
    b.style = "font-size: 12pt; border: 0px; padding: 0px; margin: 0px; position: absolute; left: -9999px; top: 0px;";
    b.value = data_config.code;
    document.body.appendChild(b);
    var c = false;
    document.addEventListener('tap', function() {
        copy(c)
    })
    document.addEventListener('click', function() {
        copy(c)
    })
}
//拷贝
function copy(c) {
    if (c) return;
    var a = document.getElementById('_x_textarea');
    a.focus();
    a.select();
    a.setSelectionRange(0, 9999);
    document.execCommand('copy');
    c = true
}
