# make cuke
#   OR
# make cuke FEATURE= features/happypath/c-media-grid-youtube_editor.feature
cuke:
	bundle exec cucumber -c $${FEATURE} \
		| grep -v JQMIGRATE
