const gulp = require("gulp"),
	clean = require("gulp-clean"),
	del = require("del"),
	copy = require("gulp-copy"),
	rename = require("gulp-rename"),
	debug = require("gulp-debug"),
	replace = require("gulp-replace"),
	zip = require("gulp-zip"),
	packageJSON = require("./package.json"),
	exec = require("child_process").exec;

const plugin = {
	name: "Pix por Piggly",
	slug: "pix-por-piggly",
	files: [
		"**",
		// Exclude all below
		"!src/**/_*.php",
		"!**/*.map",
		"!LICENSE",
		"!**/build/**",
		"!**/build",
		"!**/dist/**",
		"!**/dist",
		"!**/docs/**",
		"!**/docs",
		"!**/dev-assets/**",
		"!**/dev-assets",
		"!**/src-assets/**",
		"!**/src-assets",
		"!**/vendor/**",
		"!**/vendor",
		"!**/.github/**",
		"!**/.github",
		"!**/bin/**",
		"!**/bin",
		"!**/tests/**",
		"!**/tests",
		"!**/Tests/**",
		"!**/Tests",
		"!**/test/**",
		"!**/test",
		"!**/Test/**",
		"!**/Test",
		"!**/example/**",
		"!**/example",
		"!**/examples/**",
		"!**/examples",
		"!**/sample/**",
		"!**/sample",
		"!**/samples/**",
		"!**/samples",
		"!**/doc/**",
		"!**/doc",
		"!**/docs/**",
		"!**/docs",
		"!**/older/**",
		"!**/older",
		"!**/logs/**",
		"!**/logs",
		"!php-scoper/**",
		"!php-scoper",
		"!**/node_modules/**",
		"!**/node_modules",
		"!**/*.map",
		"!**/*LICENSE.txt",
		"!**/*.gitignore",
		"!**/*.md",
		"!**/*.sh",
		"!**/*.rst",
		"!**/*.xml",
		"!**/*.yml",
		"!**/*.dist",
		"!**/*.json",
		"!**/*.lock",
		"!**/gulpfile.js",
		"!**/.jshint.js",
		"!**/.eslintrc.js",
		"!**/.eslintignore.js",
		"!**/AUTHORS",
		"!**/Copying",
		"!**/CHANGELOG",
		"!**/CONTRIBUTING",
		"!**/Dockerfile",
		"!**/Makefile",
		"!.packages/**",
		"!.packages/",
		"!.env.example",
		"!.env",
		"!vendor/instituteweb/**",
		"!vendor/instituteweb/",
		// Prefixed packages
		"!vendor/chillerlan/**",
		"!vendor/chillerlan/",
		"!vendor/monolog/**",
		"!vendor/monolog/",
		"!vendor/piggly/**",
		"!vendor/piggly/",
		"!vendor/psr/**",
		"!vendor/psr/",
		// Common files
		"vendor/composer/**",
		"vendor/autoload.php",
		"./assets/vendor/**",
		"composer.json",
		"LICENSE",
		"readme.txt",
	],
	php: ["**/*.php", "!vendor/**", "!vendors/**", "!libs/**", "!tests/**"],
};

// Composer commands

// Build
gulp.task("composer", function (cb) {
	exec("composer build", function (err, stdout, stderr) {
		console.log(stdout);
		console.log(stderr);
		cb(err);
	});
});

// Delete lock file and vendor folder
gulp.task("composer:delete_lock_and_vendor", function () {
	return gulp
		.src(["composer.lock", "vendor"], { allowEmpty: true, read: false })
		.pipe(clean());
});

// Delete prefixed libraries
gulp.task("composer:delete_prefixed_libraries", function () {
	return gulp
		.src(
			[
				"vendor/chillerlan/php-qrcode",
				"vendor/chillerlan/php-settings-container",
				"vendor/monolog/monolog",
				"vendor/piggly/php-pix",
				"vendor/piggly/wordpress-starter-kit",
				"vendor/psr/log",
			],
			{ allowEmpty: true, read: false }
		)
		.pipe(clean());
});

// Create prefixed folder
gulp.task("composer:create_prefixed_folder", function () {
	return gulp.src("*.*", { read: false }).pipe(gulp.dest("./libs"));
});

// Prefix dependencies
gulp.task("composer:prefix", function (cb) {
	exec("composer prefix-dependencies", function (err, stdout, stderr) {
		console.log(stdout);
		console.log(stderr);
		cb(err);
	});
});

// Builder

// Delete build folder
gulp.task("build:fresh", function () {
	return del(["build/**", "!build"], { force: true });
});

// Create assets
gulp.task("build:assets:create", function (cb) {
	exec(
		"cd src-assets && npm install && npm run js:build && npm run css:deploy",
		function (err, stdout, stderr) {
			console.log(stdout);
			console.log(stderr);
			cb(err);
		}
	);
});

// Copy files to build folder
gulp.task("build:copy", function () {
	var files = plugin.files;

	return gulp.src(files).pipe(copy("./build"));
});

// Build composer
gulp.task("build:composer", function (cb) {
	exec("composer build", function (err, stdout, stderr) {
		console.log(stdout);
		console.log(stderr);
		cb(err);
	});
});

// Build zip
gulp.task("build:zip", function () {
	return gulp
		.src("./build/**")
		.pipe(
			rename(function (file) {
				file.dirname = plugin.slug + "/" + file.dirname;
			})
		)
		.pipe(zip(plugin.slug + "-" + packageJSON.version + ".zip"))
		.pipe(gulp.dest("./dist"))
		.pipe(debug({ title: "[zip]" }));
});

// Global
gulp.task(
	"build",
	gulp.series(
		"build:composer",
		"build:fresh",
		"build:assets:create",
		"build:copy",
		"build:zip"
	)
);

gulp.task(
	"deploy",
	gulp.series(
		"build:composer",
		"build:fresh",
		"build:assets:create",
		"build:copy",
		"build:zip",
		"build:fresh"
	)
);
