#!/bin/bash

PREFIX="dev"
BACKUP_DT=$(date +'%Y-%m-%d_%H-%M')
BACKUP_DIR="/var/www/zagorodnaz/dev.zagorodna.com/dev_app/storage/logs/"
LARAVEL_LOG="laravel.log"


echo "Laravel log rotate has started at:"
date

cd ${BACKUP_DIR}
pwd
ls -alh

tar cpjvf "${BACKUP_DIR}_${PREFIX}_${LARAVEL_LOG}_${BACKUP_DT}.tar.bz2" "${BACKUP_DIR}${LARAVEL_LOG}"
echo '' > "${BACKUP_DIR}${LARAVEL_LOG}"

pwd
ls -alh

echo "Laravel log rotate has finished at:"
date
