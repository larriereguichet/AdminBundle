.PHONY: assets assets.production assets.build assets.watch assets.install

assets: assets.install assets.production

assets.dev:
	$(JS) yarn run encore dev

assets.watch:
	$(JS) yarn run encore dev --watch

assets.production:
	$(JS) yarn run encore production

assets.install:
	$(JS) yarn install
