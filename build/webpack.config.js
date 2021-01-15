import path from 'path';
import webpack from 'webpack';
import MiniCssExtractPlugin from 'mini-css-extract-plugin';
import TerserPlugin from 'terser-webpack-plugin'; // Used instead of Uglify JS.
import OptimizeCSSAssetsPlugin from 'optimize-css-assets-webpack-plugin';
import { CleanWebpackPlugin } from 'clean-webpack-plugin';
import DashboardPlugin from 'webpack-dashboard/plugin';
import autoprefixer from 'autoprefixer';

// Fixes any issues related to Babel's working directory.
import babelConfig from './.babelrc.json';

// Detecting Dev Mode
const devMode = process.env.NODE_ENV !== 'production';

// Project root.
const rootDir = path.resolve( __dirname + '/..' );

// The build tools.
const buildToolsDir = rootDir + '/build';

// The compiled distribution assets.
const distributionDir = rootDir + '/plugins/post-type-csv-exporter/source/';

// The assets source code.
const assetsSrcDir = rootDir + '/assets-src';

// The node module dir.
const nodeModulesDir = buildToolsDir + '/node_modules';

// Extract text from a bundle, or bundles, into a separate file
const cssPlugin = new MiniCssExtractPlugin( {
	filename: devMode ? './css/[name].css' : './css/[name].min.css'
} );

// Keeping it clean and fresh.
const cleanPlugin = new CleanWebpackPlugin( {
	root: rootDir,
	cleanOnceBeforeBuildPatterns: [ "js", "css" ]
} );

// Minify JS.
const terserPlugin = new TerserPlugin(
	{
		sourceMap: true,
		terserOptions: {
			warnings: false,
			output: {
				comments: false
			},
			ie8: false,
			safari10: false,
		}
	}
);

// Minify CSS.
const optimizeCssPlugin = new OptimizeCSSAssetsPlugin( {
	cssProcessorPluginOptions: {
		preset: [ 'default', {
			discardComments: {
				removeAll: true
			}
		} ]
	}
} );

// Added Jquery declaration into Webpack
const jquery = new webpack.ProvidePlugin(
	{
		$: 'jquery',
		jQuery: 'jquery',
		'window.jQuery': 'jquery',
	}
);

// Dashboard Plugin.
const dashboardPlugin = new DashboardPlugin();

export default {
	mode: devMode ? 'development' : 'production',
	context: rootDir,
	resolve: {
		modules: [ nodeModulesDir, 'node_modules' ],
	},
	resolveLoader: {
		modules: [ nodeModulesDir, 'node_modules' ],
	},
	devtool: 'source-map',
	entry: {
		'post-type-csv-exporter': [
			'./assets-src/js/index.js',
			'./assets-src/sass/styles.scss',
		],
	},
	output: {
		path: distributionDir,
		filename: devMode ? './js/[name].js' : './js/[name].min.js'
	},
	module: {
		rules: [ {
			test: /\.(js)$/,
			exclude: /node_modules/,
			loader: 'babel-loader',
			options: babelConfig
			// query: {
			// 	presets: [ require.resolve('@babel/preset-env' ) ]
			// }
		}, {
			test: /\.(sa|sc|c)ss$/,
			use: [ MiniCssExtractPlugin.loader, {
				loader: 'css-loader'
			}, {
				loader: 'postcss-loader',
				options: {
					plugins: [
						autoprefixer()
					],
					sourceMap: true
				}
			}, {
				loader: 'sass-loader',
				options: {
					sourceMap: true,
				},
			} ]
		} ]
	},
	externals: {
		jquery: "jQuery",
	},
	optimization: {
		minimizer: [ terserPlugin, optimizeCssPlugin ]
	},
	plugins: [
		cleanPlugin,
		cssPlugin,
		jquery,
		...( devMode ? [ dashboardPlugin ] : [] ),
	]
}
