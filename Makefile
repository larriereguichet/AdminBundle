all: install


install:
	composer install
	make install-assets


update:
	composer update
	make install-assets


install-assets:
	make install-assets-css
	make install-assets-js
	make install-assets-fonts


install-assets-css:
	### copy bootstrap css ###
	mkdir -p Resources/public/css/bootstrap
	cp vendor/twbs/bootstrap/dist/css/*.min.css Resources/public/css/bootstrap/

	### copy font-awesome css ###
	mkdir -p Resources/public/css/font-awesome
	cp vendor/components/font-awesome/css/*.min.css Resources/public/css/font-awesome/

	### build css with compass  ###
	compass compile


install-assets-js:
	### copy bootstrap js ###
	mkdir -p Resources/public/js/bootstrap
	cp vendor/twbs/bootstrap/dist/js/*.min.js Resources/public/js/bootstrap/

	### copy jquery js ###
	mkdir -p Resources/public/js/jquery
	cp vendor/components/jquery/jquery.min.js Resources/public/js/jquery/
	cp vendor/components/jqueryui/jquery-ui.min.js Resources/public/js/jquery/


install-assets-fonts:
	### copy font-awesome fonts ###
	mkdir -p Resources/public/css/fonts/
	cp vendor/components/font-awesome/fonts/* Resources/public/css/fonts/


# for develoment or tests purpose only
test-create-web-directory:
	rm -rf web/bundles/lagadmin/css/
	mkdir -p web/bundles/lagadmin/css/
	mkdir -p web/bundles/lagadmin/js/
	cp -r Resources/public/css/* web/bundles/lagadmin/css/
	cp -r Resources/public/js/* web/bundles/lagadmin/js/
