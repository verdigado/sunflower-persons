publish:
	@echo "Latest tag was: "
	@git describe --tags --abbrev=0
	@read -p "which version do you want to publish now (start with number, NO v): " newversion; \
	sed -i  "0,/Version:.*/s//Version: $$newversion/" "sunflower-persons.php" && \
	php create-changelog.php $$newversion && \
	git checkout -B deploy && \
	git add sunflower-persons.php changelog.html && git commit -m "publishing version $$newversion" && \
	git push --set-upstream origin deploy
