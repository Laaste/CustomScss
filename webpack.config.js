const path = require('path');
const glob = require('glob');

const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const FixStyleOnlyEntriesPlugin = require('webpack-fix-style-only-entries');

module.exports = (env) =>
{
	const watch = (env.watch == 'true') ? true : false;

	// Use glob pattern to include nested SCSS files
	const templateFiles = glob.sync(path.resolve(__dirname, './scss/theme/**/*.scss').replace(/\\/g, '/'));

	const templateEntries = templateFiles.reduce((entries, file) =>
	{
		// Create relative path for nested files
		const relativePath = path.relative(path.resolve(__dirname, './scss'), file);
		const name = relativePath.replace(/\.scss$/, '');
		entries[`css/${name}`] = file.replace(/\\/g, '/');

		return entries;
	}, {});

	return {
		mode: env.mode,
		resolve:
		{
			alias: {
				'@rootScss': path.resolve(__dirname, './scss'),
			},
			extensions: [
				'.js',
				'.css',
				'.scss'
			]
		},
		entry: {
			// 'js/main-compiled': path.resolve(__dirname, './js/main.js'),
			'css/styles': path.resolve(__dirname, './scss/styles.scss'),
			...templateEntries
		},
		output: {
			path: path.resolve(__dirname, './'),
		},
		plugins: [
			new FixStyleOnlyEntriesPlugin(),
			new MiniCssExtractPlugin({
				filename: '[name].css'
			})
		],
		watch: watch,
		module: {
			rules: [
				{
					test: /\.(scss|css)$/,
					use: [
						MiniCssExtractPlugin.loader,
						{
							loader: 'css-loader',
							options: {
								url: false
							}
						},
						'sass-loader'
					]
				}
			]
		}
	}
}
