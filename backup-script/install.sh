#!/bin/bash

export CLICKHOUSE_HOST={$CH_HOST}
export REMOTE_STORAGE='s3'
export BACKUPS_TO_KEEP_REMOTE=3
export S3_ACCESS_KEY='AKIA2H6TKJHILI6YZPGO'
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

wget https://github.com/AlexAkulov/clickhouse-backup/releases/download/v0.6.3/clickhouse-backup.tar.gz
tar -xf clickhouse-backup.tar.gz 
cd clickhouse-backup/ 
sudo cp clickhouse-backup /usr/local/bin/
clickhouse-backup -v

# restore 
# clickhouse-backup download 2020-07-06T20-13-02
# clickhouse-backup restore 2020-07-06T20-13-02

# backup process
clickhouse-backup tables
clickhouse-backup create
clickhouse-backup list
clickhouse-backup upload 