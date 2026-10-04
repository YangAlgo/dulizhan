#!/usr/bin/env bash
# Build dist/bufan.zip, the file you upload in WordPress (Appearance → Themes → Add New → Upload).
#
# When WP-CLI is available the Chinese translation files are recompiled from
# bufan/languages/zh_CN.po first. Point WP at your WP-CLI command if it is not "wp",
# e.g.  WP="php wp-cli.phar --allow-root" tools/build.sh
set -euo pipefail
cd "$(dirname "$0")/.."

WP="${WP:-wp}"
if $WP --version >/dev/null 2>&1; then
	$WP i18n make-pot bufan bufan/languages/bufan.pot --domain=bufan --exclude=assets
	$WP i18n update-po bufan/languages/bufan.pot bufan/languages/zh_CN.po
	$WP i18n make-mo bufan/languages/zh_CN.po bufan/languages
	$WP i18n make-php bufan/languages/zh_CN.po bufan/languages
else
	echo "WP-CLI not found: using the translation files already in bufan/languages."
fi

mkdir -p dist
rm -f dist/bufan.zip
zip -rq dist/bufan.zip bufan -x '*.DS_Store'
echo "Built dist/bufan.zip ($(du -h dist/bufan.zip | cut -f1))"
