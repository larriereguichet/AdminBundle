.PHONY: assets assets.production

assets:
	yarn run encore dev

assets.watch:
	yarn run encore dev --watch

assets.production:
	yarn run encore production
