/**
 * url 请求地址
 * success 成功的回调
 * fail 失败的回调
 */
function _get( url,data, success, fail ) {
    wx.request( {
        url: url,
        data:data,
        success: function( res ) {
            if(typeof res.data =="string"){
                var newstr = res.data.replace(/\r\n/gi, "");
                res = JSON.parse(newstr)
            }else{
                res = res.data
            }
            success( res );
        },
        fail: function( res ) {
            fail( res );
        }
    });
}
/**
 * url 请求地址
 * success 成功的回调
 * fail 失败的回调
 */
function _post(url,data, success, fail ) {
     wx.request( {
        url: url,
        header: {
            'content-type': 'application/x-www-form-urlencoded',
        },
        method:'POST',
        data: data,
        success: function( res ) {
            if(typeof res.data =="string"){
                 var newstr = res.data.replace(/\r\n/gi, "");
                 res = JSON.parse(newstr)
            }else{
                res = res.data
            }
            success( res );
        },
        fail: function( res ) {
            fail( res );
        }
    });
}
/**
 * url 请求地址
 * success 成功的回调
 * fail 失败的回调
 */
function _post_json(url,data, success, fail ) {
     console.log( "----_post--start-------" );
    wx.request( {
        url: url,
        // header: {
        //     'content-type': 'application/json',
        // },
        method:'POST',
        data:data,
        success: function( res ) {
            success( res );
        },
        fail: function( res ) {
            fail( res );
        }
    });
    
    console.log( "----end----_post-----" );
}
module.exports = {
    _get: _get,
    _post:_post,
    _post_json:_post_json
}
