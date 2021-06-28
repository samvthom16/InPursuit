const webpack = require('webpack');

const config = {
	entry: {
		app: "./dist/js/app.js",
	},
	output: {
		path: __dirname + "/dist/js/",
		filename: "app-final.js"
	},
	plugins: [
	],
  module: {

	}
};


module.exports = config;
