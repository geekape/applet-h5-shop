let path = require('path')

let config = {
    '/yi': {
        target: 'https://leyao.tv',
        ws: true,
        changeOrigin: true
    }
}

function resolve(dir) {
    return path.join(__dirname, dir)
}
module.exports = {
    baseUrl: process.env.NODE_ENV == "development" ? '/' : './',
    outputDir: '../../public/wap',
	productionSourceMap: false,	// 不生成map文件
    chainWebpack: config => {
        config.resolve.alias.set('images', resolve('static/img/'))
        config.resolve.alias.set('styles', resolve('static/styles/'))
    },
	

    css: {
        loaderOptions: {
            sass: {
                // @/ is an alias to src/
                // so this assumes you have a file named `src/variables.scss`
                data: `@import "@/../static/styles/base.scss";`
            }

        }
    },
    devServer: {
        proxy: process.env.NODE_ENV == "development" ? config : {}
    }
}