subDir="$(date +%F)"
mkdir -p "/root/coral/mongo-backup/$subDir"
docker-compose -f /root/coral/docker-compose.yml exec mongo mongodump --out /mongo-backup/$subDir >> backup.log
