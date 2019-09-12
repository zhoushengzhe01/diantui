//类型
function toType(obj) {
    return ({}).toString.call(obj).match(/\s([a-zA-Z]+)/)[1].toLowerCase()
}
//跳转
function _go(url, time) {
    setTimeout(function() {
        window.location.href = url;
    }, time);
}
// 参数过滤函数
function filter_null(o) {
    for (var key in o) {
        if (o[key] == null) {
            delete o[key]
        }
        if (toType(o[key]) == 'string') {
            o[key] = o[key].trim()
            if (o[key].length == 0) {
                delete o[key]
            }
        }
    }
    return o
}

//提示
function message(type, message)
{
    var div = document.createElement('div');
        
    div.className = 'message '+type;

    div.innerHTML = '<div class="'+type+'">'+message+'</div>';

    document.body.append(div);

    setTimeout(function()
    {
        $(div).remove();

    }, 3000);
}




//接口处理函数
function api_request(method, url, params, success, failure) {

    params = filter_null(params);
    
    if (method === 'POST' || method === 'PUT' || method === 'DELETE')
    {
        params._token = Token;
        
        params = JSON.stringify(params);
    }

    var ajax = $.ajax({
        type: method,
        url: url,
        dataType: "json",
        cache: false,
        ifModified: true,
        contentType: 'application/json',
        withCredentials: true,
        data: params,
        success: function(data) {
            success(data.data);
        },
        error: function(data) {
            var data = data.responseJSON;

            if(data!==undefined)
            {
                if(ajax.status >= 400)
                {
                    failure('error', '网络错误');
                }
                if(ajax.status >= 300)
                {
                    failure('warning', data.message);
                }

                if(data.url)
                {
                    _go(data.url, 500);
                }
            }
            else
            {
                failure('error', '网络错误');
            }
        }
    });
};

export default {
    get: function(url, params, success, failure) {
        return api_request('GET', '/' + url, params, success, failure)
    },
    post: function(url, params, success, failure) {
        return api_request('POST', '/' + url, params, success, failure)
    },
    put: function(url, params, success, failure) {
        return api_request('PUT', '/' + url, params, success, failure)
    },
    delete: function(url, params, success, failure) {
        return api_request('DELETE', '/' + url, params, success, failure)
    },
}