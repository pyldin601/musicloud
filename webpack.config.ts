/*
 * Copyright (c) 2017 Roman Lakhtadyr
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

import path = require('path')
import MiniCssExtractPlugin = require('mini-css-extract-plugin')
import webpack = require('webpack')

export default (env: Record<string, string> = {}): webpack.Configuration => {
  return {
    entry: {
      app: path.join(__dirname, 'src/scripts/musicloud/index.js'),
    },
    output: {
      path: path.join(__dirname, 'public/assets'),
      filename: '[name].js',
      publicPath: '/assets/',
    },
    optimization: {
      minimize: !!env.production,
    },
    resolve: {
      extensions: ['.js', '.ts'],
    },
    module: {
      strictExportPresence: true,
      rules: [
        {
          oneOf: [
            {
              test: [/\.js$/, /\.ts$/],
              loader: 'ts-loader',
              exclude: /node_modules/,
            },
            {
              test: [/\.jpe?g$/, /\.png$/, /\.html/],
              loader: 'url-loader',
              options: {
                limit: false,
                name: '[name].[contentHash].[ext]',
              },
            },
            {
              test: [/\.less$/, /\.css$/],
              use: [MiniCssExtractPlugin.loader, 'css-loader', 'less-loader'],
            },
          ],
        },
      ],
    },
    plugins: [new MiniCssExtractPlugin()],
    externals: {
      jquery: '$',
      angular: 'angular',
    },
  }
}
