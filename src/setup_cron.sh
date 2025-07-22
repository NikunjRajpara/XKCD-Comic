#!/bin/bash
# This script sets up a CRON job to run cron.php every 24 hours

cd "$(dirname "$0")"
PHP_PATH=$(which php)
CRON_FILE="cron.php"
FULL_PATH="$(pwd)/$CRON_FILE"
CRON_JOB="0 0 * * * $PHP_PATH $FULL_PATH"

# Remove existing job for this script, then add new one
(crontab -l 2>/dev/null | grep -v "$FULL_PATH"; echo "$CRON_JOB") | crontab -
echo "CRON job set: XKCD mailer will run every 24 hours."
