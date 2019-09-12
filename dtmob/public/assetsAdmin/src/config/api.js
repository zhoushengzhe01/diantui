var page = 'home';

var name = 'zhou';

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


//接口处理函数
function api_request(method, url, params, success, failure)
{
    params = filter_null(params);
    
    if (method === 'POST' || method === 'PUT' || method === 'DELETE'){
        params._token = Token;
        params = JSON.stringify(params);
    }



    var ajax = $.ajax({
        type: method,
        url: url,
        cache: false,
        ifModified: true,
        dataType: "json",
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
                if(data.url)
                {
                    _go(data.url, 500);
                }
                else
                {
                    if(ajax.status >= 400)
                    {
                        failure('error', '网络错误');
                    }
                    if(ajax.status >= 300)
                    {
                        failure('warning', data.message);
                    }
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
    //获取URL参数
    getQueryString: function(name)
    { 
        var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)", "i");
        var r = window.location.search.substr(1).match(reg);
        if (r != null)
            return unescape(r[2]);
        else
            return null;
    },
}