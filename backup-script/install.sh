#!/bin/bash

export CLICKHOUSE_HOST=clickhouse-rwj4sm
export REMOTE_STORAGE='s3'
export BACKUPS_TO_KEEP_REMOTE=1
export S3_ACCESS_KEY='AKIA2H6TKJHIJRUKBDE7'
export S3_SECRET_KEY='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'
export S3_BUCKET='bnsclickhousebucket'
export S3_ENDPOINT='http://bnsclickhousebucket.s3.eu-west-1.amazonaws.com/'
export S3_REGION="eu-west-1"
export S3_ACL="private"
export S3_ASSUME_ROLE_ARN
export S3_FORCE_PATH_STYLE='true'
export S3_PATH='backup'
export S3_DISABLE_SSL='true'
export S3_DEBUG='true'

wget https://github.com/AlexAkulov/clickhouse-backup/releases/download/v0.6.3/clickhouse-backup.tar.gz --no-check-certificate
tar -xf clickhouse-backup.tar.gz 
cd clickhouse-backup/ 

clickhouse-backup -v

# restore 
clickhouse-backup download
clickhouse-backup restore 

# backup process
clickhouse-backup tables
clickhouse-backup create $BACKUP_NAME
clickhouse-backup list
clickhouse-backup upload 