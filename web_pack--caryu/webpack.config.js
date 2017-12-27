const NODE_DEV = false

var rd = require('rd');
var webpack = require('webpack');
var resolve = require('path').resolve;
var htmlWebpackPlugin = require('html-webpack-plugin');
var publicPath = NODE_DEV ? './dist' : '/Public/static/packaged-assets/Caryu/';
var assetsPath = NODE_DEV ? resolve(publicPath) : resolve('..' + publicPath); //打包后的静态资源存放路径(JS,CSS,IMG...)
var htmlPath = NODE_DEV ? resolve(publicPath) : resolve('../App/Caryu/View'); //打包后的HTML存放路径
var ExtractTextPlugin = require("extract-text-webpack-plugin");
var CleanPlugin = require('clean-webpack-plugin')                  //webpack插件，用于清除目录文件
var CommonsChunkPlugin = webpack.optimize.CommonsChunkPlugin;      //对指定的chunks进行公共模块的提取 多个html共用一个js文件(chunk)，可用CommonsChunkPlugin

var entry = { main: __dirname + '/src/pages/main.js' }; //主入口
var entryKeys = []
var plugins = [
  new CleanPlugin( //每次编译 自动删除之前编译完的旧文件
    ['./Public/static/packaged-assets/Caryu']
    , {
      root: resolve(__dirname, '../'),       　 //基于此目录查找
      verbose: true,        　　　　　　　　　　  //是否开启在控制台输出信息
      watch: true,                              //默认false 为true时删除所有的编译文件
      // exclude: []
    }
  ),
  new CommonsChunkPlugin({//提取JS中公共模块
    name: ['main', ...entryKeys],                      //or   names: Array 对应entry上的键值
    filename: './js/vendor.[ChunkHash:8].js',          //生成文件的名字，如果没有默认为输出文件名
    minChunks: Infinity,                               //模块被引用的次数多少才会被独立打包>=2
    // chunks:                                         //表示需要在哪些chunk（也可以理解为webpack配置中entry的每一项）里寻找公共代码进行打包。不设置此参数则默认提取范围为所有的chunk
  }),
  new ExtractTextPlugin("css/[name]-[ContentHash:8].css"), //页面中提取的css名字
  new webpack.LoaderOptionsPlugin({
    options: {
      postcss: [//自动添加CSS属性前缀
        require("autoprefixer")({
          browsers: ['last 10 Chrome versions', 'last 5 Firefox versions', 'Safari >= 6', 'ie > 8']
        })
      ]
    }
  })
];

//枚举目录下的所有文件夹
rd.eachDirFilterSync('./src/pages', /(pages)(\\)(\b[a-z]+\b)(\\)(\b[a-z]+\b)/i, f => {
  let s = '\\';
  f = f.split(s).join(s + s);
  rd.eachFilterSync(f, /\.html$/i, files => {//枚举对应文件夹下的文件
    let temp = files.split(s).slice(-3);
    let filename = [...temp.slice(0, 1), ...temp.slice(2, 3)].join(s);
    let entryKey = filename.replace('.html', '');
    let template = resolve('./src/pages/' + entryKey + s + temp[2]);

    entry[entryKey] = template.replace('.html', '.js');//批量添加入口文件
    temp = new htmlWebpackPlugin({
      filename: htmlPath + s + filename,//编译后的html路径
      template,//模板html路径
      inject: 'head',
      chunks: ['main', entryKey]
    });
    entryKeys.push(entryKey)
    plugins.push(temp);//枚举到的页面加入打包队列
  });

}, err => {
  assert.equal(err, null);
});

module.exports = {
  entry: entry,
  devServer: {
    //其实很简单的，只要配置这个参数就可以了
    proxy: {
      '/index.php/*': {
        target: 'http://test.caryu.com/index.php',
        secure: false
      }
    }
  },
  output: {
    path: assetsPath,
    filename: 'js/[name]-[ChunkHash].js',
    publicPath: publicPath,
  },
  plugins: plugins,
  resolve: {
    extensions: ['.js', '.json', '.styl'],
    alias: {
      '@': resolve('src'),
      '~': resolve('src/components'),
      'vue': 'vue/dist/vue.js'
    }
  },
  module: {
    rules: [{
      test: /\.html$/,
      use: [
        //   'extract-loader?publicPath=' + publicPath,
        'html-loader?' + JSON.stringify({
          attrs: ['img:src', 'img:data-on', 'img:data-off', 'link:href']
        }),
      ]
    }, {
      test: /\.css$/,
      use: ExtractTextPlugin.extract({
        fallback: "style-loader",
        use: "css-loader?importLoaders=1!postcss-loader"
      })
    }, {
      test: /\.styl$/,
      use: ExtractTextPlugin.extract({
        fallback: "style-loader",
        use: "css-loader!postcss-loader!stylus-loader"
      })
    }, {
      test: /\.js$/,
      loader: 'babel-loader',//ES6转ES5
      include: resolve(__dirname, 'src'), //包含的检测目录
      exclude: resolve(__dirname, 'node_modules'), //排除的检测目录
      options: {
        presets: ['latest']
      }
    }, {
      test: /\.(png|jpg|gif|webp|svg)$/i,
      use: [
        {
          loader: 'url-loader',//limit小于指定值会转base64
          options: {
            limit: 1000,
            // name: 'img/[name]-[hash:8].[ext]'
            name: 'img/[name].[ext]'
          }
        }
      ]
    }, {
      test: /\.(woff2?|eot|ttf|otf)(\?.*)?$/i,
      use: [
        {
          loader: 'url-loader',//limit小于指定值会转base64
          options: {
            limit: 10000,
            name: 'fonts/[name].[ext]'
          }
        }
      ]
    }]
  }
}
