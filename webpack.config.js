const webpack = require('webpack');

const config = {
	entry: {
		app: "./dist/js/app.js",
	},
	output: {
		path: __dirname + "/dist/js/final/",
		filename: "app-final.js"
	},
	plugins: [

	],
  module: {
		rules: [
		]
	}
};


module.exports = config;
