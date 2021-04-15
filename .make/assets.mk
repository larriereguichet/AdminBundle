.PHONY: assets assets.production assets.build assets.watch assets.install

assets: assets.install assets.build

assets.build:
	yarn run encore dev

assets.watch:
	yarn run encore dev --watch

assets.production:
	yarn run encore production

assets.install:
	yarn install
