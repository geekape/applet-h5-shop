var app = getApp()
Page({
    data: {
        upload_limit: 9,// 默认最多上传9张
        img_srcs: [], //如果是编辑状态，只需要把原信息的图片地址放到此处就可以显示出来
        img_ids: []
    },
    //上传图片
    chooseImg: function () {
        var that = this;

        wx.chooseImage({
            count: that.data.upload_limit,
            sizeType: ['original', 'compressed'], // 可以指定是原图还是压缩图，默认二者都有
            sourceType: ['album', 'camera'], // 可以指定来源是相册还是相机，默认二者都有
            success: function (res) {
                // 返回选定照片的本地文件路径列表，tempFilePath可以作为img标签的src属性显示图片
                var tempFilePaths = res.tempFilePaths

                var nowImg = that.data.img_srcs
                for (let i = 0; i < tempFilePaths.length; i++) {
                    var imgUrl = tempFilePaths[i]
                    nowImg.push(imgUrl)
                    if (nowImg.length > that.data.upload_limit) {
                        nowImg.shift()
                    }
                }

                that.setData({
                    'img_srcs': nowImg
                })
                that.sendPhotos(tempFilePaths)
            }
        })

    },
    //发图片发送给后端服务器
    sendPhotos: function (tempFilePaths) {
        var that = this
        if (tempFilePaths.length != 0) {
            wx.uploadFile({
                url: app.url + 'weiapp/Api/upload&PHPSESSID=' + wx.getStorageSync('PHPSESSID'),
                filePath: tempFilePaths[0],
                name: 'download',
                header: { "Content-Type": "multipart/form-data" },
                success: function (res) {
                    var data = JSON.parse(res.data)
                    var imgs = that.data.img_ids
                    imgs.push(data.id)
                    that.setData({
                        'img_ids': imgs
                    })
                    tempFilePaths.splice(0, 1)
                    that.sendPhotos(tempFilePaths)
                },
                fail: function (res) {
                    console.log('上传图片到服务器失败')
                },
                complete: function (res) {
                    console.log(res)
                }
            })
        }
    },
    //图片预览
    previewImage: function (e) {
        var img_srcs = this.data.img_srcs
        var index = e.target.dataset.index
        wx.previewImage({
            current: img_srcs[index],
            urls: img_srcs// 需要预览的图片http链接列表
        })
    },
    //删除图片
    delImg(e) {
        var index = e.target.dataset.index
        var img_srcs = this.data.img_srcs
        var img_ids = this.data.img_ids
        var that = this
        wx.showModal({
            title: '提示',
            content: '确定要删除？',
            success: function (res) {
                if (res.confirm) {
                    img_srcs.splice(index, 1)
                    img_ids.splice(index, 1)
                    that.setData({
                        'img_srcs': img_srcs,
                        'img_ids': img_ids
                    })
                }
            }
        })
    }
})