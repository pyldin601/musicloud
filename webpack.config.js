const path = require('path');
const ExtractTextPlugin = require('extract-text-webpack-plugin');

const extractLess = new ExtractTextPlugin({
    filename: "[name].css"
});

module.exports = {
    target: 'web',
    entry: {
        'library': path.join(__dirname, 'public/css/src/library.less'),
        'lobby': path.join(__dirname, 'public/css/src/lobby.less'),
        'common': path.join(__dirname, 'public/css/src/common.less')
    },
    output: {
        path: path.join(__dirname, 'public/css'),
        filename: '[name].css'
    },
    module: {
        rules: [{
            test: /\.less$/,
            use: extractLess.extract({
                use: [{
                    loader: "css-loader"
                }, {
                    loader: "less-loader"
                }],
                fallback: "style-loader"
            })
        }]
    },
    plugins: [
        extractLess
    ],
    resolve: {
        extensions: ['.less']
    }
};
